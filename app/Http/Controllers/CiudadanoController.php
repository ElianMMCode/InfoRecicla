<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;

class CiudadanoController extends Controller
{
    public function view_ciudadano()
    {
        $usuario = Auth::user(); // instancia de App\Models\Usuario ya autenticada
        return view('Ciudadano.ciudadano', compact('usuario'));
    }

    public function updatePerfil(Request $request)
    {
        /** @var \App\Models\Usuario $user */
        $user = Auth::user();

        // 1) Validación de campos básicos + reglas de unicidad
        $data = $request->validate([
            'nombre'          => ['required', 'string', 'max:120'],
            'correo'          => ['required', 'email', 'max:160', Rule::unique('usuarios', 'correo')->ignore($user->id, 'id')],
            'nombre_usuario'  => ['required', 'string', 'max:60', Rule::unique('usuarios', 'nombre_usuario')->ignore($user->id, 'id')],
            'localidad'      => ['required', 'string', 'max:120'],
            'old_password'    => ['nullable', 'string'],
            'password'        => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        // 2) Asignar campos simples
        $user->nombre         = $data['nombre'];
        $user->correo         = $data['correo'];
        $user->nombre_usuario = $data['nombre_usuario'] ?? $user->nombre_usuario;
        //$user->localidad = $data['localidad'] ?? $user->localidad;
        $user->localidad      = $request->filled('localidad') ? $data['localidad'] : null;


        // 3) Cambio de contraseña (solo si envían una nueva)
        if ($request->filled('password')) {
            // Debe llegar y coincidir la contraseña actual
            if (! $request->filled('old_password') || ! Hash::check($request->old_password, $user->password)) {
                return back()
                    ->withErrors(['old_password' => 'La contraseña actual no es correcta.'])
                    ->withInput();
            }

            $user->password = $request->password;
        }

        // 4) Guardar
        $user->save();

        return redirect()->route('ciudadano')->with('success', 'Perfil actualizado.');
    }
}
