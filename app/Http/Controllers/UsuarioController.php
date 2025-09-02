<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UsuarioController extends Controller
{
    //
    public function view_registro()
    {
        // Lógica para la vista de registro
        return view('Registro.registro');
    }
    public function view_registro_ciudadano()
    {
        // Lógica para la vista de registro
        return view('Registro.registro_ciudadano');
    }
    public function view_registro_eca()
    {
        // Lógica para la vista de registro
        return view('Registro.registro_eca');
    }
    public function view_registro_exitoso()
    {
        // Lógica para la vista de registro exitoso
        return view('Registro.registro_exitoso');
    }
}
