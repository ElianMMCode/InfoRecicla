<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CiudadanoController extends Controller
{
    public function view_ciudadano()
    {
        $usuario = Auth::user();
        return view('Ciudadano.ciudadano', compact('usuario'));
    }
}
