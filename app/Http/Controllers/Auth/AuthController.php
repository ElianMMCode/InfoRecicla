<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function index()
    {
        return view('Registro.inicioSesion'); // usa tu Blade existente


    }

    public function postLogin(Request $request)
    {
        $request->validate([
            'correo'   => 'required|email',
            'password' => 'required|string',
        ]);

        $ok = Auth::attempt(
            ['correo' => $request->correo, 'password' => $request->password],
            $request->boolean('remember')
        );

        if ($ok) {
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
        return view('Inicio.inicio'); // crea/ajusta tu vista
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect(route('login'))->with('success', 'Saliendo...');
    }
}
