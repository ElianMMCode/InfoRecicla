<?php

namespace App\Http\Controllers;

use App\Models\PuntoEca;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UsuarioController extends Controller
{
    //
    public function store(Request $request)
    {
        $rules = [
            'tipo'                 => ['required', 'in:Ciudadano,GestorECA,Administrador'], // <- lista blanca
            'correo'               => ['required', 'email', 'max:255', 'unique:usuarios,correo'],
            'password'             => ['required', 'string', 'min:8'],
            'nombre'               => ['required', 'string', 'max:100'],
            'apellido'             => ['required', 'string', 'max:100'],
            'recibeNotificaciones' => ['nullable'], // checkbox
            'fechaNacimiento'      => ['nullable', 'date'],
            'avatar'               => ['nullable', 'url'],
            'nombre_usuario'       => ['nullable', 'string', 'max:60'],
            'genero'               => ['nullable', 'string', 'max:20'],
            'localidad'            => ['nullable', 'string', 'max:60'],
        ];

        $data = $request->validate($rules);

        // Mapeo camelCase -> snake_case y hasheo de contraseña
        $payload = [
            'id'                    => (string) Str::uuid(),
            'rol'                   => $data['tipo'],                 // <- mapeo clave para que no falle
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

    public function storeEca(Request $request)
    {
        $rules = [
            'tipo'                 => ['required', 'in:Ciudadano,GestorECA,Administrador'], // <- lista blanca
            'correo'               => ['required', 'email', 'max:255', 'unique:usuarios,correo'],
            'password'             => ['required', 'string', 'min:8'],
            'nombre'               => ['required', 'string', 'max:50'],
            'apellido'             => ['required', 'string', 'max:50'],
            'tipoDocumento'        => ['nullable', 'string', 'max:30'],
            'numeroDocumento'      => ['nullable', 'string', 'max:30'],
            'recibeNotificaciones' => ['required'], // checkbox
            //Punto Eca 
            'nombrePunto'          => ['required', 'string', 'max:100'],
            'direccionPunto'       => ['required', 'string', 'max:100'],
            'telefonoPunto'        => ['required', 'string', 'max:100'],
            'correoPunto'          => ['required', 'string', 'max:100'],
            'ciudad'               => ['required', 'string', 'max:100'],
            'localidadPunto'       => ['required', 'string', 'max:100'],
            'latitud'              => ['nullable', 'string', 'max:100'],
            'longitud'             => ['nullable', 'string', 'max:100'],
            'nit'                  => ['nullable', 'string', 'max:100'],
            'horarioAtencion'      => ['nullable', 'string', 'max:100'],
            'sitioWeb'             => ['nullable', 'string', 'max:100'],
            'logo'                 => ['nullable', 'string', 'max:100'],
            'foto'                 => ['nullable', 'string', 'max:100'],
            'mostrarMapa'          => ['required'],
        ];

        $data = $request->validate($rules);

        DB::transaction(function () use ($data) {
            $usuarioId = (string) Str::uuid();
            $usuario = Usuario::create([
                'id'                    => $usuarioId,
                'rol'                   => $data['tipo'],                 // <- mapeo clave para que no falle
                'correo'                => $data['correo'],
                'password'              => Hash::make($data['password']), // <- hasheo hacia columna 'password'
                'nombre'                => $data['nombre'],
                'apellido'              => $data['apellido'],
                'recibe_notificaciones' => isset($data['recibeNotificaciones']) ? 1 : 0,
                'tipo_documento'        => $data['tipoDocumento'] ?? null,
                'numero_documento'      => $data['numeroDocumento'] ?? null,
                'nombre_usuario'        => $data['nombrePunto'] ?? null,
                'estado'                => 'activo',
                'creado'                => now(),
                'actualizado'           => now(),
            ]);
            if ($usuario->rol === 'GestorECA') {
                PuntoEca::create([
                    'id'                    => (string) Str::uuid(),
                    'gestor_id'            => $usuarioId,
                    'nombre'                => $data['nombrePunto'],
                    'direccion'             => $data['direccionPunto'],
                    'telefonoPunto'         => $data['telefonoPunto'],
                    'correoPunto'           => $data['correoPunto'],
                    'ciudad'                => $data['ciudad'],
                    'localidad'             => $data['localidadPunto'],
                    'latitud'               => $data['latitud'],
                    'longitud'              => $data['longitud'],
                    'nit'                   => $data['nit'],
                    'horario_atencion'      => $data['horarioAtencion'],
                    'sitio_web'             => $data['sitioWeb'],
                    'logo_url'              => $data['logo'] ?? null,
                    'foto_url'              => $data['foto'] ?? null,
                    'mostrar_mapa'          => isset($data['mostrarMapa']) ? 1 : 0,
                    'creado'                => now(),
                    'actualizado'           => now(),
                ]);
            }
        });

        return redirect('/registro/exitoso')->with('ok', true);
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
