<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use App\Models\TipoMaterial;
use App\Models\CategoriaMaterial;
use App\Models\Material;
use App\Models\Inventario;
use App\Models\PuntoEca;

class PuntoEcaController extends Controller


{
    protected $permitidas = ['resumen', 'perfil', 'materiales', 'historial', 'calendario', 'centros', 'conversaciones', 'configuracion'];

    public function view_punto_eca(Request $request, $seccion = null)
    {
        if ($seccion === null || !in_array($seccion, $this->permitidas, true)) {
            $seccion = 'resumen';
        }

        // Solo preparar datos si es la sección "materiales"
            // Punto ECA actual (ajusta a tu lógica real)
            $puntoEcaId = $defaultPuntoEcaId = config('app.default_punto_eca_id');

            // ====== CATÁLOGOS para los <select> ======
            $categorias = CategoriaMaterial::orderBy('nombre')->get(['id', 'nombre']);
            $tipos      = TipoMaterial::orderBy('nombre')->get(['id', 'nombre']);

            // ====== FILTROS del catálogo (arriba) ======
            $f = $request->validate([
                'categoria' => ['nullable', 'uuid', 'exists:categorias_material,id'],
                'tipo'      => ['nullable', 'uuid', 'exists:tipos_material,id'],
                'nombre'    => ['nullable', 'string', 'max:120'],
            ]);

            // Excluir los materiales ya registrados para este punto (opcional pero recomendado)
            $materialesYaRegistrados = Inventario::query()
                ->when($puntoEcaId, fn($q) => $q->where('punto_eca_id', $puntoEcaId))
                ->pluck('material_id');

            // Catálogo de materiales disponibles
            $materiales = Material::query()
                ->with(['categoria:id,nombre', 'tipo:id,nombre'])
                ->when($f['categoria'] ?? null, fn($q, $v) => $q->where('categoria_id', $v))
                ->when($f['tipo'] ?? null,      fn($q, $v) => $q->where('tipo_id', $v))
                ->when($f['nombre'] ?? null,    fn($q, $v) => $q->where('nombre', 'like', "%{$v}%"))
                ->when($puntoEcaId, fn($q) => $q->whereNotIn('id', $materialesYaRegistrados))
                ->orderBy('nombre')
                ->paginate(6)
                ->withQueryString();

            // ====== FILTROS del inventario (abajo) ======
            $q = $request->validate([
                'q_categoria' => ['nullable', 'uuid', 'exists:categorias_material,id'],
                'q_tipo'      => ['nullable', 'uuid', 'exists:tipos_material,id'],
                'q_nombre'    => ['nullable', 'string', 'max:120'],
            ]);

            // Inventario ya registrado para el Punto ECA
            $inventario = Inventario::query()
                ->with([
                    'material:id,nombre,categoria_id,tipo_id',
                    'material.categoria:id,nombre',
                    'material.tipo:id,nombre'
                ])
                ->when($puntoEcaId, fn($q2) => $q2->where('punto_eca_id', $puntoEcaId))
                ->when($q['q_categoria'] ?? null, fn($q2, $v) => $q2->whereHas('material', fn($qq) => $qq->where('categoria_id', $v)))
                ->when($q['q_tipo'] ?? null,      fn($q2, $v) => $q2->whereHas('material', fn($qq) => $qq->where('tipo_id', $v)))
                ->when($q['q_nombre'] ?? null,    fn($q2, $v) => $q2->whereHas('material', fn($qq) => $qq->where('nombre', 'like', "%{$v}%")))
                ->orderByDesc('creado') // usa tus columnas custom de timestamp
                ->paginate(6)
                ->withQueryString();

            // Renderizar la misma Blade que ya tienes
            return view('PuntoECA.punto-eca', compact(
                'seccion',
                'categorias',
                'tipos',
                'materiales',
                'inventario',
                'puntoEcaId'
            ));
        

        // Otras secciones no se tocan
    }

    /**
     * POST: Registrar un material en inventario (botón por fila).
     * Usa validación inline (puedes mover a FormRequest si prefieres).
     */
    public function storeInventario(Request $request)
    {
        // 1) Forzar el punto por defecto desde config/.env
        $puntoEcaId = config('app.default_punto_eca_id');

        // 2) Validación mínima (sin pedir punto_eca_id al cliente)
        $data = $request->validate([
            'material_id'   => [
                'required',
                'uuid',
                'exists:materiales,id',
                // unique por material dentro del punto por defecto:
                Rule::unique('inventario')->where(fn($q) => $q->where('punto_eca_id', $puntoEcaId)),
            ],
            'capacidad_max' => ['nullable', 'numeric', 'gte:0'],
            'unidad_medida' => ['nullable', 'in:kg,unidad,t,m3'],
            'stock_actual'  => ['nullable', 'numeric', 'gte:0'],
            'umbral_alerta' => ['nullable', 'numeric', 'gte:0'],
            'umbral_critico' => ['nullable', 'numeric', 'gte:0'],
            'precio_compra' => ['nullable', 'numeric', 'gte:0'],
            'precio_venta'  => ['nullable', 'numeric', 'gte:0'],
            'nota_material' => ['nullable', 'string', 'max:300'],
            'activo'        => ['required', 'boolean'],
        ], [
            'material_id.unique' => 'Este material ya está registrado para este Punto ECA.',
        ]);

        // 3) Crear el registro MERGEANDO el punto por defecto
        DB::transaction(function () use ($data, $puntoEcaId) {
            Inventario::create(array_merge($data, [
                'punto_eca_id' => $puntoEcaId,
            ]));
        });

        return back()->with('ok', 'Material registrado en inventario.');
    }
    public function updateInventario(Request $request, Inventario $inventario)
    {
        // Punto ECA fijo (no confiar en el form)
        $puntoEcaId = config('app.default_punto_eca_id');

        // Asegurar que el registro pertenece al punto por defecto
        if ($inventario->punto_eca_id !== $puntoEcaId) {
            abort(403, 'No autorizado.');
        }

        $data = $request->validate([
            'capacidad_max'  => ['nullable', 'numeric', 'gte:0'],
            'unidad_medida'  => ['nullable', 'in:kg,unidad,t,m3'],
            'stock_actual'   => ['nullable', 'numeric', 'gte:0'],
            'umbral_alerta'  => ['nullable', 'numeric', 'gte:0'],
            'umbral_critico' => ['nullable', 'numeric', 'gte:0'],
            'precio_compra'  => ['nullable', 'numeric', 'gte:0'],
            'precio_venta'   => ['nullable', 'numeric', 'gte:0'],
            'nota_material'  => ['nullable', 'string', 'max:300'],
            'activo'         => ['required', 'boolean'],
        ]);

        $inventario->update($data);

        return back()->with('ok', 'Inventario actualizado.');
    }

    public function destroyInventario(Inventario $inventario)
    {
        $puntoEcaId = config('app.default_punto_eca_id');

        if ($inventario->punto_eca_id !== $puntoEcaId) {
            abort(403, 'No autorizado.');
        }

        $inventario->delete();

        return back()->with('ok', 'Material eliminado del inventario.');
    }
}