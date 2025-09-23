<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\CentroAcopio;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CentroAcopioController extends Controller
{
    // data centros
    public function data(Request $request): array
    {
        $punto = DB::table('puntos_eca')->select('id', 'gestor_id')->where('gestor_id', Auth::id())->first();
        $puntoEcaId = $punto->id;

        $f = $request->validate([
            'f_nombre' => ['nullable', 'string', 'max:150'],
            'f_tipo' => ['nullable', Rule::in(['Planta', 'Proveedor', 'Otro'])],
            'f_estado' => ['nullable', Rule::in(['activo', 'inactivo', 'bloqueado'])],
            'f_localidad' => ['nullable', 'string', 'max:60'],
            'f_materiales' => ['nullable', 'array'],
            'f_materiales.*' => ['uuid', 'exists:materiales,id'],
        ]);

        $apply = function ($q) use ($f) {
            return $q
                ->when($f['f_nombre'] ?? null, fn($qq, $v) => $qq->where('nombre', 'like', "%{$v}%"))
                ->when($f['f_tipo'] ?? null, fn($qq, $v) => $qq->where('tipo', $v))
                ->when($f['f_estado'] ?? null, fn($qq, $v) => $qq->where('estado', $v))
                ->when($f['f_localidad'] ?? null, fn($qq, $v) => $qq->where('localidad', 'like', "%{$v}%"))
                ->when(!empty($f['f_materiales']), function ($qq) use ($f) {
                    $qq->whereHas('materiales', fn($qm) => $qm->whereIn('materiales.id', $f['f_materiales']));
                });
        };

        $centrosGlobales = CentroAcopio::with(['materiales:id,nombre'])
            ->where('alcance', 'global')
            ->tap($apply)
            ->orderBy('nombre')
            ->paginate(10, ['*'], 'page_cg')
            ->withQueryString();

        $centrosPropios = CentroAcopio::with(['materiales:id,nombre'])
            ->where('alcance', 'eca')
            ->where('owner_punto_eca_id', $puntoEcaId)
            ->tap($apply)
            ->orderBy('nombre')
            ->paginate(10, ['*'], 'page_cp')
            ->withQueryString();

        // materiales punto
        $materialesPunto = DB::table('inventario as i')
            ->join('materiales as m', 'm.id', '=', 'i.material_id')
            ->where('i.punto_eca_id', $puntoEcaId)
            ->select('m.id', 'm.nombre')
            ->distinct()
            ->orderBy('m.nombre')
            ->get();

        return [
            'centrosGlobales' => $centrosGlobales,
            'centrosPropios' => $centrosPropios,
            'materialesPunto' => $materialesPunto,
        ];
    }
    public function storeCentro(Request $request)
    {
        $user = Auth::user();

        $punto = DB::table('puntos_eca')->select('id', 'gestor_id')->where('gestor_id', $user->id)->first();

        // id punto
        $puntoEcaId = $punto->id;

        $data = $request->validate([
            'cac.nombre' => ['required', 'string', 'max:150'],
            'cac.tipo' => ['required', Rule::in(['Planta', 'Proveedor', 'Otro'])],
            'cac.descripcion' => ['nullable', 'string', 'max:255'],
            'cac.telefono' => ['nullable', 'string', 'max:20'],
            'cac.correo' => ['nullable', 'email', 'max:120'],
            'cac.sitio_web' => ['nullable', 'url', 'max:200'],
            'cac.horario_atencion' => ['nullable', 'string', 'max:150'],
            'cac.materiales' => ['nullable', 'array'],
            'cac.materiales.*' => ['uuid', 'exists:materiales,id'],
            'cac.direccion' => ['nullable', 'string', 'max:200'],
            'cac.localidad' => ['nullable', 'string', 'max:60'],
            'cac.latitud' => ['nullable', 'numeric', 'between:-90,90'],
            'cac.longitud' => ['nullable', 'numeric', 'between:-180,180'],
            'cac.notas' => ['nullable', 'string', 'max:300'],
            'cac.estado' => ['nullable', Rule::in(['activo', 'inactivo'])],
        ]);

        $cac = $data['cac'];

        $centro = CentroAcopio::create([
            'id' => (string) Str::uuid(),
            'nombre' => $cac['nombre'],
            'tipo' => $cac['tipo'],
            'alcance' => 'eca',
            'owner_punto_eca_id' => $puntoEcaId,
            // si no llega nada se deja null
            'contacto' => null,
            'telefono' => $cac['telefono'] ?? null,
            'correo' => $cac['correo'] ?? null,
            'sitio_web' => $cac['sitio_web'] ?? null,
            'horario_atencion' => $cac['horario_atencion'] ?? null,
            'direccion' => $cac['direccion'] ?? null,
            'ciudad' => null,
            'localidad' => $cac['localidad'] ?? null,
            'latitud' => $cac['latitud'] ?? null,
            'longitud' => $cac['longitud'] ?? null,
            // se deja activo
            'estado' => $cac['estado'] ?? 'activo',
            'notas' => $cac['notas'] ?? null,
            'descripcion' => $cac['descripcion'] ?? null,
        ]);

        //
        // sync materiales
        $centro->materiales()->sync($cac['materiales'] ?? []);

        // validar update
        $data = $request->validate([
            'cac.nombre' => ['required', 'string', 'max:150'],
            'cac.tipo' => ['required', Rule::in(['Planta', 'Proveedor', 'Otro'])],
            'cac.descripcion' => ['nullable', 'string', 'max:255'],
            'cac.telefono' => ['nullable', 'string', 'max:20'],
            'cac.correo' => ['nullable', 'email', 'max:120'],
            'cac.sitio_web' => ['nullable', 'url', 'max:200'],
            'cac.horario_atencion' => ['nullable', 'string', 'max:150'],
            'cac.direccion' => ['nullable', 'string', 'max:200'],
            'cac.localidad' => ['nullable', 'string', 'max:60'],
            'cac.latitud' => ['nullable', 'numeric', 'between:-90,90'],
            'cac.longitud' => ['nullable', 'numeric', 'between:-180,180'],
            'cac.notas' => ['nullable', 'string', 'max:300'],
            'cac.estado' => ['nullable', Rule::in(['activo', 'inactivo'])],
        ]);

        // ownership
        abort_if($centro->owner_punto_eca_id !== $punto->id, 403);

        // update centro
        $payload = $data['cac'];
        // contacto/cudad null
        $payload['contacto'] = null;
        $payload['ciudad'] = null;
        $centro->fill($payload);
        $centro->save();

        // sync opcional
        if (array_key_exists('materiales', $data['cac'])) {
            $centro->materiales()->sync($data['cac']['materiales'] ?? []);
        }

        return redirect()->route('eca.index', ['seccion' => 'centros'])
            ->with('ok', 'Centro de acopio creado.');
    }
    public function updateCentroAcopio(Request $request, CentroAcopio $centro)
    {
        $punto = DB::table('puntos_eca')->select('id', 'gestor_id')->where('gestor_id', Auth::id())->first();
        abort_if(!$punto, 404);

        $data = $request->validate([
            'cac.nombre' => ['required', 'string', 'max:150'],
            'cac.tipo' => ['required', Rule::in(['Planta', 'Proveedor', 'Otro'])],
            'cac.descripcion' => ['nullable', 'string', 'max:255'],
            'cac.contacto' => ['nullable', 'string', 'max:100'],
            'cac.telefono' => ['nullable', 'string', 'max:20'],
            'cac.correo' => ['nullable', 'email', 'max:120'],
            'cac.sitio_web' => ['nullable', 'url', 'max:200'],
            'cac.horario_atencion' => ['nullable', 'string', 'max:150'],
            'cac.direccion' => ['nullable', 'string', 'max:200'],
            'cac.ciudad' => ['nullable', 'string', 'max:60'],
            'cac.localidad' => ['nullable', 'string', 'max:60'],
            'cac.latitud' => ['nullable', 'numeric', 'between:-90,90'],
            'cac.longitud' => ['nullable', 'numeric', 'between:-180,180'],
            'cac.notas' => ['nullable', 'string', 'max:300'],
        ]);

        // fill
        $centro->fill($data['cac']);
        // save
        $centro->save();

        return redirect()->route('eca.index', ['seccion' => 'centros'])
            ->with('ok', 'Centro de acopio actualizado.');
    }

    public function destroyCentro(CentroAcopio $centro)
    {
        $punto = DB::table('puntos_eca')->select('id', 'gestor_id')->where('gestor_id', Auth::id())->first();
        abort_if(!$punto, 404);
        // validar eca
        if ($centro->alcance !== 'eca' || $centro->owner_punto_eca_id !== $punto->id) {
            return redirect()->route('eca.index', ['seccion' => 'centros'])
                ->with('error', 'No autorizado para eliminar este centro.');
        }

        // detach relaciones
        try {
            DB::transaction(function () use ($centro) {
                $centro->materiales()->detach();
                $centro->delete();
            });
            return redirect()->route('eca.index', ['seccion' => 'centros'])
                ->with('ok', 'Centro eliminado.');
        } catch (\Throwable $e) {
            return redirect()->route('eca.index', ['seccion' => 'centros'])
                ->with('error', 'No se pudo eliminar.');
        }
    }
}
