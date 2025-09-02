<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class InicioSesionController extends Controller
{
    //
    public function view_InicioSesion()
    {
        // Lógica para la vista de inicio de sesión
        return view('Registro.inicioSesion');
    }
}
