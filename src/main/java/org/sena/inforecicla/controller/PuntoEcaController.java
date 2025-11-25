package org.sena.inforecicla.controller;

import lombok.AllArgsConstructor;
import org.sena.inforecicla.dto.usuario.UsuarioGestorResponseDTO;
import org.sena.inforecicla.service.GestorEcaService;
import org.springframework.stereotype.Controller;
import org.springframework.ui.Model;
import org.springframework.web.bind.annotation.GetMapping;
import org.springframework.web.bind.annotation.PathVariable;
import org.springframework.web.bind.annotation.RequestMapping;
import org.springframework.web.bind.annotation.RequestParam;

import java.util.UUID;

@Controller
@AllArgsConstructor
@RequestMapping("/punto-eca")
public class PuntoEcaController {

    private final GestorEcaService gestorEcaService;

    // Vista principal con usuarioId
    @GetMapping("/{usuarioId}")
    public String puntoEca(
            @PathVariable UUID usuarioId,
            Model model
    ) {
        UsuarioGestorResponseDTO usuario = gestorEcaService.buscarGestorPuntoEca(usuarioId);

        model.addAttribute("usuario", usuario);
        model.addAttribute("gestor", usuario);
        model.addAttribute("seccion", "resumen");
        return "views/PuntoECA/puntoECA-layout";
    }

    // Navegación: /punto-eca/{usuarioId}?seccion=materiales
    @GetMapping(value = "/{usuarioId}", params = "seccion")
    public String puntoEcaPorQuery(
            @PathVariable UUID usuarioId,
            @RequestParam String seccion,
            Model model
    ) {
        UsuarioGestorResponseDTO usuario = gestorEcaService.buscarGestorPuntoEca(usuarioId);

        model.addAttribute("usuario", usuario);
        model.addAttribute("gestor", usuario);
        model.addAttribute("seccion", seccion);
        return "views/PuntoECA/puntoECA-layout";
    }

    // Navegación por path: /punto-eca/{usuarioId}/{seccion}
    @GetMapping("/{usuarioId}/{seccion}")
    public String puntoEcaPorPath(
            @PathVariable UUID usuarioId,
            @PathVariable String seccion,
            Model model
    ) {
        UsuarioGestorResponseDTO usuario = gestorEcaService.buscarGestorPuntoEca(usuarioId);

        model.addAttribute("usuario", usuario);
        model.addAttribute("gestor", usuario);
        model.addAttribute("seccion", seccion);
        return "views/PuntoECA/puntoECA-layout";
    }
}
