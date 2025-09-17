<?php

// app/Http/Middleware/RoleMiddleware.php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // Ajusta 'tipo' al nombre real de tu columna de rol
        $userRole = Auth::user()->rol;
        \Illuminate\Support\Facades\Log::info('RoleMiddleware', ['user_role' => $userRole, 'allowed' => $roles]);
        if (!in_array((string) $userRole, array_map('strval', $roles), true)) {
            return redirect()->route('login')->with('error', 'No tienes permiso para acceder.');
        }

        return $next($request);
    }
}