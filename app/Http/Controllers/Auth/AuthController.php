<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function index()
    {
        return view('Registro.inicioSesion');
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
            return redirect()->intended(route('inicio'));
        }

        return back()->withErrors([
            'correo' => 'Estas credenciales no coinciden con nuestros registros.',
        ])->onlyInput('correo');
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
    public function dashboard()
    {
        $usuario = Auth::user();
        if (! $usuario) {
            return redirect()->route('login');
        }

        // Redirige según rol (mismo criterio que en postLogin)
        if ($usuario->rol === 'GestorECA' || $usuario->rol === 'Administrador') {
            return redirect()->route('eca.index', ['seccion' => 'resumen']);
        }

        if ($usuario->rol === 'Ciudadano') {
            return redirect()->route('ciudadano');
        }

        // Fallback: si no encaja en nada, a inicio
        return redirect()->route('inicio');
    }
}
