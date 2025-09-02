<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class InicioController extends Controller
{
    //

    public function view_inicio()
    {
        // Lógica para la página de inicio
        //return $this->view('/Inicio/', 'inicio');

        /*con la clase ayuda view podemos traer vistas de la carpeta views
        solo se necesita el nombre de la vista con la extension .blade.php

        no es necesario incluir la ruta completa.
        Ingresamos nombre carpeta dentro de views y el nombre de la vista sin la extensión .blade.php
        */
        return view('Inicio.inicio');
    }
}
