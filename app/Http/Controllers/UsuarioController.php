<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;

class UsuarioController extends Controller
{
    //
    public function store(Request $request)
    {
        $data = $request->validate([
            'tipo'                 => ['required', 'in:Ciudadano,GestorECA,Administrador'], // <- lista blanca
            'correo'               => ['required', 'email', 'max:255', 'unique:usuarios,correo'],
            'password'             => ['required', 'string', 'min:8'],
            'nombre'               => ['required', 'string', 'max:100'],
            'apellido'             => ['required', 'string', 'max:100'],
            'tipoDocumento'        => ['required', 'string', 'max:30'],
            'numeroDocumento'      => ['required', 'string', 'max:30', 'unique:usuarios,numero_documento'],
            'telefono'             => ['nullable', 'string', 'max:30'],
            'recibeNotificaciones' => ['nullable'], // checkbox
            'fechaNacimiento'      => ['nullable', 'date'],
            'avatar'               => ['nullable', 'url'],
            'genero'               => ['nullable', 'string', 'max:20'],
        ]);
        Usuario::create($data);
        // $usuario = new Usuario();
        // $usuario->correo                = $data['correo'];
        // $usuario->password              = bcrypt($request->password_hash);
        // $usuario->nombre                = $data['nombre'];
        // $usuario->apellido              = $data['apellido'];
        // $usuario->rol                   = $rol;
        // $usuario->tipo_documento        = $request->input('tipoDocumento');
        // $usuario->numero_documento      = $data['numeroDocumento'];
        // $usuario->telefono              = $data['telefono'] ?? null;
        // $usuario->recibe_notificaciones = $request->boolean('recibe_notificaciones');
        // $usuario->fecha_nacimiento      = $data['fecha_nacimiento'] ?? null;
        // $usuario->avatar                = $data['avatar_url'] ?? null;
        // $usuario->nombre_usuario        = $data['correo'] ?? null;
        // $usuario->localidad             = $data['localidad'] ?? null;
        // $usuario->genero                = $data['genero'] ?? null;
        // $usuario->save();

        return redirect('/registro/exitoso');
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
