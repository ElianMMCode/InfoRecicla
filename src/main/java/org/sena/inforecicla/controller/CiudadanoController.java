package org.sena.inforecicla.controller;

import lombok.RequiredArgsConstructor;
import org.sena.inforecicla.dto.usuario.UsuarioResponseDTO;
import org.sena.inforecicla.service.CiudadanoService;
import org.springframework.stereotype.Controller;
import org.springframework.ui.Model;
import org.springframework.web.bind.annotation.GetMapping;
import org.springframework.web.bind.annotation.PathVariable;
import org.springframework.web.bind.annotation.PostMapping;
import org.springframework.web.bind.annotation.RequestParam;
import org.springframework.web.servlet.mvc.support.RedirectAttributes;

import java.util.UUID;

@Controller
@RequiredArgsConstructor
public class CiudadanoController {

    private final CiudadanoService ciudadanoService;

    @GetMapping("/ciudadano/{id}")
    public String ciudadano(@PathVariable UUID id, Model model) {
        UsuarioResponseDTO ciudadano = ciudadanoService.getCiudadano(id);
        model.addAttribute("ciudadano", ciudadano);
        return "ciudadano";
    }

    @PostMapping("/ciudadano/{id}")
    public String actualizarCiudadano(
            @PathVariable UUID id,
            @RequestParam String nombres,
            @RequestParam String apellidos,
            @RequestParam String email,
            @RequestParam String ciudad,
            @RequestParam String celular,
            @RequestParam(required = false) String fechaNacimiento,
            RedirectAttributes redirectAttributes) {

        UsuarioResponseDTO dto = new UsuarioResponseDTO(
                id,
                nombres,
                apellidos,
                null,
                null,
                null,
                fechaNacimiento,
                celular,
                email,
                ciudad,
                null,
                null
        );

        UsuarioResponseDTO actualizado = ciudadanoService.actualizarCiudadano(id, dto);
        redirectAttributes.addFlashAttribute("mensaje", "Ciudadano actualizado correctamente");
        return "redirect:/ciudadano/" + id;
    }
}
