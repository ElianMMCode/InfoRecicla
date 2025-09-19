<?php

namespace App\Http\Controllers;

use App\Models\PuntoEca;
use App\Models\Usuario;
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

class UsuarioController extends Controller
{
    public function indexEca(Request $request)
    {
        $user = Auth::user();

        $gestor = DB::table('usuarios')->where('id', $user->id)->where('tipo', 'GestorECA')->first();

        return view('users.indexEca', compact('gestor'));
    }
    //
    public function store(Request $request)
    {
        $data = $request->validate([
            'tipo' => ['required', 'in:Ciudadano,GestorECA,Administrador'],
            'correo' => ['required', 'email', 'max:255', 'unique:usuarios,correo'],
            'password' => ['required', 'string', 'min:8'],
            'nombre' => ['required', 'string', 'max:100'],
            'apellido' => ['required', 'string', 'max:100'],
            'recibeNotificaciones' => ['nullable'],
            'fechaNacimiento' => ['nullable', 'date'],
            'avatar' => ['nullable', 'url'],
            'nombre_usuario' => ['nullable', 'string', 'max:60'],
            'genero' => ['nullable', 'string', 'max:20'],
            'localidad' => ['nullable', 'string', 'max:60'],
        ]);

        $carga = [
            'id' => (string) Str::uuid(),
            'correo' => $data['correo'],
            'password' => Hash::make($data['password']),
            'nombre' => $data['nombre'],
            'apellido' => $data['apellido'],
            'recibe_notificaciones' => isset($data['recibeNotificaciones']) ? 1 : 0,
            'fecha_nacimiento' => $data['fechaNacimiento'] ?? null,
            'avatar_url' => $data['avatar'] ?? null,
            'nombre_usuario' => $data['nombre_usuario'] ?? null,
            'genero' => $data['genero'] ?? null,
            'localidad' => $data['localidad'] ?? null,
            'estado' => 'activo',
            'creado' => now(),
            'actualizado' => now(),
        ];

        DB::transaction(function () use ($carga) {
            Usuario::create($carga);
        });
        return redirect('/registro/exitoso')->with('ok', true);
    }

    public function storeEca(Request $request)
    {
        $rules = [
            'tipo' => ['required', 'in:Ciudadano,GestorECA,Administrador'],
            'correo' => ['required', 'email', 'max:255', 'unique:usuarios,correo'],
            'password' => ['required', 'string', 'min:8'],
            'nombre' => ['required', 'string', 'max:50'],
            'apellido' => ['required', 'string', 'max:50'],
            'tipoDocumento' => ['nullable', 'string', 'max:30'],
            'numeroDocumento' => ['nullable', 'string', 'max:30'],
            'recibeNotificaciones' => ['required'],
            'nombrePunto' => ['required', 'string', 'max:100'],
            'direccionPunto' => ['required', 'string', 'max:100'],
            'telefonoPunto' => ['required', 'string', 'max:100'],
            'correoPunto' => ['required', 'string', 'max:100'],
            'ciudad' => ['required', 'string', 'max:100'],
            'localidadPunto' => ['required', 'string', 'max:100'],
            'latitud' => ['nullable', 'string', 'max:100'],
            'longitud' => ['nullable', 'string', 'max:100'],
            'nit' => ['nullable', 'string', 'max:100'],
            'horarioAtencion' => ['nullable', 'string', 'max:100'],
            'sitioWeb' => ['nullable', 'string', 'max:100'],
            'logo' => ['nullable', 'string', 'max:100'],
            'foto' => ['nullable', 'string', 'max:100'],
            'mostrarMapa' => ['required'],
        ];

        $data = $request->validate($rules);

        DB::transaction(function () use ($data) {
            $usuarioId = (string) Str::uuid();
            $usuario = Usuario::create([
                'id' => $usuarioId,
                'rol' => $data['tipo'],
                'correo' => $data['correo'],
                'password' => Hash::make($data['password']),
                'nombre' => $data['nombre'],
                'apellido' => $data['apellido'],
                'recibe_notificaciones' => isset($data['recibeNotificaciones']) ? 1 : 0,
                'tipo_documento' => $data['tipoDocumento'] ?? null,
                'numero_documento' => $data['numeroDocumento'] ?? null,
                'nombre_usuario' => $data['nombrePunto'] ?? null,
                'estado' => 'activo',
                'creado' => now(),
                'actualizado' => now(),
            ]);
            if ($usuario->rol === 'GestorECA') {
                PuntoEca::create([
                    'id' => (string) Str::uuid(),
                    'gestor_id' => $usuarioId,
                    'nombre' => $data['nombrePunto'],
                    'direccion' => $data['direccionPunto'],
                    'telefonoPunto' => $data['telefonoPunto'],
                    'correoPunto' => $data['correoPunto'],
                    'ciudad' => $data['ciudad'],
                    'localidad' => $data['localidadPunto'],
                    'latitud' => $data['latitud'],
                    'longitud' => $data['longitud'],
                    'nit' => $data['nit'],
                    'horario_atencion' => $data['horarioAtencion'],
                    'sitio_web' => $data['sitioWeb'],
                    'logo_url' => $data['logo'] ?? null,
                    'foto_url' => $data['foto'] ?? null,
                    'mostrar_mapa' => isset($data['mostrarMapa']) ? 1 : 0,
                    'creado' => now(),
                    'actualizado' => now(),
                ]);
            }
        });
        return redirect('/registro/exitoso')->with('ok', true);
    }

    public function updatePerfil(Request $request)
    {
        $authUser = Usuario::findOrFail(Auth::user()->id);

        //obtener el id del punto
        $punto = PuntoEca::where('gestor_id', $authUser->id)->firstOrFail();

        // Validación combinada (usuario + punto)
        $rules = [
            // encargado
            'usuarios.nombre' => ['required', 'string', 'max:120'],
            'usuarios.apellido' => ['required', 'string', 'max:120'],
            'usuarios.telefono' => ['nullable', 'string', 'max:30'],
            'usuarios.correo' => ['required', 'email', 'max:255', Rule::unique('usuarios', 'correo')->ignore($authUser->id)],
            'usario.avatar' => ['nullable', 'image', 'max:2048'],
            'usuarios.current_password' => ['nullable:usuario:password', 'current_password:web'],
            'usuarios.password' => ['nullable', 'min:8', 'confirmed'],
            'usuarios.password_confirmation' => ['nullable'],
            'punto.nombre' => ['required', 'string', 'max:120'],
            'punto.direccion' => ['nullable', 'string', 'max:200'],
            'punto.ciudad' => ['nullable', 'string', 'max:100'],
            'punto.localidad' => ['nullable', 'string', 'max:100'],
            'punto.latitud' => ['nullable', 'numeric'],
            'punto.longitud' => ['nullable', 'numeric'],
            'punto.horario_atencion' => ['nullable', 'string', 'max:120'],
            'punto.foto' => ['nullable', 'image', 'max:4096'],
            'punto.logo' => ['nullable', 'image', 'max:2048'],
        ];

        $data = $request->validate($rules);

        // Validación de la contraseña
        if (!empty($data['usuarios']['current_password']) && empty($data['usuarios']['password'])) {
            throw ValidationException::withMessages([
                'usuarios.password' => 'Debes indicar la nueva contraseña.',
            ]);
        }
        DB::transaction(function () use ($data, $authUser, $punto, $request) {
            $authUser->nombre = $data['usuarios']['nombre'];
            $authUser->correo = $data['usuarios']['correo'];
            // si no viene nueva no se hace nada
            $nueva = $data['usuarios']['password'] ?? null;
            if (!filled($nueva)) {
                // nada que hacer con password
            } else {
                $authUser->password = $nueva;
                $authUser->save();
            }

            if ($request->hasFile('usuarios.avatar')) {
                $path = $request->file('usuarios.avatar')->store('avatars', 'public');
                $authUser->avatar_url = Storage::url($path);
            }

            if (!empty($data['usario']['password'])) {
                $authUser->password = Hash::make($data['usuarios']['password']);
            }

            $authUser->save();

            $punto->nombre = $data['punto']['nombre'];
            $punto->direccion = $data['punto']['direccion'] ?? null;
            $punto->ciudad = $data['punto']['ciudad'] ?? null;
            $punto->localidad = $data['punto']['localidad'] ?? null;
            $punto->latitud = $data['punto']['latitud'] ?? null;
            $punto->longitud = $data['punto']['longitud'] ?? null;
            $punto->horario_atencion = $data['punto']['horario_atencion'] ?? null;

            if ($request->hasFile('punto.foto')) {
                $path = $request->file('punto.foto')->store('puntos', 'public');
                $punto->foto_url = Storage::url($path);
            }
            if ($request->hasFile('punto.logo')) {
                $path = $request->file('punto.logo')->store('logos', 'public');
                $punto->logo_url = Storage::url($path);
            }

            $punto->save();
        });

        return redirect()
            ->route('eca.index', ['seccion' => 'perfil'])
            ->with('ok', 'Perfil actualizado correctamente.');
    }

    public function view_registro($tipo = null)
    {
        switch ($tipo) {
            case 'ciudadano':
                return view('Registro.registro_ciudadano');
            case 'eca':
                return view('Registro.registro_eca');
            case 'exitoso':
                return view('Registro.registro_exitoso');
            default:
                return view('Registro.registro');
        }
    }
}
