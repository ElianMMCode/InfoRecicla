<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // <-- importa Auth

class CiudadanoController extends Controller
{
    public function view_ciudadano()
    {
        $usuario = Auth::user(); // instancia de App\Models\Usuario ya autenticada
        return view('Ciudadano.ciudadano', compact('usuario'));
    }
}
