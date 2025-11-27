package org.sena.inforecicla.controller;

import org.springframework.stereotype.Controller;
import org.springframework.ui.Model;
import org.springframework.web.bind.annotation.*;

@Controller
@RequestMapping("/punto-eca")
public class PuntoEcaController {

    @GetMapping({"", "/"})
    public String puntoEca(Model model) {
        model.addAttribute("seccion", "resumen");
        return "views/PuntoECA/puntoECA-layout";
    }

    // Navegación por query param: /punto-eca?seccion=materiales
    @GetMapping(params = "seccion")
    public String puntoEcaPorConsulta(@RequestParam("seccion") String seccion, Model model) {
        model.addAttribute("seccion", seccion);
        return "views/PuntoECA/puntoECA-layout";
    }

    // Navegación por path: /punto-eca/materiales
    @GetMapping("/{seccion}")
    public String puntoEcaPorPath(@PathVariable("seccion") String seccion, Model model) {
        model.addAttribute("seccion", seccion);
        return "views/PuntoECA/puntoECA-layout";
    }
}
