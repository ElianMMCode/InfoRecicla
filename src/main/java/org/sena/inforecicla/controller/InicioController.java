package org.sena.inforecicla.controller;

import org.springframework.stereotype.Controller;
import org.springframework.web.bind.annotation.GetMapping;

@Controller
public class InicioController {

    @GetMapping({"/inicio", "/home"})
    public String inicio() {
        // Vista principal de la aplicación
        return "views/converted/Inicio/inicio";
    }

    @GetMapping({"/inicio-sesion", "/login"})
    public String inicioSesion() {
        // Vista de inicio de sesión (registro convertido)
        return "views/Registro/inicioSesion";
    }
}

