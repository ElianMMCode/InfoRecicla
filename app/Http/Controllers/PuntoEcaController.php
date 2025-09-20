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
use App\Models\Compra;
use App\Models\ProgramacionRecoleccion;
use Illuminate\Support\Carbon;
use App\Models\CentroAcopio;
use Illuminate\Support\Str;

class PuntoEcaController extends Controller
{
    protected $permitidas = ['resumen', 'perfil', 'materiales', 'movimientos', 'historial', 'calendario', 'centros', 'conversaciones', 'configuracion'];

    public function view_punto_eca(Request $request, $seccion = null)
    {
        //  Usuario autenticado
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

        // Opciones para las selecciones
        $categorias = CategoriaMaterial::orderBy('nombre')->get(['id', 'nombre']);
        // Treer la categoria y tipo de los materiales ordenados por nombre, con la infomacion del id para el valor y el nombre para la seleccion
        $tipos = TipoMaterial::orderBy('nombre')->get(['id', 'nombre']);

        // Filtros del CATÁLOGO
        $f = $request->validate([
            'categoria' => ['nullable', 'uuid', 'exists:categorias_material,id'],
            'tipo' => ['nullable', 'uuid', 'exists:tipos_material,id'],
            'nombre' => ['nullable', 'string', 'max:120'],
        ]);

        // Excluir materiales ya registrados en inventario del Punto
        $materialesYaRegistrados = Inventario::query()->where('punto_eca_id', $puntoEcaId)->pluck('material_id');
        // pluck() devuelve un array con los id de los materiales registrados

        // Catálogo de materiales (paginado)
        $materiales = Material::query()
            //hacemos una consulta de todos los materiales, con su categoria y tipo
            ->with(['categoria:id,nombre', 'tipo:id,nombre'])
            //para esto nos apoyamos en las relaciones de la tabla materiales construidas en la migración y el modelo
            ->when($f['categoria'] ?? null, fn($q, $v) => $q->where('categoria_id', $v))
            //filtro condicional, si la categoria es nula, no se aplica el filtro
            //fn es una funcion anonima que recibe un query y un valor, que en este caso $v es el id de la categoria
            ->when($f['tipo'] ?? null, fn($q, $v) => $q->where('tipo_id', $v))
            ->when($f['nombre'] ?? null, fn($q, $v) => $q->where('nombre', 'like', "%{$v}%"))
            //para este caso usamos like para buscar el nombre de los materiales y con % para que busque en cualquier parte del string
            ->whereNotIn('id', $materialesYaRegistrados)
            //nos aseguramos de que no se repitan los materiales
            ->orderBy('nombre')
            //ordenamos por el nombre
            ->paginate(6)
            //paginamos por 6 materiales en la tabla
            ->withQueryString();
        //con withQueryString() agregamos la paginación a la url

        // Filtros del INVENTARIO
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
            //esta vez lo ordenamos por la fecha de creacion del registro en el inventario
            ->orderByDesc('creado')
            ->paginate(6)
            ->withQueryString();

        // ultimos movimientos
        // ulitmas 10 compras
        $compras = Compra::query()
            ->with(['inventario.material:id,nombre'])
            //filtramos el inventario para que solo traiga los de este punto
            ->whereHas('inventario', fn($q2) => $q2->where('punto_eca_id', $puntoEcaId))
            ->latest('fecha')
            //traemos las ultimas 10 compras
            ->take(10)
            ->get()
            //ejecutamos la consulta y traemos los resultados
            ->map(function ($c) {
                //con map() iteramos los resultados y creamos un array con los datos que queremos mostrar
                return [
                    // le definimos un tipo para que en la tabla sepa que es una compra
                    'tipo' => 'compra',
                    'fecha' => $c->fecha instanceof Carbon ? $c->fecha->format('Y-m-d') : (string) $c->fecha,
                    //si la fecha es una instancia de Carbon, la formateamos, si no, la convertimos a string
                    'material' => $c->inventario->material->nombre ?? '—',
                    'cantidad' => $c->cantidad ?? 0,
                    'unidad' => $c->inventario->unidad_medida ?? '',
                    'precio_unit' => $c->precio_compra ?? 0,
                    'observ' => $c->observaciones ?? null,
                ];
                //con esto ya tenemos un array con los datos de las compras
            });

        // ultimas 10 ventas
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
                    'cantidad' => $v->cantidad ?? 0,
                    'unidad' => $v->inventario->unidad_medida ?? '',
                    'precio_unit' => $v->precio_venta ?? 0,
                    'observ' => $v->observaciones ?? null,
                ];
            });

        // ahora almacenamos todos los movimientos en un array
        $ultimosMovimientos = $compras->concat($ventas)->sortByDesc('fecha')->take(10)->values();
        //con cancat() concatenamos los arrays y con sortByDesc() los ordenamos por fecha de forma descendente y con take(10) los limitamos a 10 ya con values() lo pasamos a array

        // Crear un array con los materiales que el Punto ECA tiene
        $materialesPunto = Inventario::query()->where('punto_eca_id', $puntoEcaId)->with('material:id,nombre')->get()->pluck('material')->filter()->unique('id')->sortBy('nombre')->values();
        // con pluck() obtenemos los materiales que el Punto ECA tiene y con unique() eliminamos los duplicados y con sortBy() los ordenamos por nombre y con values() lo pasamos a array

        // Filtros para el Historial Compras
        $hc = $request->validate([
            'hc_desde' => ['nullable', 'date'],
            'hc_hasta' => ['nullable', 'date'],
            'hc_material' => ['nullable', 'uuid', 'exists:materiales,id'],
        ]);
        $hcHasta = !empty($hc['hc_hasta']) ? Carbon::parse($hc['hc_hasta'])->endOfDay() : null;
        //verificamos que dentro del array hc, la fecha hasta no este vacia, y con la clase Carbon lo pasamos a una fecha
        // con endOfDay() le agregamos la hora 23:59:59 para que a la hora de comparar con la fecha hasta, se incluya el dia completo

        //contruimos la consulta para el Historial Compras utilizando los filtros
        $histCompras = Compra::query()
            //seleccionamos los campos que queremos mostrar
            ->with(['inventario:id,unidad_medida,material_id,precio_compra', 'inventario.material:id,nombre'])
            ->whereHas('inventario', fn($q) => $q->where('punto_eca_id', $puntoEcaId))
            ->when($hc['hc_material'] ?? null, fn($q, $matId) => $q->whereHas('inventario', fn($qi) => $qi->where('material_id', $matId)))
            //con fn($q, matId) le pasamos la consulta y el id del material, luego con $q whereHas() le decimos que busque en el inventario el material con el id que le pasamos y con where('material_id', $matId) le decimos que busque el material con el id que le pasamos
            ->when($hc['hc_desde'] ?? null, fn($q, $desde) => $q->whereDate('fecha', '>=', $desde))
            //con when() le decimos que si el filtro desde no esta vacio, entonces le decimos que busque la fecha que le pasamos y con whereDate() le decimos que busque la fecha que le pasamos
            ->when($hcHasta, fn($q) => $q->where('fecha', '<=', $hcHasta))
            // con when() le decimos que si la fecha hasta no esta vacia, entonces le decimos que busque la fecha hasta y con where() le decimos que busque la fecha hasta
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

        $histVentas = Venta::query()
            ->with(['inventario:id,unidad_medida,material_id', 'inventario.material:id,nombre'])
            ->whereHas('inventario', fn($q) => $q->where('punto_eca_id', $puntoEcaId))
            ->when($hs['hs_material'] ?? null, fn($q, $matId) => $q->whereHas('inventario', fn($qi) => $qi->where('material_id', $matId)))
            ->when($hs['hs_desde'] ?? null, fn($q, $desde) => $q->whereDate('fecha', '>=', $desde))
            ->when($hsHasta, fn($q) => $q->where('fecha', '<=', $hsHasta))
            ->latest('fecha')
            ->paginate(10)
            ->withQueryString();

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

        // Listas para selecionar
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

        //Calendario

        //traemmos el año y mes actual y lo convertimos a entero
        $y = intval($request->query('y', now()->year));
        $m = intval($request->query('m', now()->month));

        //calculamos el primer dia del mes
        $firstOfMonth = Carbon::create($y, $m, 1)->startOfDay();
        //calculamos el dia de la semana del primer dia del mes
        $dow = $firstOfMonth->dayOfWeekIso;
        //calculamos la fecha del lunes
        $start = $firstOfMonth
            //copiamos la fecha
            ->copy()
            //le restamos el dia de la semana para llegar al lunes
            ->subDays($dow - 1)
            //le ponemos la hora
            ->startOfDay(); // Lunes
        //calculamos la fecha del domingo
        $end = $start->copy()->addDays(41)->endOfDay(); // 42 días
        //calculamos el ultimo dia del mes

        //traemos el mes anterior y le sumamos 1 mes para cubrir los dias que se puedan en el mes
        $baseStart = $start->copy()->subMonthNoOverflow()->startOfDay(); // cubrir mensuales que “caen”

        //Construimos la consulta de la programación y treemmos los datos
        $rows = DB::table('programacion_recoleccion as pr')
            //filtramos por punto eca
            ->where('pr.punto_eca_id', $puntoEcaId)
            //filtramos por fechas desde el mes anterior hasta el ultimo dia que muestra el calendario
            ->whereBetween('pr.fecha', [$baseStart->toDateString(), $end->toDateString()])
            ->leftJoin('materiales as m3', 'm3.id', '=', 'pr.material_id')
            ->leftJoin('centros_acopio as ca', 'ca.id', '=', 'pr.centro_acopio_id')
            ->orderBy('pr.fecha')
            ->get(['pr.fecha', 'pr.hora', 'pr.frecuencia', 'pr.notas', DB::raw('COALESCE(pr.creado, NULL) as creado'), 'm3.nombre  as material_nombre', 'ca.nombre  as centro_nombre']);
        //Con raw() le indicamos que el campo creado se llame creado

        //creamos un array donde la clave sera la fecha y tremos los eventos asociados a ese dia
        $eventos = [];
        //recorremos la consulta
        foreach ($rows as $r) {
            //convertimos la fecha y la inicializamos con la hora
            $c = Carbon::parse($r->fecha)->startOfDay();

            $push = function (Carbon $d) use (&$eventos, $r, $start, $end, $punto) {
                // verificamos si la fecha esta dentro del rango de la
                if (!$d->betweenIncluded($start, $end)) {
                    //si no esta no agrega nada y se sale
                    return;
                }

                //formato de la fecha de la programación en el calendario para usarla como clave en el evento
                $key = $d->toDateString();
                //si la fecha tiene hora le sacamos los segundos y si no la dejamos null
                $hhmm = $r->hora ? Str::of($r->hora)->limit(5, '')->__toString() : null;

                $event = [
                    //traemos los datos de la programación con el material y el centro
                    'punto' => $punto->nombre ?? '—',
                    'material' => $r->material_nombre,
                    'centro' => $r->centro_nombre,
                    'fecha' => $key,
                    'hora' => $r->hora,
                    'time' => $hhmm,
                    'frecuencia' => $r->frecuencia,
                    'freq' => $r->frecuencia,
                    'notas' => $r->notas,
                    'notes' => $r->notas,
                    'creado' => $r->creado,
                ];

                //si no hay eventos para la fecha lo dejamos vacio
                $eventos[$key] = $eventos[$key] ?? [];
                //agregamos el evento con la fecha
                $eventos[$key][] = $event;
            };

            //ejecutamos la funcion
            $push($c);

            // repeticiones
            // si la frecuencia no es manual ni unico tenemos que hacer las repeticiones
            if (!in_array($r->frecuencia, ['manual', 'unico'], true)) {
                // un cursor para ir sumando periodos
                $cursor = $c->copy();
                while (true) {
                    switch ($r->frecuencia) {
                        case 'semanal':
                            $cursor->addWeek();
                            break;
                        case 'quincenal':
                            $cursor->addDays(14);
                            break;
                        case 'mensual':
                            $cursor->addMonthNoOverflow();
                            break;
                        default:
                            $cursor->addYears(100);
                    }
                    //con gt() verificamos si el cursor es mayor al final
                    if ($cursor->gt($end)) {
                        break;
                    }
                    //si no es mayor lo agregamos
                    $push($cursor);
                }
            }
        }

        // Construir 42 días para el calendario
        $dias = [];
        $iter = $start->copy();
        //recorremos 42 dias que son los que muestra el calendario
        for ($i = 0; $i < 42; $i++) {
            $key = $iter->toDateString();
            //convertimos la fecha de la programación en el calendario para usarla como clave en el evento
            //creamos un array con los eventos de la fecha
            $items = collect($eventos[$key] ?? [])
                //ordenamos los eventos por hora
                ->sortBy(fn($e) => $e['time'] ?? '')
                //obtenemos los eventos de la fecha
                ->values()
                ->all();

            $dias[] = [
                //creamos un array con la fecha y los eventos
                'date' => $iter->copy(),
                'events' => $items,
                //verificamos si la fecha es de este mes
                'inMonth' => $iter->month === $firstOfMonth->month,
            ];
            $iter->addDay();
        }

        // Navegación del calendario
        // Prev/Next mes
        $prev = $firstOfMonth->copy()->subMonthNoOverflow();
        $next = $firstOfMonth->copy()->addMonthNoOverflow();

        //construimos la url para la navegación
        $navPrevUrl = $request->fullUrlWithQuery(['seccion' => 'calendario', 'y' => $prev->year, 'm' => $prev->month]);
        $navNextUrl = $request->fullUrlWithQuery(['seccion' => 'calendario', 'y' => $next->year, 'm' => $next->month]);
        //Volvemos la fecha en en texto para el título y hacemos la primera letra en mayúscula
        $mesTitulo = Str::ucfirst($firstOfMonth->translatedFormat('F Y'));
        //Rango de fechas
        $rangoLabel = $start->toDateString() . ' → ' . $end->toDateString();

        // sel = fecha seleccionada
        $payload['sel'] = $request->query('sel');

        // Límites de mes actual (para Entradas/Salidas del mes)
        $inicioMes = Carbon::now()->startOfMonth()->toDateString();
        $finMes = Carbon::now()->endOfMonth()->toDateString();

        // Inventario total (kg) = suma de stock_actual del punto
        $kpiInventario = (float) Inventario::query()->where('punto_eca_id', $puntoEcaId)->sum('stock_actual');

        // Entradas del mes (kg) = suma de cantidad en Compras del mes el punto
        $kpiEntradasMes = (float) Compra::query()
            ->whereHas('inventario', fn($q) => $q->where('punto_eca_id', $puntoEcaId))
            ->whereBetween('fecha', [$inicioMes, $finMes])
            ->sum('cantidad');

        // Salidas del mes (kg) = suma de cantidad en Ventas para el punto
        $kpiSalidasMes = (float) Venta::query()
            ->whereHas('inventario', fn($q) => $q->where('punto_eca_id', $puntoEcaId))
            ->whereBetween('fecha', [$inicioMes, $finMes])
            ->sum('cantidad');

        // Próximo despacho (si tiene ProgramacionRecoleccion)
        $kpiProximoDespacho = null;
        $proximo = ProgramacionRecoleccion::query()
            ->with(['material:id,nombre', 'centroAcopio:id,nombre'])
            ->where('punto_eca_id', $puntoEcaId)
            ->whereDate('fecha', '>=', Carbon::today()->toDateString())
            ->orderBy('fecha')
            ->orderBy('hora')
            ->first();

        // Si hay un próximo despacho
        if ($proximo) {
            // Formateamos la fecha
            $fecha = $proximo->fecha instanceof Carbon ? $proximo->fecha->format('Y-m-d') : (string) $proximo->fecha;

            // Formateamos el próximo despacho
            $kpiProximoDespacho = sprintf('%s %s • %s • %s', $fecha, $proximo->hora ?? '', $proximo->material->nombre ?? '—', $proximo->centroAcopio->nombre ?? '—');
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
            // si hay umbral critico
            if (!is_null($it->umbral_critico) && $it->stock_actual <= $it->umbral_critico) {
                // si el stock es menor o igual al umbral critico
                $alertas[] = [
                    'tipo' => 'crítico',
                    'texto' => "Stock CRÍTICO de {$nombre}: {$it->stock_actual} {$it->unidad_medida} (≤ {$it->umbral_critico})",
                ];
                continue;
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

        // AJUSTES / CONFIGURACIÓN ===
        $usuario = Auth::user();
        $payload['config'] = [
            'mostrar_mapa' => (bool) ($punto->mostrar_mapa ?? false),
            'recibir_notificaciones' => (bool) ($usuario->recibe_notificaciones ?? false),
        ];
        // Mezcla al payload
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

            'centrosGlobales' => $centrosGlobales,
            'centrosPropios' => $centrosPropios,
            'centrosGlobalesLista' => $centrosGlobalesLista,
            'centrosPropiosLista' => $centrosPropiosLista,

            'centrosglobaleslista' => $centrosGlobalesLista,
            'centrospropioslista' => $centrosPropiosLista,

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
        $punto = DB::table('puntos_eca')->select('id', 'gestor_id')->where('gestor_id', Auth::id())->first();
        $puntoEcaId = $punto->id;

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

        return view('eca.index', ['seccion' => 'materiales'])->with('ok', 'Material registrado en inventario.');
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

        return view('eca.index', ['seccion' => 'materiales'])->with('ok', 'Inventario actualizado.');
    }

    public function destroyInventario(Inventario $inventario)
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
