package org.sena.inforecicla.controller;

import org.springframework.stereotype.Controller;
import org.springframework.web.bind.annotation.GetMapping;

@Controller
public class InicioController {

    @GetMapping
    public String inicio() {
        return "views/Inicio/inicio";
    }
}
