<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUsuarioRequest;
use App\Http\Requests\StoreEcaRequest;
use App\Http\Requests\UpdatePerfilRequest;
use App\Models\Usuario;
use App\Models\PuntoEca;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class UsuarioController extends Controller
{
    /**
     * Vista del Gestor ECA
     */
    public function indexEca(Request $request)
    {
        $user = Auth::user();

        $gestor = DB::table('usuarios')->where('id', $user->id)->where('tipo', 'GestorECA')->first();

        return view('users.indexEca', compact('gestor'));
    }

    /**
     * Registro de usuario (Ciudadano / GestorECA / Administrador)
     * Usa StoreUsuarioRequest con validaciones estrictas.
     */
    public function store(StoreUsuarioRequest $request)
    {
        $data = $request->validated();

        $carga = [
            'id' => (string) Str::uuid(),
            'correo' => $data['correo'],
            'password' => Hash::make($data['password']),
            'nombre' => $data['nombre'],
            'apellido' => $data['apellido'],
            'recibe_notificaciones' => isset($data['recibeNotificaciones']) ? (int) (bool) $data['recibeNotificaciones'] : 0,
            'fecha_nacimiento' => $data['fechaNacimiento'] ?? null,
            'avatar_url' => $data['avatar'] ?? null,
            'nombre_usuario' => $data['nombre_usuario'] ?? null,
            'genero' => $data['genero'] ?? null,
            'localidad' => $data['localidad'] ?? null,
            'estado' => 'activo',
            'creado' => now(),
            'actualizado' => now(),
            // Si en tu tabla guardas el tipo/rol, respétalo:
            'tipo' => $data['tipo'] ?? null,
            // 'rol' => $data['tipo'] ?? null, // usa este si tu columna es 'rol' en vez de 'tipo'
        ];

        DB::transaction(function () use ($carga) {
            Usuario::create($carga);
        });

        return redirect('/registro/exitoso')->with('ok', true);
    }

    /**
     * Registro de Gestor ECA + creación de Punto ECA
     * Usa StoreEcaRequest con validaciones estrictas por contexto.
     */
    public function storeEca(StoreEcaRequest $request)
    {
        $data = $request->validated();

        DB::transaction(function () use ($data) {
            $usuarioId = (string) Str::uuid();

            $usuario = Usuario::create([
                'id' => $usuarioId,
                // En tu código usas 'rol' aquí; si en tu tabla realmente se llama 'tipo', cambia a 'tipo' y no dupliques.
                'rol' => $data['tipo'],
                'tipo' => $data['tipo'] ?? null, // déjalo si tu tabla también guarda 'tipo'
                'correo' => $data['correo'],
                'password' => Hash::make($data['password']),
                'nombre' => $data['nombre'],
                'apellido' => $data['apellido'],
                'recibe_notificaciones' => isset($data['recibeNotificaciones']) ? (int) (bool) $data['recibeNotificaciones'] : 0,
                'tipo_documento' => $data['tipoDocumento'] ?? null,
                'numero_documento' => $data['numeroDocumento'] ?? null,
                'nombre_usuario' => $data['nombrePunto'] ?? null,
                'localidad' => $data['localidad'],
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
                    'latitud' => $data['latitud'] ?? null,
                    'longitud' => $data['longitud'] ?? null,
                    'nit' => $data['nit'] ?? null,
                    'horario_atencion' => $data['horarioAtencion'] ?? null,
                    'sitio_web' => $data['sitioWeb'] ?? null,
                    'logo_url' => $data['logo'] ?? null,
                    'foto_url' => $data['foto'] ?? null,
                    'mostrar_mapa' => isset($data['mostrarMapa']) ? (int) (bool) $data['mostrarMapa'] : 0,
                    'creado' => now(),
                    'actualizado' => now(),
                ]);
            }
        });

        return redirect('/registro/exitoso')->with('ok', true);
    }

    /**
     * Actualización de perfil (usuario + punto)
     * Usa UpdatePerfilRequest con validaciones estrictas y normalización.
     */
    public function updatePerfil(UpdatePerfilRequest $request)
    {
        $authUser = Usuario::findOrFail(Auth::user()->id);
        $punto = PuntoEca::where('gestor_id', $authUser->id)->firstOrFail();

        $data = $request->validated();

        // Si el usuario envía contraseña actual pero no nueva → error controlado
        if (!empty(data_get($data, 'usuarios.current_password')) && empty(data_get($data, 'usuarios.password'))) {
            throw ValidationException::withMessages([
                'usuarios.password' => 'Debes indicar la nueva contraseña.',
            ]);
        }

        DB::transaction(function () use ($data, $authUser, $punto, $request) {
            // ==== USUARIO ====
            $authUser->nombre = $data['usuarios']['nombre'];
            $authUser->apellido = $data['usuarios']['apellido'];
            $authUser->correo = $data['usuarios']['correo'];

            // Teléfono (puede venir null/nullable)
            if (array_key_exists('telefono', $data['usuarios'])) {
                $authUser->telefono = $data['usuarios']['telefono'];
            }

            // Password (si viene y pasó validación)
            $nueva = $data['usuarios']['password'] ?? null;
            if (filled($nueva)) {
                $authUser->password = Hash::make($nueva);
            }

            // Avatar (input file con name="usuarios[avatar]")
            if ($request->hasFile('usuarios.avatar')) {
                $path = $request->file('usuarios.avatar')->store('avatars', 'public');
                $authUser->avatar_url = Storage::url($path);
            }

            $authUser->actualizado = now();
            $authUser->save();

            // ==== PUNTO ====
            $punto->nombre = $data['punto']['nombre'];
            $punto->telefonoPunto = $data['punto']['telefono'] ?? null; // ← nombre que ya usas en tu asignación
            $punto->direccion = $data['punto']['direccion'] ?? null;
            $punto->ciudad = $data['punto']['ciudad'] ?? null;
            $punto->localidad = $data['punto']['localidad'] ?? null;
            $punto->latitud = $data['punto']['latitud'] ?? null;
            $punto->longitud = $data['punto']['longitud'] ?? null;
            $punto->horario_atencion = $data['punto']['horario_atencion'] ?? null;

            // Archivos anidados (name="punto[foto]" y "punto[logo]")
            if ($request->hasFile('punto.foto')) {
                $path = $request->file('punto.foto')->store('puntos', 'public');
                $punto->foto_url = Storage::url($path);
            }
            if ($request->hasFile('punto.logo')) {
                $path = $request->file('punto.logo')->store('logos', 'public');
                $punto->logo_url = Storage::url($path);
            }

            $punto->actualizado = now();
            $punto->save();
        });

        return redirect()
            ->route('eca.index', ['seccion' => 'perfil'])
            ->with('ok', 'Perfil actualizado correctamente.');
    }

    /**
     * Vistas de registro por tipo
     */
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
