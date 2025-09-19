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
use App\Models\Venta;
use App\Models\PuntoEca;
use App\Models\Compra;
use Illuminate\Support\Carbon;
use App\Models\CentroAcopio;

class PuntoEcaController extends Controller
{
    protected $permitidas = ['resumen', 'perfil', 'materiales', 'movimientos', 'historial', 'calendario', 'centros', 'conversaciones', 'configuracion'];

    public function view_punto_eca(Request $request, $seccion = null)
    {
        $usuario = Auth::user();

        if ($seccion === null || !in_array($seccion, $this->permitidas, true)) {
            $seccion = 'resumen';
        }

        //  Punto ECA del gestor autenticado
        $punto = DB::table('puntos_eca')->select('id', 'gestor_id', 'nombre', 'mostrar_mapa', 'direccion', 'ciudad', 'localidad', 'latitud', 'longitud', 'nit', 'horario_atencion', 'sitio_web', 'logo_url', 'foto_url', 'estado')->where('gestor_id', Auth::id())->first();

        if (!$punto) {
            abort(404, 'No se encontró Punto ECA para este usuario.');
        }

        $puntoEcaId = $punto->id;

        $payload = [
            'seccion' => $seccion,
            'puntoEcaId' => $puntoEcaId,
            'punto' => $punto,
        ];

        // ===== Catálogos para los <select>
        $categorias = CategoriaMaterial::orderBy('nombre')->get(['id', 'nombre']);
        $tipos = TipoMaterial::orderBy('nombre')->get(['id', 'nombre']);

        // ===== Filtros del CATÁLOGO (arriba)
        $f = $request->validate([
            'categoria' => ['nullable', 'uuid', 'exists:categorias_material,id'],
            'tipo' => ['nullable', 'uuid', 'exists:tipos_material,id'],
            'nombre' => ['nullable', 'string', 'max:120'],
        ]);

        // Excluir materiales ya registrados en inventario del Punto
        $materialesYaRegistrados = Inventario::query()->where('punto_eca_id', $puntoEcaId)->pluck('material_id');

        // Catálogo de materiales (paginado)
        $materiales = Material::query()
            ->with(['categoria:id,nombre', 'tipo:id,nombre'])
            ->when($f['categoria'] ?? null, fn($q, $v) => $q->where('categoria_id', $v))
            ->when($f['tipo'] ?? null, fn($q, $v) => $q->where('tipo_id', $v))
            ->when($f['nombre'] ?? null, fn($q, $v) => $q->where('nombre', 'like', "%{$v}%"))
            ->whereNotIn('id', $materialesYaRegistrados)
            ->orderBy('nombre')
            ->paginate(6)
            ->withQueryString();

        // ===== Filtros del INVENTARIO (abajo)
        $q = $request->validate([
            'q_categoria' => ['nullable', 'uuid', 'exists:categorias_material,id'],
            'q_tipo' => ['nullable', 'uuid', 'exists:tipos_material,id'],
            'q_nombre' => ['nullable', 'string', 'max:120'],
        ]);

        // Inventario del Punto (paginado)
        $inventario = Inventario::query()
            ->with(['material:id,nombre,categoria_id,tipo_id', 'material.categoria:id,nombre', 'material.tipo:id,nombre'])
            ->where('punto_eca_id', $puntoEcaId)
            ->when($q['q_categoria'] ?? null, fn($q2, $v) => $q2->whereHas('material', fn($m) => $m->where('categoria_id', $v)))
            ->when($q['q_tipo'] ?? null, fn($q2, $v) => $q2->whereHas('material', fn($m) => $m->where('tipo_id', $v)))
            ->when($q['q_nombre'] ?? null, fn($q2, $v) => $q2->whereHas('material', fn($m) => $m->where('nombre', 'like', "%{$v}%")))
            // Si tienes columna personalizada "creado", usa esa; si no, latest():
            ->orderByDesc('creado') // ->latest() si no existe "creado"
            ->paginate(6)
            ->withQueryString();

        // ===== Últimos movimientos (siempre cargados)
        // Compras (top 10 por fecha)
        $compras = Compra::query()
            ->with(['inventario.material:id,nombre'])
            ->whereHas('inventario', fn($q2) => $q2->where('punto_eca_id', $puntoEcaId))
            ->latest('fecha') // o ->latest()
            ->take(10)
            ->get()
            ->map(function ($c) {
                return [
                    'tipo' => 'compra',
                    'fecha' => $c->fecha instanceof \Illuminate\Support\Carbon ? $c->fecha->format('Y-m-d') : (string) $c->fecha,
                    'material' => $c->inventario->material->nombre ?? '—',
                    'cantidad' => $c->cantidad,
                    'unidad' => $c->inventario->unidad_medida ?? '',
                    'precio_unit' => $c->precio_compra,
                    'observ' => $c->observaciones ?? null,
                ];
            });

        // Ventas (top 10 por fecha)
        $ventas = Venta::query()
            ->with(['inventario.material:id,nombre'])
            ->whereHas('inventario', fn($q2) => $q2->where('punto_eca_id', $puntoEcaId))
            ->latest('fecha')
            ->take(10)
            ->get()
            ->map(function ($v) {
                return [
                    'tipo' => 'venta',
                    'fecha' => $v->fecha instanceof \Illuminate\Support\Carbon ? $v->fecha->format('Y-m-d') : (string) $v->fecha,
                    'material' => $v->inventario->material->nombre ?? '—',
                    'cantidad' => $v->cantidad,
                    'unidad' => $v->inventario->unidad_medida ?? '',
                    'precio_unit' => $v->precio_venta,
                    'observ' => $v->observaciones ?? null,
                ];
            });

        $ultimosMovimientos = $compras->concat($ventas)->sortByDesc('fecha')->take(10)->values();

        // ===== Materiales del Punto (para combos/filtros)
        $materialesPunto = Inventario::query()->where('punto_eca_id', $puntoEcaId)->with('material:id,nombre')->get()->pluck('material')->filter()->unique('id')->sortBy('nombre')->values();

        // ===== Historial Compras (entradas)
        $hc = $request->validate([
            'hc_desde' => ['nullable', 'date'],
            'hc_hasta' => ['nullable', 'date'],
            'hc_material' => ['nullable', 'uuid', 'exists:materiales,id'],
        ]);
        $hcHasta = !empty($hc['hc_hasta']) ? Carbon::parse($hc['hc_hasta'])->endOfDay() : null;

        $histCompras = \App\Models\Compra::query()
            ->with(['inventario:id,unidad_medida,material_id,precio_compra', 'inventario.material:id,nombre'])
            ->whereHas('inventario', fn($q) => $q->where('punto_eca_id', $puntoEcaId))
            ->when($hc['hc_material'] ?? null, fn($q, $matId) => $q->whereHas('inventario', fn($qi) => $qi->where('material_id', $matId)))
            ->when($hc['hc_desde'] ?? null, fn($q, $desde) => $q->whereDate('fecha', '>=', $desde))
            ->when($hcHasta, fn($q) => $q->where('fecha', '<=', $hcHasta))
            ->latest('fecha')
            ->paginate(10)
            ->withQueryString();

        // ===== Historial Ventas (salidas)
        $hs = $request->validate([
            'hs_desde' => ['nullable', 'date'],
            'hs_hasta' => ['nullable', 'date'],
            'hs_material' => ['nullable', 'uuid', 'exists:materiales,id'],
        ]);
        $hsHasta = !empty($hs['hs_hasta']) ? Carbon::parse($hs['hs_hasta'])->endOfDay() : null;

        $histVentas = \App\Models\Venta::query()
            ->with(['inventario:id,unidad_medida,material_id', 'inventario.material:id,nombre'])
            ->whereHas('inventario', fn($q) => $q->where('punto_eca_id', $puntoEcaId))
            ->when($hs['hs_material'] ?? null, fn($q, $matId) => $q->whereHas('inventario', fn($qi) => $qi->where('material_id', $matId)))
            ->when($hs['hs_desde'] ?? null, fn($q, $desde) => $q->whereDate('fecha', '>=', $desde))
            ->when($hsHasta, fn($q) => $q->where('fecha', '<=', $hsHasta))
            ->latest('fecha')
            ->paginate(10)
            ->withQueryString();

        // ===== Centros de Acopio (listas + catálogos) — respeta variables en minúscula y camelCase
        // Filtros (GET): nombre, tipo, ciudad, estado, material
        $f2 = $request->validate([
            'f_nombre' => ['nullable', 'string', 'max:150'],
            'f_tipo' => ['nullable', Rule::in(['Planta', 'Proveedor', 'Otro'])],
            'f_ciudad' => ['nullable', 'string', 'max:60'],
            'f_estado' => ['nullable', Rule::in(['activo', 'inactivo', 'bloqueado'])],
            'f_material' => ['nullable', 'uuid', 'exists:materiales,id'],
        ]);

        $applyFilters = function ($q) use ($f2) {
            return $q
                ->when($f2['f_nombre'] ?? null, fn($qq, $v) => $qq->where('nombre', 'like', "%{$v}%"))
                ->when($f2['f_tipo'] ?? null, fn($qq, $v) => $qq->where('tipo', $v))
                ->when($f2['f_ciudad'] ?? null, fn($qq, $v) => $qq->where('ciudad', 'like', "%{$v}%"))
                ->when($f2['f_estado'] ?? null, fn($qq, $v) => $qq->where('estado', $v))
                ->when($f2['f_material'] ?? null, fn($qq, $v) => $qq->where('materiales_centro_acc', $v));
        };

        // Listas para selects
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

        // Catálogos (paginados)
        $centrosGlobales = CentroAcopio::query()
            ->with(['materiales'])
            ->where('alcance', 'global')
            ->tap($applyFilters)
            ->orderBy('nombre')
            ->paginate(10)
            ->withQueryString();

        $centrosPropios = CentroAcopio::query()
            ->with(['materiales'])
            ->where('alcance', 'eca')
            ->where('owner_punto_eca_id', $puntoEcaId)
            ->tap($applyFilters)
            ->orderBy('nombre')
            ->paginate(10)
            ->withQueryString();

        // ===== Calendario SIN JS (grilla 6x7) — usa solo material_id
        $y = intval($request->query('y', now()->year));
        $m = intval($request->query('m', now()->month));
        $sel = $request->query('sel'); // YYYY-MM-DD o null

        $firstOfMonth = Carbon::create($y, $m, 1)->startOfDay();
        $dow = $firstOfMonth->dayOfWeekIso; // 1=Lun..7=Dom
        $start = $firstOfMonth
            ->copy()
            ->subDays($dow - 1)
            ->startOfDay(); // Lunes
        $end = $start->copy()->addDays(41)->endOfDay(); // 42 días

        $baseStart = $start->copy()->subMonthNoOverflow()->startOfDay(); // cubrir mensuales que “caen”

        // SELECT: solo nombres + campos de programación (sin IDs)
        $rows = DB::table('programacion_recoleccion as pr')
            ->where('pr.punto_eca_id', $puntoEcaId)
            ->whereBetween('pr.fecha', [$baseStart->toDateString(), $end->toDateString()])
            ->leftJoin('materiales as m3', 'm3.id', '=', 'pr.material_id')
            ->leftJoin('centros_acopio as ca', 'ca.id', '=', 'pr.centro_acopio_id')
            ->orderBy('pr.fecha')
            ->get(['pr.fecha', 'pr.hora', 'pr.frecuencia', 'pr.notas', DB::raw('COALESCE(pr.creado, NULL) as creado'), 'm3.nombre  as material_nombre', 'ca.nombre  as centro_nombre']);

        // Expandir repeticiones dentro de [start, end] y NORMALIZAR CLAVES
        $eventos = []; // 'YYYY-MM-DD' => [ eventos... ]
        foreach ($rows as $r) {
            $c = \Carbon\Carbon::parse($r->fecha)->startOfDay();

            $push = function (\Carbon\Carbon $d) use (&$eventos, $r, $start, $end, $punto) {
                if (!$d->betweenIncluded($start, $end)) {
                    return;
                }

                $key = $d->toDateString();
                $hhmm = $r->hora ? \Illuminate\Support\Str::of($r->hora)->limit(5, '')->__toString() : null;

                $event = [
                    // nombres “humanos”
                    'punto' => $punto->nombre ?? '—',
                    'material' => $r->material_nombre,
                    'centro' => $r->centro_nombre,

                    // datos de programación
                    'fecha' => $key,
                    'hora' => $r->hora,
                    'time' => $hhmm,
                    'frecuencia' => $r->frecuencia,
                    'freq' => $r->frecuencia,
                    'notas' => $r->notas,
                    'notes' => $r->notas,
                    'creado' => $r->creado,
                ];

                // con esto verificamos si hay una programación para el material y el centro, y lo agregamos
                $eventos[$key] = $eventos[$key] ?? [];
                // si no hay programación para el material y el centro, lo agregamos
                $eventos[$key][] = $event;
            };

            // sin repeticiones
            $push($c);

            // repeticiones
            if (!in_array($r->frecuencia, ['manual', 'unico'], true)) {
                $cursor = $c->copy();
                while (true) {
                    switch ($r->frecuencia) {
                        case 'semanal':
                            $cursor->addWeek();
                            break;
                        case 'quincenal':
                            $cursor->addDays(15);
                            break;
                        case 'mensual':
                            $cursor->addMonthNoOverflow();
                            break;
                        default:
                            $cursor->addYears(100);
                    }
                    if ($cursor->gt($end)) {
                        break;
                    }
                    $push($cursor);
                }
            }
        }

        // Construir 42 días para la grilla
        $dias = [];
        $iter = $start->copy();
        for ($i = 0; $i < 42; $i++) {
            $key = $iter->toDateString();
            $items = collect($eventos[$key] ?? [])
                ->sortBy(fn($e) => $e['time'] ?? '')
                ->values()
                ->all();

            $dias[] = [
                'date' => $iter->copy(),
                'events' => $items,
                'inMonth' => $iter->month === $firstOfMonth->month,
            ];
            $iter->addDay();
        }

        // Navegación y labels
        $prev = $firstOfMonth->copy()->subMonthNoOverflow();
        $next = $firstOfMonth->copy()->addMonthNoOverflow();

        $navPrevUrl = $request->fullUrlWithQuery(['seccion' => 'calendario', 'y' => $prev->year, 'm' => $prev->month]);
        $navNextUrl = $request->fullUrlWithQuery(['seccion' => 'calendario', 'y' => $next->year, 'm' => $next->month]);
        $mesTitulo = \Illuminate\Support\Str::ucfirst($firstOfMonth->translatedFormat('F Y'));
        $rangoLabel = $start->toDateString() . ' → ' . $end->toDateString();

        $payload['sel'] = $request->query('sel');

        // Límites de mes actual (para Entradas/Salidas del mes)
        $inicioMes = Carbon::now()->startOfMonth()->toDateString();
        $finMes = Carbon::now()->endOfMonth()->toDateString();

        // Inventario total (kg) = suma de stock_actual del punto
        $kpiInventario = (float) \App\Models\Inventario::query()->where('punto_eca_id', $puntoEcaId)->sum('stock_actual');

        // Entradas del mes (kg) = suma de cantidad en Compras del mes para mi punto
        $kpiEntradasMes = (float) \App\Models\Compra::query()
            ->whereHas('inventario', fn($q) => $q->where('punto_eca_id', $puntoEcaId))
            ->whereBetween('fecha', [$inicioMes, $finMes])
            ->sum('cantidad');

        // Salidas del mes (kg) = suma de cantidad en Ventas del mes para mi punto
        $kpiSalidasMes = (float) \App\Models\Venta::query()
            ->whereHas('inventario', fn($q) => $q->where('punto_eca_id', $puntoEcaId))
            ->whereBetween('fecha', [$inicioMes, $finMes])
            ->sum('cantidad');

        // Próximo despacho (si tienes ProgramacionRecoleccion; si no existe el modelo, queda null)
        $kpiProximoDespacho = null;
        if (class_exists(\App\Models\ProgramacionRecoleccion::class)) {
            $proximo = \App\Models\ProgramacionRecoleccion::query()
                ->with(['material:id,nombre', 'centroAcopio:id,nombre'])
                ->where('punto_eca_id', $puntoEcaId)
                ->whereDate('fecha', '>=', Carbon::today()->toDateString())
                ->orderBy('fecha')
                ->orderBy('hora')
                ->first();

            if ($proximo) {
                $fecha = $proximo->fecha instanceof \Illuminate\Support\Carbon ? $proximo->fecha->format('Y-m-d') : (string) $proximo->fecha;

                $kpiProximoDespacho = sprintf('%s %s • %s • %s', $fecha, $proximo->hora ?? '', $proximo->material->nombre ?? '—', $proximo->centroAcopio->nombre ?? '—');
            }
        }

        // Alertas por umbrales/capacidad según columnas del inventario
        $alertas = [];
        $invParaAlertas = \App\Models\Inventario::query()
            ->with(['material:id,nombre'])
            ->where('punto_eca_id', $puntoEcaId)
            ->get(['id', 'material_id', 'stock_actual', 'umbral_alerta', 'umbral_critico', 'capacidad_max', 'unidad_medida']);

        foreach ($invParaAlertas as $it) {
            $nombre = $it->material->nombre ?? '—';
            // Crítico: stock <= umbral_critico
            if (!is_null($it->umbral_critico) && $it->stock_actual <= $it->umbral_critico) {
                $alertas[] = [
                    'tipo' => 'crítico',
                    'texto' => "Stock CRÍTICO de {$nombre}: {$it->stock_actual} {$it->unidad_medida} (≤ {$it->umbral_critico})",
                ];
                continue; // ya es crítico, no señalamos 'bajo' a la vez
            }
            // Bajo: stock <= umbral_alerta
            if (!is_null($it->umbral_alerta) && $it->stock_actual <= $it->umbral_alerta) {
                $alertas[] = [
                    'tipo' => 'bajo',
                    'texto' => "Stock bajo de {$nombre}: {$it->stock_actual} {$it->unidad_medida} (≤ {$it->umbral_alerta})",
                ];
            }
            // Lleno: stock >= capacidad_max
            if (!is_null($it->capacidad_max) && $it->stock_actual >= $it->capacidad_max) {
                $alertas[] = [
                    'tipo' => 'lleno',
                    'texto' => "Capacidad llena de {$nombre}: {$it->stock_actual}/{$it->capacidad_max} {$it->unidad_medida}",
                ];
            }
        }

        // Empaquetar resumen
        $payload['resumen'] = [
            'inventario_total' => $kpiInventario,
            'entradas_mes' => $kpiEntradasMes,
            'salidas_mes' => $kpiSalidasMes,
            'proximo_despacho' => $kpiProximoDespacho,
            'alertas' => $alertas,
        ];

        // === AJUSTES / CONFIGURACIÓN ===
        // Usa mostrar_mapa del punto y, si existe, el flag de notificaciones del usuario.
        $usuario = Auth::user();
        $payload['config'] = [
            'mostrar_mapa' => (bool) ($punto->mostrar_mapa ?? false),
            'recibir_notificaciones' => (bool) ($usuario->recibe_notificaciones ?? false),
        ];
        // ===== Mezcla al payload (TODO JUNTO, sin condicionar por sección)
        $payload += [
            'categorias' => $categorias,
            'tipos' => $tipos,
            'materiales' => $materiales,
            'inventario' => $inventario,
            'ultimosMovimientos' => $ultimosMovimientos,
            'usuarios' => $usuario,

            'materialesPunto' => $materialesPunto,
            'hc' => $hc,
            'hs' => $hs,
            'histCompras' => $histCompras,
            'histVentas' => $histVentas,

            // Centros (camelCase)
            'centrosGlobales' => $centrosGlobales,
            'centrosPropios' => $centrosPropios,
            'centrosGlobalesLista' => $centrosGlobalesLista,
            'centrosPropiosLista' => $centrosPropiosLista,

            // Centros (minúsculas para compatibilidad con la vista)
            'centrosglobaleslista' => $centrosGlobalesLista,
            'centrospropioslista' => $centrosPropiosLista,

            // Calendario sin JS
            'dias' => $dias,
            'mesTitulo' => $mesTitulo,
            'navPrevUrl' => $navPrevUrl,
            'navNextUrl' => $navNextUrl,
            'rangoLabel' => $rangoLabel,
        ];

        return view('PuntoECA.punto-eca', $payload);
    }

    public function storeInventario(Request $request)
    {
        // 1) Forzar el punto por defecto desde config/.env
        $punto = DB::table('puntos_eca')->select('id', 'gestor_id')->where('gestor_id', Auth::id())->first();
        $puntoEcaId = $punto->id;
        // 2) Validación mínima (sin pedir punto_eca_id al cliente)
        $data = $request->validate(
            [
                'material_id' => [
                    'required',
                    'uuid',
                    'exists:materiales,id',
                    // unique por material dentro del punto por defecto:
                    Rule::unique('inventario')->where(fn($q) => $q->where('punto_eca_id', $puntoEcaId)),
                ],
                'capacidad_max' => ['nullable', 'numeric', 'gte:0'],
                'unidad_medida' => ['nullable', 'in:kg,unidad,t,m3'],
                'stock_actual' => ['nullable', 'numeric', 'gte:0'],
                'umbral_alerta' => ['nullable', 'numeric', 'gte:0'],
                'umbral_critico' => ['nullable', 'numeric', 'gte:0'],
                'precio_compra' => ['nullable', 'numeric', 'gte:0'],
                'precio_venta' => ['nullable', 'numeric', 'gte:0'],
                'nota_material' => ['nullable', 'string', 'max:300'],
                'activo' => ['required', 'boolean'],
            ],
            [
                'material_id.unique' => 'Este material ya está registrado para este Punto ECA.',
            ],
        );

        DB::transaction(function () use ($data, $puntoEcaId) {
            Inventario::create(
                array_merge($data, [
                    'punto_eca_id' => $puntoEcaId,
                ]),
            );
        });

        return back()->with('ok', 'Material registrado en inventario.');
    }
    public function updateInventario(Request $request, Inventario $inventario)
    {
        // Punto ECA fijo (no confiar en el form)
        $punto = DB::table('puntos_eca')->select('id', 'gestor_id')->where('gestor_id', Auth::id())->first();
        $puntoEcaId = $punto->id;
        // Asegurar que el registro pertenece al punto por defecto
        if ($inventario->punto_eca_id !== $puntoEcaId) {
            abort(403, 'No autorizado.');
        }

        $data = $request->validate([
            'capacidad_max' => ['nullable', 'numeric', 'gte:0'],
            'unidad_medida' => ['nullable', 'in:kg,unidad,t,m3'],
            'stock_actual' => ['nullable', 'numeric', 'gte:0'],
            'umbral_alerta' => ['nullable', 'numeric', 'gte:0'],
            'umbral_critico' => ['nullable', 'numeric', 'gte:0'],
            'precio_compra' => ['nullable', 'numeric', 'gte:0'],
            'precio_venta' => ['nullable', 'numeric', 'gte:0'],
            'nota_material' => ['nullable', 'string', 'max:300'],
            'activo' => ['required', 'boolean'],
        ]);

        $inventario->update($data);

        return back()->with('ok', 'Inventario actualizado.');
    }

    public function destroyInventario(Request $request, Inventario $inventario)
    {
        $punto = DB::table('puntos_eca')->select('id', 'gestor_id')->where('gestor_id', Auth::id())->first();
        $puntoEcaId = $punto->id;
        if ($inventario->punto_eca_id !== $puntoEcaId) {
            abort(403, 'No autorizado.');
        }

        $inventario->delete();

        return back()->with('ok', 'Material eliminado del inventario.');
    }
}
