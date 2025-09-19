<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function index()
    {
        return redirect()->route('eca.index', ['seccion' => 'perfil']);
    }

    public function postLogin(Request $request)
    {
        //valida los campos ingresados desde el login
        $request->validate([
            'correo'   => 'required|email',
            'password' => 'required|string',
        ]);

        //valida las credenciales
        $autenticado = Auth::attempt(
            ['correo' => $request->correo, 'password' => $request->password],
            $request->boolean('remember')
        );

        if ($autenticado) {
            // Regenera la sesión
            $request->session()->regenerate();

            $usuario = Auth::user();

            // Redirige según el rol
            if ($usuario->rol === 'GestorECA' || $usuario->rol === 'Administrador') {
                return redirect()->intended(route('eca.index', ['seccion' => 'resumen']));
            } elseif ($usuario->rol === 'Ciudadano') {
                return redirect()->intended(route('ciudadano'));
            }
            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors([
            'correo' => 'Estas credenciales no coinciden con nuestros registros.',
        ])->onlyInput('correo');
    }


    public function dashboard()
    {
    }

    public function logout(Request $request)
    {
        // Cierra la sesión
        Auth::logout();
        // Regenera el token
        $request->session()->invalidate();
        // Genera un nuevo token
        $request->session()->regenerateToken();
        return redirect(route('login'))->with('success', 'Saliendo...');
    }
}
