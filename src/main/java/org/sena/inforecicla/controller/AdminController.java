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
        return "views/Admin/admin-layout";
    }

    // Navegación por query param: /punto-eca?seccion=materiales
    @GetMapping(params = "seccion")
    public String adminPorConsulta(@RequestParam("seccion") String seccion, Model model) {
        model.addAttribute("seccion", seccion);
        return "views/Admin/admin-layout";
    }

    // Navegación por path: /punto-eca/materiales
    @GetMapping("/{seccion}")
    public String adminPorPath(@PathVariable("seccion") String seccion, Model model) {
        model.addAttribute("seccion", seccion);
        return "views/Admin/admin-layout";
    }

    @GetMapping({ "", "/" })
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

    @GetMapping({ "", "/" })
    public String usuario(Model model) {
        model.addAttribute("seccion", "resumen");
        return "views/Usuario/usuario-layout";
    }

    // Navegación por query param: /punto-eca?seccion=materiales
    @GetMapping(params = "seccion")
    public String usuarioPorConsulta(@RequestParam("seccion") String seccion, Model model) {
        model.addAttribute("seccion", seccion);
        return "views/Usuario/usuario-layout";
    }

    // Navegación por path: /punto-eca/materiales
    @GetMapping("/{seccion}")
    public String usuarioPorPath(@PathVariable("seccion") String seccion, Model model) {
        model.addAttribute("seccion", seccion);
        return "views/Usuario/usuario-layout";
    }

    @GetMapping({ "", "/" })
    public String material(Model model) {
        model.addAttribute("seccion", "resumen");
        return "views/Material/material-layout";
    }

    // Navegación por query param: /punto-eca?seccion=materiales
    @GetMapping(params = "seccion")
    public String materialPorConsulta(@RequestParam("seccion") String seccion, Model model) {
        model.addAttribute("seccion", seccion);
        return "views/Material/material-layout";
    }

    // Navegación por path: /punto-eca/materiales
    @GetMapping("/{seccion}")
    public String materialPorPath(@PathVariable("seccion") String seccion, Model model) {
        model.addAttribute("seccion", seccion);
        return "views/Material/material-layout";
    }
}
