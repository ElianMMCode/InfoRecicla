<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use App\Models\PuntoEca;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    public function indexAdmin(Request $request)
    {
        $totalUsuarios   = Usuario::count();
        $totalAdmins     = Usuario::where('rol', 'Administrador')->count();
        $totalGestores   = Usuario::where('rol', 'GestorECA')->count();
        $totalCiudadanos = Usuario::where('rol', 'Ciudadano')->count();
        $totalEcas       = PuntoEca::count();

        $ultimos = Usuario::latest('creado')->limit(10)->get();

        return view('Administracion.administrador', compact(
            'totalUsuarios',
            'totalAdmins',
            'totalGestores',
            'totalCiudadanos',
            'totalEcas',
            'ultimos'
        ));
    }

    /*USUARIOS: LISTADO*/
    public function usuariosIndex(Request $request)
    {
        $q      = $request->input('q');
        $rol    = $request->input('rol');
        $estado = $request->input('estado');

        $usuarios = Usuario::query()
            ->when($q, fn($q2) => $q2->where(function ($w) use ($q) {
                $w->where('nombre', 'like', "%$q%")
                    ->orWhere('apellido', 'like', "%$q%")
                    ->orWhere('correo', 'like', "%$q%");
            }))
            ->when($rol, fn($q2) => $q2->where('rol', $rol))
            ->when($estado, fn($q2) => $q2->where('estado', $estado))
            ->orderBy('creado', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('Administracion.administrador', compact('usuarios'));
    }

    /*USUARIOS: CREAR*/
    public function usuariosStore(Request $request)
    {
        $data = $request->validate([
            'rol'               => ['required', 'in:Administrador,GestorECA,Ciudadano'],
            'correo'            => ['required', 'email', 'max:255', 'unique:usuarios,correo'],
            'password'          => ['required', 'string', 'min:8'],
            'nombre'            => ['required', 'string', 'max:100'],
            'apellido'          => ['required', 'string', 'max:100'],
            'estado'            => ['nullable', 'in:activo,inactivo,bloqueado'],
            'genero'            => ['nullable', 'in:Masculino,Femenino,Otro'],
            'tipo_documento'    => ['nullable', 'string', 'max:30'],
            'numero_documento'  => ['nullable', 'string', 'max:30'],
            'telefono'          => ['nullable', 'string', 'max:30'],
            'fecha_nacimiento'  => ['nullable', 'date'],
            'nombre_usuario'    => ['nullable', 'string', 'max:60'],
            'avatar_url'        => ['nullable', 'url'],
        ]);

        $payload = array_merge($data, [
            'id'          => (string) Str::uuid(),
            'password'    => Hash::make($data['password']),
            'estado'      => $data['estado'] ?? 'activo',
            'creado'      => now(),
            'actualizado' => now(),
        ]);

        DB::transaction(fn() => Usuario::create($payload));

        return redirect()->route('admin.dashboard')->with('ok', 'Usuario creado correctamente');
    }

    /*USUARIOS: EDITAR*/
    public function usuariosEdit(string $id)
    {
        $usuario = Usuario::findOrFail($id);
        // Vista nueva en Administracion/
        return view('Administracion.usuario_edit', compact('usuario'));
    }

    /*USUARIOS: ACTUALIZAR*/
    public function usuariosUpdate(Request $request, string $id)
    {
        $usuario = Usuario::findOrFail($id);

        $data = $request->validate([
            'rol'               => ['required', 'in:Administrador,GestorECA,Ciudadano'],
            'correo'            => ['required', 'email', 'max:255', Rule::unique('usuarios', 'correo')->ignore($usuario->id)],
            'password'          => ['nullable', 'string', 'min:8', 'confirmed'],
            'nombre'            => ['required', 'string', 'max:100'],
            'apellido'          => ['required', 'string', 'max:100'],
            'estado'            => ['nullable', 'in:activo,inactivo,bloqueado'],
            'genero'            => ['nullable', 'in:Masculino,Femenino,Otro'],
            'tipo_documento'    => ['nullable', 'string', 'max:30'],
            'numero_documento'  => ['nullable', 'string', 'max:30'],
            'telefono'          => ['nullable', 'string', 'max:30'],
            'fecha_nacimiento'  => ['nullable', 'date'],
            'nombre_usuario'    => ['nullable', 'string', 'max:60'],
            'avatar_url'        => ['nullable', 'url'],
        ]);

        DB::transaction(function () use ($usuario, $data) {
            $usuario->fill($data);
            if (!empty($data['password'])) {
                $usuario->password = Hash::make($data['password']);
            }
            $usuario->actualizado = now();
            $usuario->save();
        });

        return redirect()->route('admin.usuarios.index')->with('ok', 'Usuario actualizado');
    }

    /*USUARIOS: ELIMINAR*/
    public function usuariosDestroy(string $id)
    {
        $usuario = Usuario::findOrFail($id);
        $usuario->delete();
        return back()->with('ok', 'Usuario eliminado');
    }

    /* ECAS: formularios */
    public function ecaStore(Request $request)
    {
        $data = $request->validate([
            'nombre'            => ['required', 'string', 'max:120'],
            'gestor_id'         => ['nullable', 'exists:usuarios,id'],
            'descripcion'       => ['nullable', 'string'],
            'direccion'         => ['nullable', 'string', 'max:200'],
            'telefonoPunto'     => ['nullable', 'string', 'max:30'],
            'correoPunto'       => ['nullable', 'email', 'max:255'],
            'ciudad'            => ['nullable', 'string', 'max:100'],
            'localidad'         => ['nullable', 'string', 'max:100'],
            'latitud'           => ['nullable', 'numeric'],
            'longitud'          => ['nullable', 'numeric'],
            'nit'               => ['nullable', 'string', 'max:30'],
            'horario_atencion'  => ['nullable', 'string', 'max:120'],
            'sitio_web'         => ['nullable', 'url'],
            'logo_url'          => ['nullable', 'url'],
            'foto_url'          => ['nullable', 'url'],
            'estado'            => ['nullable', 'in:activo,inactivo,bloqueado'],
        ]);

        $data['estado'] = $data['estado'] ?? 'activo';

        DB::transaction(fn() => PuntoEca::create([
            // mapea campos
            ...$data,
            'telefono' => $data['telefonoPunto'] ?? null,
            'correo'   => $data['correoPunto'] ?? null,
        ]));

        return back()->with('ok', 'Punto ECA creado');
    }

    public function ecaUpdate(Request $request, string $id)
    {
        $punto = PuntoEca::findOrFail($id);

        $data = $request->validate([
            'nombre'            => ['required', 'string', 'max:120'],
            'gestor_id'         => ['nullable', 'exists:usuarios,id'],
            'descripcion'       => ['nullable', 'string'],
            'direccion'         => ['nullable', 'string', 'max:200'],
            'telefonoPunto'     => ['nullable', 'string', 'max:30'],
            'correoPunto'       => ['nullable', 'email', 'max:255'],
            'ciudad'            => ['nullable', 'string', 'max:100'],
            'localidad'         => ['nullable', 'string', 'max:100'],
            'latitud'           => ['nullable', 'numeric'],
            'longitud'          => ['nullable', 'numeric'],
            'nit'               => ['nullable', 'string', 'max:30'],
            'horario_atencion'  => ['nullable', 'string', 'max:120'],
            'sitio_web'         => ['nullable', 'url'],
            'logo_url'          => ['nullable', 'url'],
            'foto_url'          => ['nullable', 'url'],
            'estado'            => ['nullable', 'in:activo,inactivo,bloqueado'],
        ]);

        DB::transaction(function () use ($punto, $data) {
            $punto->fill([
                ...$data,
                'telefono' => $data['telefonoPunto'] ?? null,
                'correo'   => $data['correoPunto'] ?? null,
            ]);
            $punto->save();
        });

        return back()->with('ok', 'Punto ECA actualizado');
    }

    public function ecaDestroy(string $id)
    {
        $punto = PuntoEca::findOrFail($id);
        $punto->delete();
        return back()->with('ok', 'Punto ECA eliminado');
    }
}
