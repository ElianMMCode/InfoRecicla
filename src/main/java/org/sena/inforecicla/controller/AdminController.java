package org.sena.inforecicla.controller;

import org.springframework.stereotype.Controller;
import org.springframework.ui.Model;
import org.springframework.web.bind.annotation.*;

@Controller
@RequestMapping("/admin")
public class AdminController {

    @GetMapping({ "", "/" })
    public String admin(Model model) {
        model.addAttribute("seccion", "resumen");
        return "views/Admin/admin";
    }

    // Navegación por query param: /punto-eca?seccion=materiales
    @GetMapping(params = "seccion")
    public String adminPorConsulta(@RequestParam("seccion") String seccion, Model model) {
        model.addAttribute("seccion", seccion);
        return "views/Admin/admin";
    }

    // Navegación por path: /punto-eca/materiales
    @GetMapping("/{seccion}")
    public String adminPorPath(@PathVariable("seccion") String seccion, Model model) {
        model.addAttribute("seccion", seccion);
        return "views/Admin/admin";
    }

}
