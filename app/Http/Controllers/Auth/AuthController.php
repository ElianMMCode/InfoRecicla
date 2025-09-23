<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Usuario;
use Illuminate\Support\Str;

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

        // admin predefinido: crea (si no existe) y autentica usando el guard
        if ($request->correo === 'admin@gmail.com' && $request->password === 'admin123') {
            $admin = Usuario::firstOrCreate(
                ['correo' => 'admin@gmail.com'],
                [
                    'id' => (string) Str::uuid(),
                    'password' => 'admin123', // mutator hará hash
                    'nombre' => 'Admin',
                    'apellido' => 'Sistema',
                    'rol' => 'Administrador',
                    'estado' => 'activo',
                    'creado' => now(),
                    'actualizado' => now(),
                ]
            );
            Auth::login($admin, true); // recuerda sesión
            $request->session()->regenerate();
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
