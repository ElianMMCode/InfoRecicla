<?php

namespace App\Http\Controllers;

use App\Models\CategoriaMaterial;
use App\Models\Material;
use App\Models\Inventario;
use App\Models\TipoMaterial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MaterialController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $punto = DB::table('puntos_eca')
            ->select('id', 'gestor_id')
            ->where('gestor_id', Auth::id())
            ->first();
        $puntoEcaId = $punto->id;

        $categorias = CategoriaMaterial::orderBy('nombre')->get('id', 'nombre');
        $tipos = TipoMaterial::orderBy('nombre')->get('id', 'nombre');

        //Filtros

        $f = $request->validate([
            'categoria' => ['nullable', 'uuid', 'exists:categorias_material,id'],
            'tipo'      => ['nullable', 'uuid', 'exists:tipos_material,id'],
            'nombre'    => ['nullable', 'string', 'max:120'],
        ]);


        $materialesYaRegistrados = Inventario::query()
            ->when($puntoEcaId, fn($q) => $q->where('punto_eca_id', $puntoEcaId))
            ->pluck('material_id');

        //Todos los materiales
        $materiales = Material::query()
            ->with(['categoria:id,nombre', 'tipo:id,nombre'])
            ->when($f['categoria'] ?? null, fn($q, $v) => $q->where('categoria_id', $v))
            ->when($f['tipo'] ?? null,      fn($q, $v) => $q->where('tipo_id', $v))
            ->when($f['nombre'] ?? null,    fn($q, $v) => $q->where('nombre', 'like', "%{$v}%"))
            ->when($puntoEcaId, fn($q) => $q->whereNotIn('id', $materialesYaRegistrados))
            ->orderBy('nombre')
            ->paginate(6)
            ->withQueryString();

        return view('PuntoECA.punto-eca', compact(
            'materiales',
            'categorias',
            'tipos',
            'puntoEcaId',
        ));
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
    public function store(Request $request)
    {
        //
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
