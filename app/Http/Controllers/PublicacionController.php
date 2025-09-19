<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PublicacionController extends Controller
{
    //
    public function view_publicaciones()
    {
        return view('Publicaciones.publicaciones');
    }

    public function view_publicacion()
    {
        return view('Publicaciones.publicacion');
    }
}
