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

class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //

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

    public function view_admin()
    {
        //return $this->view('/Admin/', 'admin');
        return view('Administracion.administrador');
    }
}
