<?php

// app/Http/Middleware/RoleMiddleware.php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class RoleMiddleware
{
    // los ... permite recibir un número variable de argumentos es decir
    // ...$roles permite recibir varias variables de tipo rol
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        // Verifica si el rol del usuario está en la lista de roles permitidos
        $userRole = Auth::user()->rol;
        // damos autorizacion a 
        Log::info('RoleMiddleware', ['user_role' => $userRole, 'allowed' => $roles]);
        if (!in_array((string) $userRole, array_map('strval', $roles), true)) {
            return redirect()->route('login')->with('error', 'No tienes permiso para acceder.');
        }

        return $next($request);
    }
}
