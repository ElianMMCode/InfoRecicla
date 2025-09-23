<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Inventario;

class InventarioController extends Controller
{
    // listado inventario
    public function index(Request $request)
    {
        //
        //
        $punto = DB::table('puntos_eca')->select('id', 'gestor_id')->where('gestor_id', Auth::id())->first();
        $puntoEcaId = $punto->id;

        // filtros
        $q = $request->validate([
            'q_categoria' => ['nullable', 'uuid', 'exists:categorias_material,id'],
            'q_tipo' => ['nullable', 'uuid', 'exists:tipos_material,id'],
            'q_nombre' => ['nullable', 'string', 'max:120'],
        ]);

        $inventario = Inventario::query()
            ->with(['material:id,nombre,categoria_id,tipo_id', 'material.categoria:id,nombre', 'material.tipo:id,nombre'])
            ->when($puntoEcaId, fn($q2) => $q2->where('punto_eca_id', $puntoEcaId))
            ->when($q['q_categoria'] ?? null, fn($q2, $v) => $q2->whereHas('material', fn($qq) => $qq->where('categoria_id', $v)))
            ->when($q['q_tipo'] ?? null, fn($q2, $v) => $q2->whereHas('material', fn($qq) => $qq->where('tipo_id', $v)))
            ->when($q['q_nombre'] ?? null, fn($q2, $v) => $q2->whereHas('material', fn($qq) => $qq->where('nombre', 'like', "%{$v}%")))
            ->orderByDesc('creado')
            ->paginate(6)
            ->withQueryString();

        return view('PuntoECA.partials.inventario-listado', compact('inventario', 'puntoEcaId', 'q'));
    }

    // create
    public function create()
    {
        //
    }

    // store
    public function store(Request $request)
    {
        //
        $punto = DB::table('puntos_eca')->select('id', 'gestor_id')->where('gestor_id', Auth::id())->first();
        $puntoEcaId = $punto?->id;

        $data = $request->validate(
            [
                'material_id' => ['required', 'uuid', 'exists:materiales,id', Rule::unique('inventario')->where(fn($q) => $q->where('punto_eca_id', $puntoEcaId))],
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

        $seccion = 'materiales';

        return redirect()->route('eca.index', ['seccion' => 'materiales'])
            ->with('ok', 'Material creado correctamente');
    }

    // show
    public function show(string $id)
    {
        //
    }

    // edit
    public function edit(string $id)
    {
        //
    }

    // update
    public function update(Request $request, Inventario $inventario)
    {
        $punto = DB::table('puntos_eca')->select('id', 'gestor_id')->where('gestor_id', Auth::id())->first();
        $puntoEcaId = $punto?->id;

        // validar ownership
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

        return redirect()->route('eca.index', ['seccion' => 'materiales'])
            ->with('ok', 'Inventario actualizado.');
    }

    // destroy
    public function destroy(Request $request, Inventario $inventario)
    {
        $punto = DB::table('puntos_eca')->select('id', 'gestor_id')->where('gestor_id', Auth::id())->first();
        $puntoEcaId = $punto?->id;

        if ($inventario->punto_eca_id !== $puntoEcaId) {
            abort(403, 'No autorizado.');
        }

        $inventario->delete();
        return redirect()->route('eca.index', ['seccion' => 'materiales'])
            ->with('ok', 'Material eliminado correctamente');
    }
}
