<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MapaController extends Controller
{
    //
    public function view_mapa()
    {
        // Lógica para la vista del mapa
        return view('Mapa.mapa');
    }
}
