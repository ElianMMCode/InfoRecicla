<?php

namespace App\Http\Controllers;

abstract class Controller
{
    //
    /*public function view($ruta, $vista)
    {
        $vista = trim($vista, '/'); // Eliminar barras al inicio y al final

        // Verificar si la vista existe
        // Asumiendo que las vistas están en un directorio específico
        // Aquí se puede ajustar la ruta según la estructura del proyecto

        if (file_exists("../resources/views/{$ruta}/{$vista}.html")) {

            // Capturar la salida
            ob_start();
            include "../resources/views/{$ruta}/{$vista}.html";
            // Guardar el contenido
            $contenido = ob_get_clean();
            return $contenido;
        } elseif (file_exists("../resources/views/{$ruta}/{$vista}.php")) {
            // Capturar la salida
            ob_start();
            include "../resources/views/{$ruta}/{$vista}.php";
            // Guardar el contenido
            $contenido = ob_get_clean();
            return $contenido;
        } else {
            ob_start();
            include "../resources/Errores/404.html";
            $contenido = ob_get_clean();
            return $contenido;
        }
    }*/
}
