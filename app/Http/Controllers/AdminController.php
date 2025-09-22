<?php

namespace App\Http\Controllers;

use App\Models\PuntoEca;
use App\Models\Usuario;
use App\Models\Publicaciones;
use App\Models\Alertas;
use App\Models\Admin;
use App\Models\GestorECA;
use App\Models\Ciudadano;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;

class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function indexAdmin(Request $request)
    {
        // Mostrar Listado de Estadísticas

        $totalUsuarios  = Usuario::count();
        $totalAdmins   = Admin::where('rol', 'Administrador')->count();
        $totalGestores   = Admin::where('rol', 'GestorECA')->count();
        $totalCiudadanos = Admin::where('rol', 'Ciudadano')->count();
        $totalPuntosECAactivos = PuntoEca::where('estado', 'activo')->count();
        $totalPublicaciones = Publicaciones::count();

        $ultimosAdmins = Admin::latest('creado')->limit(10)->get();
        $ultimosGestores = Admin::latest('creado')->limit(10)->get();
        $ultimosCiudadanos = Admin::latest('creado')->limit(10)->get();
        $ultimosPuntosECAactivos = PuntoEca::latest('creado')->limit(10)->get();
        $ultimosPublicaciones = Publicaciones::latest('creado')->limit(10)->get();


        $dataView = [
            'totalUsuarios'           => $totalUsuarios,
            'totalAdmins'             => $totalAdmins,
            'totalGestores'           => $totalGestores,
            'totalCiudadanos'         => $totalCiudadanos,
            'totalPuntosECAactivos'   => $totalPuntosECAactivos,
            'totalPublicaciones'      => $totalPublicaciones,
            'ultimosAdmins'           => $ultimosAdmins,
            'ultimosGestores'         => $ultimosGestores,
            'ultimosCiudadanos'       => $ultimosCiudadanos,
            'ultimosPuntosECAactivos' => $ultimosPuntosECAactivos,
            'ultimosPublicaciones'    => $ultimosPublicaciones,

        ];
        return view('Administracion.administrador', $dataView);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function createUsuarios(Request $request)
    {
        //
        $rules = [
            'rol'                 => ['required', 'in:Ciudadano,GestorECA,Administrador'], // <- lista blanca
            'correo'               => ['required', 'email', 'max:255', 'unique:usuarios,correo'],
            'password'             => ['required', 'string', 'min:8'],
            'nombre'               => ['required', 'string', 'max:100'],
            'apellido'             => ['required', 'string', 'max:100'],
            'recibeNotificaciones' => ['nullable'], // checkbox
            'fechaNacimiento'      => ['nullable', 'date'],
            'avatar'               => ['nullable', 'url'],
            'estado'               => ['nullable', 'in:activo,inactivo,bloqueado'], // <- lista blanca
            'nombre_usuario'       => ['nullable', 'string', 'max:60'],
            'genero'               => ['nullable', 'in:Masculino,Femenino,Otro'],
            'localidad'            => ['nullable', 'string', 'max:60'],
            'tipo_documento'      => ['nullable', 'string', 'max:20'],
            'numero_documento'    => ['nullable', 'string', 'max:20'],
        ];

        $data = $request->validate($rules);

        // Mapeo camelCase -> snake_case y hasheo de contraseña
        $payload = [
            'id'                    => (string) Str::uuid(),
            'rol'                   => $data['rol'],                 // <- mapeo clave para que no falle
            'correo'                => $data['correo'],
            'password'              => Hash::make($data['password']), // <- hasheo hacia columna 'password'
            'nombre'                => $data['nombre'],
            'apellido'              => $data['apellido'],
            'recibe_notificaciones' => isset($data['recibeNotificaciones']) ? 1 : 0,
            'fecha_nacimiento'      => $data['fechaNacimiento'] ?? null,
            'avatar_url'            => $data['avatar'] ?? null,
            'nombre_usuario'        => $data['nombre_usuario'] ?? null,
            'genero'                => $data['genero'] ?? null,
            'localidad'             => $data['localidad'] ?? null,
            'estado'                => 'activo',
            'creado'                => now(),
            'actualizado'           => now(),
        ];

        DB::transaction(function () use ($payload) {
            Usuario::create($payload);
        });
        return redirect('/registro/exitoso')->with('ok', true);
    }

    /**
     * Store a newly created resource in storage.
     */
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

        DB::transaction(fn() => Admin::create($payload));

        return redirect()->route('admin.dashboard')->with('ok', 'Usuario creado correctamente');
    }


    public function puntosECAStore(Request $request)
    {
        $data = $request->validate([
            'gestor_id'         => ['required', 'in:GestorECA'],
            'nombre'            => ['required', 'email', 'max:255', 'unique:usuarios,correo'],
            'apellido'          => ['required', 'string', 'min:8'],
            'tipoDocumento'     => ['required', 'in:Cédula de Ciudadanía,Cédula de Extranjería,Tarjeta de Identidad, Pasaporte'],
            'numeroDocumento'   => ['required', 'string', 'max:100'],
            'nombrePunto'       => ['required', 'string', 'max:100'],
            'nit'               => ['required', 'text', 'max:20'],
            'horario_atencion'  => ['nullable', 'text', 'max:30'],
            'correoPunto'       => ['nullable', 'string', 'max:30'],
            'telefonoPunto'     => ['nullable', 'integer', 'max:30'],
            'direccionPunto'    => ['required', 'string', 'max:30'],
            'ciudad'            => ['nullable', 'in:Bogotá'],
            'localidadPunto'    => ['nullable', 'in:Usaquén,Chapinero,Santa Fe,San Cristóbal,Usme,Tunjuelito,Bosa,Kennedy,Fontibon,Engativa,Suba,Barrios Unidos,Teusaquillo,Los Mártires,Antonio Nariño,Puente Aranda,La Candelaria,Rafael Uribe,Ciudad Bolívar,Sumapaz'],
            'logo'              => ['nullable', 'file'],
            'foto'              => ['nullable', 'file'],
            'sitioWeb'          => ['nullable', 'url'],
        ]);

        $payload = array_merge($data, [
            'id'          => (string) Str::uuid(),
            'password'    => Hash::make($data['password']),
            'estado'      => $data['estado'] ?? 'activo',
            'creado'      => now(),
            'actualizado' => now(),
        ]);

        DB::transaction(fn() => Admin::create($payload));

        return redirect()->route('admin.dashboard')->with('ok', 'Usuario creado correctamente');
    }
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
            ...$data,
            'telefono' => $data['telefonoPunto'] ?? null,
            'correo'   => $data['correoPunto'] ?? null,
        ]));

        return back()->with('ok', 'Punto ECA creado');
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
    public function usuariosEdit(string $id)
    {
        $usuario = Admin::findOrFail($id);

        return view('Administracion.usuario_edit', compact('usuario'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function usuariosUpdate(Request $request, string $id)
    {
        $usuario = Admin::findOrFail($id);

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


    /**
     * Remove the specified resource from storage.
     */
    public function usuariosDestroy(string $id)
    {
        $usuario = Admin::findOrFail($id);
        $usuario->delete();
        return back()->with('ok', 'Usuario eliminado');
    }

    public function view_admin()
    {
        //return $this->view('/Admin/', 'admin');
        return view('Administracion.administrador');
    }
}
