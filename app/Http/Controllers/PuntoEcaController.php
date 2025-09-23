<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use App\Models\CentroAcopio;
use App\Models\Inventario;
use App\Models\Venta;
use App\Models\Compra;
use App\Models\ProgramacionRecoleccion;
use Illuminate\Support\Carbon;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\MovimientosController;
use App\Http\Controllers\ProgramacionRecoleccionController;
use App\Http\Controllers\CentroAcopioController;
use App\Models\Usuario;

// panel punto eca
class PuntoEcaController extends Controller
{
    // secciones permitidas
    protected $permitidas = ['resumen', 'perfil', 'materiales', 'movimientos', 'historial', 'calendario', 'centros', 'conversaciones', 'configuracion'];

    // vista principal
    public function view_punto_eca(Request $request, $seccion = null)
    {
        // Validar y establecer sección por defecto
        if ($seccion === null || !in_array($seccion, $this->permitidas, true)) {
            $seccion = 'resumen';
        }

        $usuario = Auth::user();

        // datos punto
        $punto = DB::table('puntos_eca')
            ->select(
                'id',
                'gestor_id',
                'nombre',
                'telefonoPunto',
                'mostrar_mapa',
                'direccion',
                'ciudad',
                'localidad',
                'latitud',
                'longitud',
                'nit',
                'horario_atencion',
                'sitio_web',
                'logo_url',
                'foto_url',
                'estado'
            )
            ->where('gestor_id', Auth::id())
            ->firstOrFail();

        $puntoEcaId = $punto->id;


        // base
        $viewData = [
            'usuarios' => $usuario,
            'seccion' => $seccion,
            'punto' => $punto,
            'puntoEcaId' => $punto->id,
        ];
        // NOTA: Ya que cada sección de la Blade está envuelta en @if($seccion==='x'),
        // no es necesario inicializar placeholders para evitar "Undefined variable".
        // Solo cargaremos lo estrictamente necesario por sección.

        // delegar
        switch ($seccion) {
            case 'materiales':
                $materialController = new MaterialController();
                $materialData = $materialController->data($request);
                $viewData = array_merge($viewData, $materialData);
                break;

            case 'movimientos':
                // movimientos
                $movimientosController = new MovimientosController();
                $movimientosData = $movimientosController->data($request);

                // inventario selects
                $inventarioList = Inventario::query()
                    ->with('material:id,nombre')
                    ->where('punto_eca_id', $puntoEcaId)
                    ->orderBy('creado', 'desc')
                    ->get();

                $viewData = array_merge($viewData, $movimientosData, [
                    'inventario' => $inventarioList,
                ]);
                break;

            case 'historial':
                $movimientosController = new MovimientosController();
                $histData = $movimientosController->dataHistorial($request);
                $viewData = array_merge($viewData, $histData);
                break;

            case 'calendario':
                // calendario
                $calendarioController = new ProgramacionRecoleccionController();
                $calData = $calendarioController->data($request);

                // filtros centros
                $f2 = $request->validate([
                    'f_nombre' => ['nullable', 'string', 'max:150'],
                    'f_tipo' => ['nullable', Rule::in(['Planta', 'Proveedor', 'Otro'])],
                    'f_localidad' => ['nullable', 'string', 'max:60'],
                    'f_material' => ['nullable', 'uuid', 'exists:materiales,id'],
                ]);
                $applyFilters = function ($q) use ($f2) {
                    return $q
                        ->when(($f2['f_nombre'] ?? null), fn($qq, $v) => $qq->where('nombre', 'like', "%{$v}%"))
                        ->when(($f2['f_tipo'] ?? null), fn($qq, $v) => $qq->where('tipo', $v))
                        ->when(($f2['f_localidad'] ?? null), fn($qq, $v) => $qq->where('localidad', 'like', "%{$v}%"))
                        ->when(($f2['f_estado'] ?? null), fn($qq, $v) => $qq->where('estado', $v))
                        ->when(($f2['f_material'] ?? null), fn($qq, $v) => $qq->where('materiales_centro_acc', $v));
                };
                $centrosGlobalesLista = CentroAcopio::query()
                    ->select(['id', 'nombre'])
                    ->where('alcance', 'global')
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

                $inventarioList = Inventario::query()
                    ->with('material:id,nombre')
                    ->where('punto_eca_id', $puntoEcaId)
                    ->orderBy('creado', 'desc')
                    ->get();

                $viewData = array_merge($viewData, $calData, [
                    'inventario' => $inventarioList,
                    'centrosGlobalesLista' => $centrosGlobalesLista,
                    'centrosPropiosLista' => $centrosPropiosLista,
                    // Aliases en minúsculas usados en la Blade (para selects de programación)
                    'centrosglobaleslista' => $centrosGlobalesLista,
                    'centrospropioslista' => $centrosPropiosLista,
                ]);
                break;

            case 'centros':
                $centrosController = new CentroAcopioController();
                $centrosData = $centrosController->data($request);
                $viewData = array_merge($viewData, $centrosData);
                break;

            case 'resumen':
                // resumen
                $viewData['resumen'] = $this->getResumenData($punto->id);
                break;
        }

        // config usuario
        $viewData['config'] = [
            'mostrar_mapa' => (bool) ($punto->mostrar_mapa ?? false),
            'recibir_notificaciones' => (bool) (Auth::user()->recibe_notificaciones ?? false),
        ];

        // sin defaults

        return view('PuntoECA.punto-eca', $viewData);
    }

    // resumen data
    private function getResumenData($puntoEcaId)
    {
        // mes actual
        $inicioMes = Carbon::now()->startOfMonth()->toDateString();
        $finMes = Carbon::now()->endOfMonth()->toDateString();

        // inventario total
        $kpiInventario = (float) Inventario::query()
            ->where('punto_eca_id', $puntoEcaId)
            ->sum('stock_actual');

        // entradas mes
        $kpiEntradasMes = (float) Compra::query()
            ->whereHas('inventario', fn($q) => $q->where('punto_eca_id', $puntoEcaId))
            ->whereBetween('fecha', [$inicioMes, $finMes])
            ->sum('cantidad');

        // salidas mes
        $kpiSalidasMes = (float) Venta::query()
            ->whereHas('inventario', fn($q) => $q->where('punto_eca_id', $puntoEcaId))
            ->whereBetween('fecha', [$inicioMes, $finMes])
            ->sum('cantidad');

        // proximo despacho
        $proximo = ProgramacionRecoleccion::query()
            ->with(['material:id,nombre', 'centroAcopio:id,nombre'])
            ->where('punto_eca_id', $puntoEcaId)
            ->whereDate('fecha', '>=', Carbon::today()->toDateString())
            ->orderBy('fecha')
            ->orderBy('hora')
            ->first();

        $kpiProximoDespacho = null;
        if ($proximo) {
            $fecha = $proximo->fecha instanceof Carbon ?
                $proximo->fecha->format('Y-m-d') :
                (string) $proximo->fecha;

            $kpiProximoDespacho = sprintf(
                '%s %s • %s • %s',
                $fecha,
                $proximo->hora ?? '',
                $proximo->material->nombre ?? '—',
                $proximo->centroAcopio->nombre ?? '—'
            );
        }

        // alertas
        $alertas = [];
        $invParaAlertas = Inventario::query()
            ->with(['material:id,nombre'])
            ->where('punto_eca_id', $puntoEcaId)
            ->get(['id', 'material_id', 'stock_actual', 'umbral_alerta', 'umbral_critico', 'capacidad_max', 'unidad_medida']);

        foreach ($invParaAlertas as $it) {
            $nombre = $it->material->nombre ?? '—';

            // Normalizar valores a 2 decimales para mensajes
            $stock = round((float)$it->stock_actual, 2);
            $umbralAlerta = is_null($it->umbral_alerta) ? null : round((float)$it->umbral_alerta, 2);
            $umbralCritico = is_null($it->umbral_critico) ? null : round((float)$it->umbral_critico, 2);
            $capMax = is_null($it->capacidad_max) ? null : round((float)$it->capacidad_max, 2);

            if (!is_null($it->umbral_critico) && $it->stock_actual <= $it->umbral_critico) {
                $alertas[] = [
                    'tipo' => 'critico',
                    'mensaje' => "¡CRÍTICO! {$nombre} está en {$stock} {$it->unidad_medida} (umbral: {$umbralCritico})"
                ];
            }
            if (!is_null($it->umbral_alerta) && $it->stock_actual <= $it->umbral_alerta) {
                $alertas[] = [
                    'tipo' => 'bajo',
                    'mensaje' => "{$nombre} está bajo: {$stock} {$it->unidad_medida} (umbral: {$umbralAlerta})"
                ];
            }
            if (!is_null($it->capacidad_max) && $it->stock_actual >= $it->capacidad_max) {
                $alertas[] = [
                    'tipo' => 'lleno',
                    'mensaje' => "{$nombre} está lleno: {$stock} {$it->unidad_medida} (capacidad: {$capMax})"
                ];
            }
        }

        return [
            'inventario_total' => $kpiInventario,
            'entradas_mes' => $kpiEntradasMes,
            'salidas_mes' => $kpiSalidasMes,
            'proximo_despacho' => $kpiProximoDespacho,
            'alertas' => $alertas
        ];
    }
}
