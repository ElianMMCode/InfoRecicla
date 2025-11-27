package org.sena.inforecicla.controller;

import lombok.AllArgsConstructor;
import org.sena.inforecicla.dto.usuario.UsuarioGestorResponseDTO;
import org.sena.inforecicla.service.GestorEcaService;
import org.sena.inforecicla.service.InventarioService;
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
    private final InventarioService inventarioService;

    // Vista principal con usuarioId
    @GetMapping("/{nombrePunto}/{gestorId}")
    public String puntoEca(
            @PathVariable String nombrePunto,
            @PathVariable UUID gestorId,
            Model model
    ) {
        UsuarioGestorResponseDTO usuario = gestorEcaService.buscarGestorPuntoEca(gestorId);

        model.addAttribute("usuario", usuario);
        model.addAttribute("gestor", usuario.nombrePunto());
        model.addAttribute("seccion", "resumen");
        model.addAttribute("inventarios", inventarioService.mostrarInventariosPuntoEca(usuario.puntoEcaId()));
        return "views/PuntoECA/puntoECA-layout";
    }

    // Navegación: /punto-eca/{usuarioId}?seccion=materiales
    @GetMapping(value = "/{nombrePunto}/{gestorId}", params = "seccion")
    public String puntoEcaPorQuery(
            @PathVariable String nombrePunto,
            @PathVariable UUID gestorId,
            @RequestParam String seccion,
            Model model
            ) {
        UsuarioGestorResponseDTO usuario = gestorEcaService.buscarGestorPuntoEca(gestorId);

        model.addAttribute("usuario", usuario);
        model.addAttribute("gestor", usuario.nombrePunto());
        model.addAttribute("seccion", seccion);
        model.addAttribute("inventarios", inventarioService.mostrarInventariosPuntoEca(usuario.puntoEcaId()));
        return "views/PuntoECA/puntoECA-layout";
    }

    // Navegación por path: /punto-eca/{usuarioId}/{seccion}
    @GetMapping("/{nombrePunto}/{usuarioId}/{seccion}")
    public String puntoEcaPorPath(
            @PathVariable UUID usuarioId,
            @PathVariable String seccion,
            Model model
    ) {
        UsuarioGestorResponseDTO usuario = gestorEcaService.buscarGestorPuntoEca(usuarioId);

        model.addAttribute("usuario", usuario);
        model.addAttribute("gestor", usuario.nombrePunto());
        model.addAttribute("seccion", seccion);
        model.addAttribute("inventarios", inventarioService.mostrarInventariosPuntoEca(usuario.puntoEcaId()));
        return "views/PuntoECA/puntoECA-layout";
    }
}
