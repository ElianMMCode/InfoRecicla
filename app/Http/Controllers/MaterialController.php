<?php

namespace App\Http\Controllers;

use App\Models\CategoriaMaterial;
use App\Models\Material;
use App\Models\Inventario;
use App\Models\TipoMaterial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class MaterialController extends Controller
{
    // tabla materiales admin
    public function indexMateriales(Request $request)
    {
        $query = Material::query();
        if ($request->filled('nombre')) {
            $query->where('nombre', 'like', "%" . $request->input('nombre') . "%");
        }
        if ($request->filled('categoria')) {
            $query->where('categoria_id', $request->input('categoria'));
        }
        if ($request->filled('tipo')) {
            $query->where('tipo_id', $request->input('tipo'));
        }
        $materiales = $query->paginate(10)->appends($request->except('page'));
        $categorias = CategoriaMaterial::orderBy('nombre')->get(['id', 'nombre']);
        $tipos = TipoMaterial::orderBy('nombre')->get(['id', 'nombre']);
        return view('Administracion.administrador', compact('materiales', 'categorias', 'tipos'));
    }

    // data materiales
    public function data(Request $request): array
    {
        $punto = DB::table('puntos_eca')->select('id', 'gestor_id')->where('gestor_id', Auth::id())->first();
        $puntoEcaId = $punto->id;

        $categorias = CategoriaMaterial::orderBy('nombre')->get(['id', 'nombre']);
        $tipos = TipoMaterial::orderBy('nombre')->get(['id', 'nombre']);

        $f = $request->validate([
            'categoria' => ['nullable', 'uuid', 'exists:categorias_material,id'],
            'tipo' => ['nullable', 'uuid', 'exists:tipos_material,id'],
            'nombre' => ['nullable', 'string', 'max:120'],
        ]);

        $inventario = Inventario::query()
            ->where('punto_eca_id', $puntoEcaId)
            ->with(['material.categoria:id,nombre', 'material.tipo:id,nombre'])
            ->orderBy('creado')
            ->paginate(6)
            ->withQueryString();

        $materialesYaRegistrados = Inventario::query()
            ->when($puntoEcaId, fn($q) => $q->where('punto_eca_id', $puntoEcaId))
            ->pluck('material_id');

        $materiales = Material::query()
            ->with(['categoria:id,nombre', 'tipo:id,nombre'])
            ->when(($f['categoria'] ?? null), fn($q, $v) => $q->where('categoria_id', $v))
            ->when(($f['tipo'] ?? null), fn($q, $v) => $q->where('tipo_id', $v))
            ->when(($f['nombre'] ?? null), fn($q, $v) => $q->where('nombre', 'like', "%{$v}%"))
            ->when($puntoEcaId, fn($q) => $q->whereNotIn('id', $materialesYaRegistrados))
            ->orderBy('nombre')
            ->paginate(6)
            ->withQueryString();

        return [
            'inventario' => $inventario,
            'materiales' => $materiales,
            'categorias' => $categorias,
            'tipos' => $tipos,
            'puntoEcaId' => $puntoEcaId,

        ];
    }

    // store material
    public function storeMateriales(Request $request)
    {
        $rules = [
            'nombre'        => 'required|string|max:120|unique:materiales,nombre',
            'descripcion'   => 'required|string',
            'tipo_id'       => 'required|uuid|exists:tipos_material,id',
            'categoria_id'  => 'required|uuid|exists:categorias_material,id',
            'imagen_url'    => 'nullable|url',
        ];

        $data = $request->validate($rules);
        try {
            DB::transaction(function () use ($data) {
                Log::info('Creando material', $data);
                Material::create([
                    'id'           => (string)Str::uuid(),
                    'nombre'       => $data['nombre'],
                    'descripcion'  => $data['descripcion'],
                    'tipo_id'      => $data['tipo_id'],
                    'categoria_id' => $data['categoria_id'],
                    'imagen_url'   => $data['imagen_url'] ?? null,
                ]);
            });
        } catch (\Throwable $e) {
            Log::error('Error creando material: '.$e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return back()->withErrors(['material' => 'Error al crear material: '.$e->getMessage()])->withInput();
        }
        return back()->with('ok', 'Material creado');


        $categorias = CategoriaMaterial::orderBy('nombre')->get();
        $tipos = TipoMaterial::orderBy('nombre')->get();

        return view('Administracion.material_create', compact('categorias', 'tipos'));
    }

    public function createCategorias(Request $request)
    {
        $categorias = [
            'nombre' => 'required|string|max:120|unique:categorias_material,nombre',
        ];

        $dataCategorias = $request->validate($categorias);

        $payloadCategorias = [
            'id'     => (string)Str::uuid(),
            'nombre' => $dataCategorias['nombre'],
        ];

        DB::transaction(function () use ($payloadCategorias) {
            CategoriaMaterial::create($payloadCategorias);
        });
        return back()->with('ok', 'Categoría creada');

        $categorias = CategoriaMaterial::orderBy('nombre')->get();
        $tipos = TipoMaterial::orderBy('nombre')->get();

        return view('Administracion.material_create', compact('categorias', 'tipos'));
    }


    public function createTiposMateriales(Request $request)
    {
        $tipos = [
            'nombre'        => 'required|string|max:120|unique:tipos_material,nombre',
            'descripcion'   => 'required|string',
        ];

        $dataTipos = $request->validate($tipos);

        $payloadTipos = [
            'id'           => (string)Str::uuid(),
            'nombre'       => $dataTipos['nombre'],
            'descripcion'  => $dataTipos['descripcion'],

        ];

        DB::transaction(function () use ($payloadTipos) {
            TipoMaterial::create($payloadTipos);
        });
        return back()->with('ok', 'Tipo de material creado');


        $categorias = CategoriaMaterial::orderBy('nombre')->get();
        $tipos = TipoMaterial::orderBy('nombre')->get();

        return view('Administracion.material_create', compact('categorias', 'tipos'));
    }

    public function createInventario(Request $request)
    {
        $inventario = [
            'material_id' => 'required|uuid|exists:materiales,id',
            'cantidad'    => 'required|numeric|min:0',
            'valor_unitario' => 'nullable|numeric|min:0',
        ];

        $dataInventario = $request->validate($inventario);

        $punto = DB::table('puntos_eca')->select('id', 'gestor_id')->where('gestor_id', Auth::id())->first();
        $puntoEcaId = $punto->id;

        $payloadInventario = [
            'id'            => (string)Str::uuid(),
            'punto_eca_id' => $puntoEcaId,
            'material_id'  => $dataInventario['material_id'],
            'cantidad'     => $dataInventario['cantidad'],
            'valor_unitario' => $dataInventario['valor_unitario'] ?? null,
        ];

        DB::transaction(function () use ($payloadInventario) {
            Inventario::create($payloadInventario);
        });
        return back()->with('ok', 'Material agregado al inventario');
    }

    // show material
    public function show(string $id)
    {
        // detalle material
        $material = Material::with(['categoria', 'tipo'])->findOrFail($id);
        return view('Administracion.material_show', compact('material'));
    }

    public function showCategoria(string $id)
    {
        // detalle categoria
        $categoria = CategoriaMaterial::findOrFail($id);
        return view('Administracion.categoria_show', compact('categoria'));
    }

    // edit material
    public function materialesEdit(string $id)
    {
        $material = Material::findOrFail($id);

        return view('Administracion.material_edit', compact('material'));
    }

    public function categoriasEdit(string $id)
    {
        $categoria = CategoriaMaterial::findOrFail($id);

        return view('Administracion.categoria_edit', compact('categoria'));
    }

    public function tiposEdit(string $id)
    {
        $tipo = TipoMaterial::findOrFail($id);

        return view('Administracion.tipo_edit', compact('tipo'));
    }

    // update material
    public function materialesUpdate(Request $request, string $id)
    {
        $material = Material::findOrFail($id);

        $data = $request->validate([
            'nombre'        => ['required', 'string', 'max:120'],
            'categoria_id'  => ['required', 'exists:categorias_material,id'],
            'tipo_id'       => ['required', 'exists:tipos_material,id'],
            'descripcion'   => ['nullable', 'string'],
            'unidad_medida' => ['nullable', 'string', 'max:30'],
            'peso'          => ['nullable', 'numeric'],
            'volumen'       => ['nullable', 'numeric'],
            'color'         => ['nullable', 'string', 'max:30'],
            'materiales'    => ['nullable', 'string', 'max:120'],
            'imagen_url'    => ['nullable', 'url'],
            'estado'        => ['nullable', 'in:activo,inactivo,bloqueado'],
        ]);

        DB::transaction(function () use ($material, $data) {
            $material->fill($data);
            $material->save();
        });

        return back()->with('ok', 'Material actualizado');
    }

    public function categoriasUpdate(Request $request, string $id)
    {
        $categoria = CategoriaMaterial::findOrFail($id);
        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:120'],
            'descripcion' => ['required', 'string'],
        ]);
        DB::transaction(function () use ($categoria, $data) {
            $categoria->fill($data);
            $categoria->save();
        });
        return back()->with('ok', 'Categoría actualizada');
    }

    public function tiposUpdate(Request $request, string $id)
    {
        $tipo = TipoMaterial::findOrFail($id);
        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:120'],
            'descripcion' => ['required', 'string'],
        ]);
        DB::transaction(function () use ($tipo, $data) {
            $tipo->fill($data);
            $tipo->save();
        });
        return back()->with('ok', 'Tipo de material actualizado');
    }


    // destroy material
    public function materialesDestroy(string $id)
    {
        $material = Material::findOrFail($id);
        $material->delete();
        return back()->with('ok', 'Material eliminado');
    }

    public function categoriasDestroy(string $id)
    {
        $categoria = CategoriaMaterial::findOrFail($id);
        $categoria->delete();
        return back()->with('ok', 'Categoría eliminada');
    }

    public function tiposDestroy(string $id)
    {
        $tipo = TipoMaterial::findOrFail($id);
        $tipo->delete();
        return back()->with('ok', 'Tipo de material eliminado');
    }
}
