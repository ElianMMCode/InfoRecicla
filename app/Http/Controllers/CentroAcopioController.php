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
    public function storeCentro(Request $request)
    {
        $punto = DB::table('puntos_eca')
            ->select('id', 'gestor_id')
            ->where('gestor_id', Auth::id())
            ->first();
        abort_if(!$punto, 404);

        $puntoEcaId = $punto->id; // UUID del punto (char 36)

        $data = $request->validate([
            'cac.nombre'            => ['required', 'string', 'max:150'],
            'cac.tipo'              => ['required', Rule::in(['Planta', 'Proveedor', 'Otro'])],
            'cac.descripcion'       => ['nullable', 'string', 'max:255'],
            'cac.contacto'          => ['nullable', 'string', 'max:100'],
            'cac.telefono'          => ['nullable', 'string', 'max:20'],
            'cac.correo'            => ['nullable', 'email', 'max:120'],
            'cac.sitio_web'         => ['nullable', 'url', 'max:200'],
            'cac.horario_atencion'  => ['nullable', 'string', 'max:150'],
            'cac.materiales'          => ['nullable', 'array'],
            'cac.materiales.*'        => ['uuid', 'exists:materiales,id'],
            'cac.direccion'         => ['nullable', 'string', 'max:200'],
            'cac.ciudad'            => ['nullable', 'string', 'max:60'],
            'cac.localidad'         => ['nullable', 'string', 'max:60'],
            'cac.latitud'           => ['nullable', 'numeric', 'between:-90,90'],
            'cac.longitud'          => ['nullable', 'numeric', 'between:-180,180'],
            'cac.notas'             => ['nullable', 'string', 'max:300'],
        ]);

        $cac = $data['cac'];

        $centro = CentroAcopio::create([
            'id'                  => (string) Str::uuid(),
            'nombre'              => $cac['nombre'],
            'tipo'                => $cac['tipo'],
            'alcance'             => 'eca',
            'owner_punto_eca_id'  => $puntoEcaId,
            'contacto'            => $cac['contacto']         ?? null,
            'telefono'            => $cac['telefono']         ?? null,
            'correo'              => $cac['correo']           ?? null,
            'sitio_web'           => $cac['sitio_web']        ?? null,
            'horario_atencion'    => $cac['horario_atencion'] ?? null,
            'direccion'           => $cac['direccion']        ?? null,
            'ciudad'              => $cac['ciudad']           ?? null,
            'localidad'           => $cac['localidad']        ?? null,
            'latitud'             => $cac['latitud']          ?? null,
            'longitud'            => $cac['longitud']         ?? null,
            'estado'              => 'activo',
            'notas'               => $cac['notas']            ?? null,
            'descripcion'         => $cac['descripcion']      ?? null,
        ]);

        $centro->materiales()->sync($cac['materiales'] ?? []);

        $data = $request->validate([
            'cac.nombre'            => ['required', 'string', 'max:150'],
            'cac.tipo'              => ['required', Rule::in(['Planta', 'Proveedor', 'Otro'])],
            'cac.descripcion'       => ['nullable', 'string', 'max:255'],
            'cac.contacto'          => ['nullable', 'string', 'max:100'],
            'cac.telefono'          => ['nullable', 'string', 'max:20'],
            'cac.correo'            => ['nullable', 'email', 'max:120'],
            'cac.sitio_web'         => ['nullable', 'url', 'max:200'],
            'cac.horario_atencion'  => ['nullable', 'string', 'max:150'],
            'cac.direccion'         => ['nullable', 'string', 'max:200'],
            'cac.ciudad'            => ['nullable', 'string', 'max:60'],
            'cac.localidad'         => ['nullable', 'string', 'max:60'],
            'cac.latitud'           => ['nullable', 'numeric', 'between:-90,90'],
            'cac.longitud'          => ['nullable', 'numeric', 'between:-180,180'],
            'cac.notas'             => ['nullable', 'string', 'max:300'],
            'cac.materiales'        => ['nullable', 'array'],
            'cac.materiales.*'      => ['uuid', 'exists:materiales,id'],
        ]);

        abort_if($centro->owner_punto_eca_id !== $punto->id, 403);

        $centro->fill($data['cac']);
        $centro->save();

        // ✅ sincroniza si vino el campo
        if (array_key_exists('materiales', $data['cac'])) {
            $centro->materiales()->sync($data['cac']['materiales'] ?? []);
        }


        return back()->with('ok', 'Centro de acopio creado.');
    }
    public function updateCentroAcopio(Request $request, CentroAcopio $centro)
    {
        $punto = DB::table('puntos_eca')
            ->select('id', 'gestor_id')
            ->where('gestor_id', Auth::id())
            ->first();
        abort_if(!$punto, 404);

        $data = $request->validate([
            'cac.nombre'            => ['required', 'string', 'max:150'],
            'cac.tipo'              => ['required', Rule::in(['Planta', 'Proveedor', 'Otro'])],
            'cac.descripcion'       => ['nullable', 'string', 'max:255'],
            'cac.contacto'          => ['nullable', 'string', 'max:100'],
            'cac.telefono'          => ['nullable', 'string', 'max:20'],
            'cac.correo'            => ['nullable', 'email', 'max:120'],
            'cac.sitio_web'         => ['nullable', 'url', 'max:200'],
            'cac.horario_atencion'  => ['nullable', 'string', 'max:150'],
            'cac.direccion'         => ['nullable', 'string', 'max:200'],
            'cac.ciudad'            => ['nullable', 'string', 'max:60'],
            'cac.localidad'         => ['nullable', 'string', 'max:60'],
            'cac.latitud'           => ['nullable', 'numeric', 'between:-90,90'],
            'cac.longitud'          => ['nullable', 'numeric', 'between:-180,180'],
            'cac.notas'             => ['nullable', 'string', 'max:300'],
        ]);


        $centro->fill($data['cac']);
        $centro->save();

        return back()->with('ok', 'Centro de acopio actualizado.');
    }
}
