<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CiudadanoController extends Controller
{
    //
    public function view_ciudadano()
    {
        // Lógica para la vista de registro
        return view('Ciudadano.ciudadano');
    }
}
