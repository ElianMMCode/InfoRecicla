<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MapaController extends Controller
{
    public function index(Request $request)
    {
        return view('Mapa.mapa');
    }
}
