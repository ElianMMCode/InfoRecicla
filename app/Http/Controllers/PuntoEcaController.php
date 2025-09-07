<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PuntoEcaController extends Controller
{
    //
    public function view_punto_eca()
    { // Lógica para la vista de registro
        //return $this->view('/PuntoECA/', 'gestor-eca');
        return view('PuntoECA.punto-eca');
    }
}
