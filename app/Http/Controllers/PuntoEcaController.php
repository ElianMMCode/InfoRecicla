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



class PuntoEcaController extends Controller


{
    protected $permitidas = ['resumen', 'perfil', 'materiales', 'movimientos', 'historial', 'calendario', 'centros', 'conversaciones', 'configuracion'];

    public function view_punto_eca(Request $request, $seccion = null)
    {
        // 1) Sección válida
        if ($seccion === null || !in_array($seccion, $this->permitidas, true)) {
            $seccion = 'resumen';
        }

<<<<<<< HEAD
        // 2) Punto ECA del gestor autenticado
        $punto = DB::table('puntos_eca')
            ->select(
                'id',
                'gestor_id',
                'nombre',
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
            ->first();

        if (!$punto) {
            abort(404, 'No se encontró Punto ECA para este usuario.');
        }

        $puntoEcaId = $punto->id;

        // Variables base que siempre pasas
        $payload = [
            'seccion'    => $seccion,
            'puntoEcaId' => $puntoEcaId,
            'punto'      => $punto,
        ];

        // ===== Catálogos para los <select>
        $categorias = CategoriaMaterial::orderBy('nombre')->get(['id', 'nombre']);
        $tipos      = TipoMaterial::orderBy('nombre')->get(['id', 'nombre']);

        // ===== Filtros del CATÁLOGO (arriba)
        $f = $request->validate([
            'categoria' => ['nullable', 'uuid', 'exists:categorias_material,id'],
            'tipo'      => ['nullable', 'uuid', 'exists:tipos_material,id'],
            'nombre'    => ['nullable', 'string', 'max:120'],
        ]);

        // Excluir materiales ya registrados en inventario del Punto
        $materialesYaRegistrados = Inventario::query()
            ->where('punto_eca_id', $puntoEcaId)
            ->pluck('material_id');

        // Catálogo de materiales (paginado)
        $materiales = Material::query()
            ->with(['categoria:id,nombre', 'tipo:id,nombre'])
            ->when($f['categoria'] ?? null, fn($q, $v) => $q->where('categoria_id', $v))
            ->when($f['tipo'] ?? null,      fn($q, $v) => $q->where('tipo_id', $v))
            ->when($f['nombre'] ?? null,    fn($q, $v) => $q->where('nombre', 'like', "%{$v}%"))
            ->whereNotIn('id', $materialesYaRegistrados)
            ->orderBy('nombre')
            ->paginate(6)
            ->withQueryString();
=======
        // Solo preparar datos si es la sección "materiales"
        // Punto ECA actual (ajusta a tu lógica real)
        $punto = DB::table('puntos_eca')
            ->select('id', 'gestor_id')
            ->where('gestor_id', Auth::id())
            ->first();
        $puntoEcaId = $punto->id;               // ====== CATÁLOGOS para los <select> ======
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

>>>>>>> f7eb6f5 (Creacion de login, actualizacion de registro y logout. Creacion distintos roles para la redireccion de cada usuario a su respectiva area, bloqueo de rutas por tipo de usuario y la integrasion de una sesion activa.)

        // ===== Filtros del INVENTARIO (abajo)
        $q = $request->validate([
            'q_categoria' => ['nullable', 'uuid', 'exists:categorias_material,id'],
            'q_tipo'      => ['nullable', 'uuid', 'exists:tipos_material,id'],
            'q_nombre'    => ['nullable', 'string', 'max:120'],
        ]);

        // Inventario del Punto (paginado)
        $inventario = Inventario::query()
            ->with([
                'material:id,nombre,categoria_id,tipo_id',
                'material.categoria:id,nombre',
                'material.tipo:id,nombre',
            ])
            ->where('punto_eca_id', $puntoEcaId)
            ->when($q['q_categoria'] ?? null, fn($q2, $v) => $q2->whereHas('material', fn($m) => $m->where('categoria_id', $v)))
            ->when($q['q_tipo'] ?? null,      fn($q2, $v) => $q2->whereHas('material', fn($m) => $m->where('tipo_id', $v)))
            ->when($q['q_nombre'] ?? null,    fn($q2, $v) => $q2->whereHas('material', fn($m) => $m->where('nombre', 'like', "%{$v}%")))
            // Si tienes columna personalizada "creado", usa esa; si no, latest():
            ->orderByDesc('creado') // ->latest() si no existe "creado"
            ->paginate(6)
            ->withQueryString();

        // ===== Últimos movimientos (solo si estás en la sección "movimientos")
        $ultimosMovimientos = collect();

        if ($seccion === 'movimientos') {
            // Relación esperada:
            // Compra::belongsTo(Inventario::class, 'inventario_id')
            // Venta::belongsTo(Inventario::class, 'inventario_id')
            // Inventario::belongsTo(Material::class, 'material_id')

            // Compras (top 10 por fecha)
            $compras = Compra::query()
                ->with(['inventario.material:id,nombre'])
                ->whereHas('inventario', fn($q2) => $q2->where('punto_eca_id', $puntoEcaId))
                ->latest('fecha') // o ->latest() si usas created_at
                ->take(10)
                ->get()
                ->map(function ($c) {
                    return [
                        'tipo'        => 'compra',
                        'fecha'       => $c->fecha instanceof \Illuminate\Support\Carbon ? $c->fecha->format('Y-m-d') : (string) $c->fecha,
                        'material'    => $c->inventario->material->nombre ?? '—',
                        'cantidad'    => $c->cantidad,
                        'unidad'      => $c->inventario->unidad_medida ?? '',
                        'precio_unit' => $c->precio_compra,
                        'observ'      => $c->observaciones ?? null,
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
                        'tipo'        => 'venta',
                        'fecha'       => $v->fecha instanceof \Illuminate\Support\Carbon ? $v->fecha->format('Y-m-d') : (string) $v->fecha,
                        'material'    => $v->inventario->material->nombre ?? '—',
                        'cantidad'    => $v->cantidad,
                        'unidad'      => $v->inventario->unidad_medida ?? '',
                        'precio_unit' => $v->precio_venta,
                        'observ'      => $v->observaciones ?? null,
                    ];
                });

            // Unir, ordenar y tomar top 10 en total
            $ultimosMovimientos = $compras
                ->concat($ventas)
                ->sortByDesc('fecha')
                ->take(10)
                ->values();
        }

        $materialesPunto = Inventario::query()
            ->where('punto_eca_id', $puntoEcaId)
            ->with('material:id,nombre')
            ->get()
            ->pluck('material')
            ->filter() // por si hay nulls
            ->unique('id')
            ->sortBy('nombre')
            ->values();

        // Filtros de COMPRAS (entradas)
        $hc = $request->validate([
            'hc_desde'    => ['nullable', 'date'],
            'hc_hasta'    => ['nullable', 'date'],
            'hc_material' => ['nullable', 'uuid', 'exists:materiales,id'],
        ]);
        // Normaliza hasta = fin del día para incluir la fecha completa
        $hcHasta = !empty($hc['hc_hasta']) ? Carbon::parse($hc['hc_hasta'])->endOfDay() : null;

        $histCompras = \App\Models\Compra::query()
            ->with(['inventario:id,unidad_medida,material_id', 'inventario.material:id,nombre'])
            ->whereHas('inventario', fn($q) => $q->where('punto_eca_id', $puntoEcaId))
            ->when(
                $hc['hc_material'] ?? null,
                fn($q, $matId) =>
                $q->whereHas('inventario', fn($qi) => $qi->where('material_id', $matId))
            )
            ->when(
                $hc['hc_desde'] ?? null,
                fn($q, $desde) =>
                $q->whereDate('fecha', '>=', $desde)
            )
            ->when(
                $hcHasta,
                fn($q) =>
                $q->where('fecha', '<=', $hcHasta)
            )
            ->latest('fecha')
            ->paginate(10)
            ->withQueryString();

        // Filtros de VENTAS (salidas)
        $hs = $request->validate([
            'hs_desde'    => ['nullable', 'date'],
            'hs_hasta'    => ['nullable', 'date'],
            'hs_material' => ['nullable', 'uuid', 'exists:materiales,id'],
        ]);
        $hsHasta = !empty($hs['hs_hasta']) ? Carbon::parse($hs['hs_hasta'])->endOfDay() : null;

        $histVentas = \App\Models\Venta::query()
            ->with(['inventario:id,unidad_medida,material_id', 'inventario.material:id,nombre'])
            ->whereHas('inventario', fn($q) => $q->where('punto_eca_id', $puntoEcaId))
            ->when(
                $hs['hs_material'] ?? null,
                fn($q, $matId) =>
                $q->whereHas('inventario', fn($qi) => $qi->where('material_id', $matId))
            )
            ->when(
                $hs['hs_desde'] ?? null,
                fn($q, $desde) =>
                $q->whereDate('fecha', '>=', $desde)
            )
            ->when(
                $hsHasta,
                fn($q) =>
                $q->where('fecha', '<=', $hsHasta)
            )
            ->latest('fecha')
            ->paginate(10)
            ->withQueryString();

        // Añade al payload
        $payload += [
            'materialesPunto' => $materialesPunto,
            'hc'              => $hc,
            'hs'              => $hs,
            'histCompras'     => $histCompras,
            'histVentas'      => $histVentas,
        ];

        // Mezcla al payload
        $payload += [
            'categorias'         => $categorias,
            'tipos'              => $tipos,
            'materiales'         => $materiales,
            'inventario'         => $inventario,
            'ultimosMovimientos' => $ultimosMovimientos,
        ];

        // 4) Render
        return view('PuntoECA.punto-eca', $payload);
    }



    /**
     * POST: Registrar un material en inventario (botón por fila).
     * Usa validación inline (puedes mover a FormRequest si prefieres).
     */
    public function storeInventario(Request $request)
    {
        // 1) Forzar el punto por defecto desde config/.env
        $punto = DB::table('puntos_eca')
            ->select('id', 'gestor_id')
            ->where('gestor_id', Auth::id())
            ->first();
        $puntoEcaId = $punto->id;
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
        $punto = DB::table('puntos_eca')
            ->select('id', 'gestor_id')
            ->where('gestor_id', Auth::id())
            ->first();
        $puntoEcaId = $punto->id;
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

    public function destroyInventario(Request $request, Inventario $inventario)
    {
        $punto = DB::table('puntos_eca')
            ->select('id', 'gestor_id')
            ->where('gestor_id', Auth::id())
            ->first();
        $puntoEcaId = $punto->id;
        if ($inventario->punto_eca_id !== $puntoEcaId) {
            abort(403, 'No autorizado.');
        }

        $inventario->delete();

        return back()->with('ok', 'Material eliminado del inventario.');
    }
}
