<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Compra;
use App\Models\PuntoEca;
use App\Models\Inventario;
use App\Models\CentroAcopio;
use App\Models\Venta;
use GuzzleHttp\Psr7\Query;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;

class MovimientosController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    /**
     * Muestra la vista de movimientos con los últimos movimientos de compra y venta
     * 
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // Mantengo index como alias a data() para compatibilidad (responde JSON)
        $payload = $this->data($request);
        return response()->json($payload + ['seccion' => 'movimientos']);
    }

    /**
     * Devuelve los últimos movimientos (compras + ventas) combinados (máx 10) y listas de centros filtrables.
     * No renderiza vista: usado por PuntoEcaController como proveedor de datos.
     *
     * @return array
     */
    public function data(Request $request): array
    {
        $punto = DB::table('puntos_eca')->select('id', 'gestor_id')->where('gestor_id', Auth::id())->first();
        $puntoEcaId = $punto->id;

        // Últimas 10 compras
        $compras = Compra::query()
            ->with(['inventario.material:id,nombre'])
            ->whereHas('inventario', fn($q2) => $q2->where('punto_eca_id', $puntoEcaId))
            ->latest('fecha')
            ->take(10)
            ->get()
            ->map(function ($c) {
                return [
                    'id' => $c->id,
                    'tipo' => 'compra',
                    'fecha' => $c->fecha,
                    'material' => $c->inventario->material->nombre ?? '—',
                    'cantidad' => $c->cantidad,
                    'unidad' => $c->inventario->unidad_medida ?? '',
                    'precio_unit' => $c->precio_compra,
                    'observ' => $c->observaciones
                ];
            });

        // Últimas 10 ventas
        $ventas = Venta::query()
            ->with(['inventario.material:id,nombre'])
            ->whereHas('inventario', fn($q2) => $q2->where('punto_eca_id', $puntoEcaId))
            ->latest('fecha')
            ->take(10)
            ->get()
            ->map(function ($v) {
                return [
                    'id' => $v->id,
                    'tipo' => 'venta',
                    'fecha' => $v->fecha,
                    'material' => $v->inventario->material->nombre ?? '—',
                    'cantidad' => $v->cantidad,
                    'unidad' => $v->inventario->unidad_medida ?? '',
                    'precio_unit' => $v->precio_venta,
                    'observ' => $v->observaciones
                ];
            });

        // Filtros nombre, tipo, localidad, estado, material
        $f2 = $request->validate([
            'f_nombre' => ['nullable', 'string', 'max:150'],
            'f_tipo' => ['nullable', Rule::in(['Planta', 'Proveedor', 'Otro'])],
            'f_localidad' => ['nullable', 'string', 'max:60'],
            'f_material' => ['nullable', 'uuid', 'exists:materiales,id'],
        ]);

        //filtros para los centros
        //Esta vez se construye la consulta con los filtros para aplicarlo a la consulta de los centros tanto globales como propios
        $applyFilters = function ($q) use ($f2) {
            return $q
                ->when($f2['f_nombre'] ?? null, fn($qq, $v) => $qq->where('nombre', 'like', "%{$v}%"))
                ->when($f2['f_tipo'] ?? null, fn($qq, $v) => $qq->where('tipo', $v))
                ->when($f2['f_localidad'] ?? null, fn($qq, $v) => $qq->where('localidad', 'like', "%{$v}%"))
                ->when($f2['f_estado'] ?? null, fn($qq, $v) => $qq->where('estado', $v))
                ->when($f2['f_material'] ?? null, fn($qq, $v) => $qq->where('materiales_centro_acc', $v));
        };

        // Combinar y ordenar movimientos
        $ultimosMovimientos = $compras->concat($ventas)
            ->sortByDesc('fecha')
            ->take(10)
            ->values();

        $centrosGlobalesLista = CentroAcopio::query()
            ->select(['id', 'nombre'])
            ->where('alcance', 'global')
            // con tap() le decimos que ejecute la consulta que le pasamos
            ->tap($applyFilters)
            ->orderBy('nombre')
            ->get();

        $centrosPropiosLista = CentroAcopio::query()
            ->select(['id', 'nombre'])
            ->where('alcance', 'eca')
            ->where('owner_punto_eca_id', $puntoEcaId)
            ->tap($applyFilters)
            ->orderBy('nombre')
            ->get();

        return [
            'ultimosMovimientos' => $ultimosMovimientos,
            'centrosGlobalesLista' => $centrosGlobalesLista,
            'centrosPropiosLista' => $centrosPropiosLista,
        ];
    }

    /**
     * Historial completo paginado de compras y ventas (para tab historial si se requiere separar luego).
     * De momento retorna arrays vacíos; se implementará en fase posterior si se externaliza del PuntoEcaController.
     */
    public function dataHistorial(Request $request): array
    {
        $punto = DB::table('puntos_eca')->select('id', 'gestor_id')->where('gestor_id', Auth::id())->first();
        $puntoEcaId = $punto->id;

        // Materiales del punto para selects
        $materialesPunto = Inventario::query()
            ->where('punto_eca_id', $puntoEcaId)
            ->with('material:id,nombre')
            ->get()
            ->pluck('material')
            ->unique('id')
            ->values();

        // Filtros compras
        $fc = $request->validate([
            'hc_desde' => ['nullable', 'date'],
            'hc_hasta' => ['nullable', 'date', 'after_or_equal:hc_desde'],
            'hc_material' => ['nullable', 'uuid', 'exists:materiales,id'],
        ]);
        // Filtros ventas
        $fv = $request->validate([
            'hs_desde' => ['nullable', 'date'],
            'hs_hasta' => ['nullable', 'date', 'after_or_equal:hs_desde'],
            'hs_material' => ['nullable', 'uuid', 'exists:materiales,id'],
        ]);

        $comprasQ = Compra::query()
            ->with(['inventario.material:id,nombre'])
            ->whereHas('inventario', fn($q) => $q->where('punto_eca_id', $puntoEcaId))
            ->when($fc['hc_desde'] ?? null, fn($q, $v) => $q->whereDate('fecha', '>=', $v))
            ->when($fc['hc_hasta'] ?? null, fn($q, $v) => $q->whereDate('fecha', '<=', $v))
            ->when($fc['hc_material'] ?? null, fn($q, $v) => $q->whereHas('inventario', fn($qq) => $qq->where('material_id', $v)))
            ->orderByDesc('fecha');

        $ventasQ = Venta::query()
            ->with(['inventario.material:id,nombre'])
            ->whereHas('inventario', fn($q) => $q->where('punto_eca_id', $puntoEcaId))
            ->when($fv['hs_desde'] ?? null, fn($q, $v) => $q->whereDate('fecha', '>=', $v))
            ->when($fv['hs_hasta'] ?? null, fn($q, $v) => $q->whereDate('fecha', '<=', $v))
            ->when($fv['hs_material'] ?? null, fn($q, $v) => $q->whereHas('inventario', fn($qq) => $qq->where('material_id', $v)))
            ->orderByDesc('fecha');

        $histCompras = $comprasQ->paginate(10, ['*'], 'page_c')->withQueryString();
        $histVentas = $ventasQ->paginate(10, ['*'], 'page_v')->withQueryString();

        return [
            'histCompras' => $histCompras,
            'histVentas' => $histVentas,
            'materialesPunto' => $materialesPunto,
        ];
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function storeCompra(Request $request)
    {
        $userId = Auth::id();

        // 1) Validación con namespacing "compra.*"
        $data = $request->validate([
            'compra.inventario_id' => ['required', 'uuid', 'exists:inventario,id'],
            'compra.cantidad' => ['required', 'numeric', 'gt:0'],
            'compra.fecha' => ['required', 'date'],
            'compra.observaciones' => ['nullable', 'string', 'max:500'],
        ]);

        // Normalizar cantidad a 2 decimales
        $data['compra']['cantidad'] = round((float)$data['compra']['cantidad'], 2);
        $precio = DB::table('inventario')->where('id', '=', $data['compra']['inventario_id'])->value('precio_compra');
        $precioCompra = round($data['compra']['cantidad'] * (float)$precio, 2);
        $data['compra']['precio_compra'] = $precioCompra;

        $compra = $data['compra'];

        // 2) Verificar pertenencia del inventario al Punto del gestor autenticado
        $inv = Inventario::with('puntoEca:id,gestor_id')->findOrFail($compra['inventario_id']);
        if ($inv->puntoEca?->gestor_id !== $userId) {
            abort(403, 'No autorizado.');
        }

        // 3) Transacción con bloqueo de fila para consistencia de stock
        DB::transaction(function () use ($compra) {
            // Bloquear la fila del inventario para esta operación
            $locked = Inventario::whereKey($compra['inventario_id'])->lockForUpdate()->firstOrFail();

            // Crear la compra (entrada)
            Compra::create([
                'inventario_id' => $locked->id,
                'cantidad' => round($compra['cantidad'], 2),
                'fecha' => $compra['fecha'],
                'observaciones' => $compra['observaciones'] ?? null,
                'precio_compra' => round($compra['precio_compra'], 2),
            ]);

            // Sumar stock
            $locked->increment('stock_actual', round($compra['cantidad'], 2));
        });

        return redirect()->route('eca.index', ['seccion' => 'movimientos'])
            ->with('ok', 'Entrada registrada.');
    }

    public function storeVenta(Request $request)
    {
        $userId = Auth::id();

        // 1) Validación con namespacing "venta.*"
        $data = $request->validate([
            'venta.inventario_id' => ['required', 'uuid', 'exists:inventario,id'],
            'venta.cantidad' => ['required', 'numeric', 'gt:0'],
            'venta.fecha' => ['required', 'date'],
            'venta.precio_venta' => ['required', 'numeric', 'gte:0'],
            'venta.centro_acopio_id' => ['nullable', 'uuid', 'exists:centros_acopio,id'],
            'venta.observaciones' => ['nullable', 'string', 'max:500'],
        ]);

        $data['venta']['cantidad'] = round((float)$data['venta']['cantidad'], 2);
        $data['venta']['precio_venta'] = round((float)($data['venta']['precio_venta'] ?? 0), 2);
        $venta = $data['venta'];

        // Verificar pertenencia del inventario al Punto del gestor autenticado
        $inv = Inventario::with('puntoEca:id,gestor_id')->findOrFail($venta['inventario_id']);
        if ($inv->puntoEca?->gestor_id !== $userId) {
            abort(403, 'No autorizado.');
        }

        // Transacción con bloqueo de fila y verificación de stock suficiente
        DB::transaction(function () use ($venta) {
            // Bloquear la fila del inventario para esta operación
            $locked = Inventario::whereKey($venta['inventario_id'])->lockForUpdate()->firstOrFail();

            // Stock suficiente
            if (($locked->stock_actual ?? 0) < $venta['cantidad']) {
                throw ValidationException::withMessages([
                    'venta.cantidad' => 'Stock insuficiente para la salida.',
                ]);
            }

            // Crear la venta (salida)
            Venta::create([
                'inventario_id' => $locked->id,
                'cantidad' => round($venta['cantidad'], 2),
                'fecha' => $venta['fecha'],
                'precio_venta' => round($venta['precio_venta'], 2),
                'centro_acopio_id' => $venta['centro_acopio_id'],
                'observaciones' => $venta['observaciones'] ?? null,
            ]);

            // Restar stock
            $locked->decrement('stock_actual', round($venta['cantidad'], 2));
        });

        return redirect()->route('eca.index', ['seccion' => 'movimientos'])
            ->with('ok', 'Salida registrada.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
