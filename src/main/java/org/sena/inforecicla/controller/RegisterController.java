package org.sena.inforecicla.controller;

import jakarta.validation.Valid;
import org.sena.inforecicla.dto.usuario.RegistroCiudadanoDTO;
import org.sena.inforecicla.dto.usuario.RegistroPuntoEcaDTO;
import org.sena.inforecicla.repository.LocalidadRepository;
import org.sena.inforecicla.service.UsuarioService;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.security.core.Authentication;
import org.springframework.stereotype.Controller;
import org.springframework.ui.Model;
import org.springframework.validation.BindingResult;
import org.springframework.web.bind.annotation.*;

@Controller
@RequestMapping("/registro")
public class RegisterController {

    @Autowired
    private UsuarioService usuarioService;

    @Autowired
    private LocalidadRepository localidadRepository;

    // =============== REGISTRO CIUDADANO ===============

    @GetMapping("/ciudadano")
    public String formularioCiudadano(Model model, Authentication authentication) {
        // Si ya está autenticado, redirige
        if (authentication != null && authentication.isAuthenticated()) {
            return "redirect:/";
        }

        model.addAttribute("registroCiudadanoDTO", new RegistroCiudadanoDTO(
                "", "", "", "", "", "", null, null, null, "Bogotá", ""
        ));
        model.addAttribute("localidades", localidadRepository.findAll());

        return "views/Auth/registro-ciudadano";
    }

    @PostMapping("/ciudadano")
    public String registrarCiudadano(
            @Valid @ModelAttribute RegistroCiudadanoDTO registroDTO,
            BindingResult bindingResult,
            Model model) {

        if (bindingResult.hasErrors()) {
            model.addAttribute("localidades", localidadRepository.findAll());
            return "views/Auth/registro-ciudadano";
        }

        try {
            usuarioService.registrarCiudadano(registroDTO);
            return "redirect:/login?registro=success";
        } catch (RuntimeException e) {
            // Log del error para debugging
            System.err.println("Error en registro de ciudadano: " + e.getMessage());
            e.printStackTrace();

            model.addAttribute("error", e.getMessage());
            model.addAttribute("registroCiudadanoDTO", registroDTO);
            model.addAttribute("localidades", localidadRepository.findAll());
            return "views/Auth/registro-ciudadano";
        }
    }

    // =============== REGISTRO PUNTO ECA ===============

    @GetMapping("/eca")
    public String formularioPuntoEca(Model model, Authentication authentication) {
        // Si ya está autenticado, redirige
        if (authentication != null && authentication.isAuthenticated()) {
            return "redirect:/";
        }

        model.addAttribute("registroPuntoEcaDTO", new RegistroPuntoEcaDTO(
                "", "", "", "", "", "", null, null, "", "Bogotá", "", null, null, ""
        ));
        model.addAttribute("localidades", localidadRepository.findAll());

        return "views/Auth/registro-eca";
    }

    @PostMapping("/eca")
    public String registrarPuntoEca(
            @Valid @ModelAttribute RegistroPuntoEcaDTO registroDTO,
            BindingResult bindingResult,
            Model model) {

        if (bindingResult.hasErrors()) {
            model.addAttribute("localidades", localidadRepository.findAll());
            return "views/Auth/registro-eca";
        }

        try {
            usuarioService.registrarPuntoECA(registroDTO);
            return "redirect:/login?registro=success";
        } catch (RuntimeException e) {
            // Log del error para debugging
            System.err.println("Error en registro de Punto ECA: " + e.getMessage());
            e.printStackTrace();

            model.addAttribute("error", e.getMessage());
            model.addAttribute("registroPuntoEcaDTO", registroDTO);
            model.addAttribute("localidades", localidadRepository.findAll());
            return "views/Auth/registro-eca";
        }
    }
}

