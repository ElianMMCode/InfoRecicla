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

        // Si es el usuario admin predeterminado, simula login
        if ($request->correo === 'admin@gmail.com' && $request->password === 'admin123') {
            // Simular usuario admin en sesión
            $adminFake = (object) [
                'id' => 'admin-fake',
                'nombre' => 'Administrador',
                'apellido' => '',
                'correo' => 'admin@gmail.com',
                'rol' => 'Administrador',
            ];
            // Guardar en sesión
            session(['usuario' => $adminFake]);
            // Redirigir al panel admin
            return redirect()->intended(route('admin'));
        }

        //valida las credenciales normales
        $autenticado = Auth::attempt(
            ['correo' => $request->correo, 'password' => $request->password],
            $request->boolean('remember')
        );

        if ($autenticado) {
            $request->session()->regenerate();
            $usuario = Auth::user();
            if ($usuario->rol === 'GestorECA') {
                return redirect()->intended(route('eca.index', ['seccion' => 'resumen']));
            } elseif ($usuario->rol === 'Administrador') {
                return redirect()->intended(route('admin'));
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
}
