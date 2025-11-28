package org.sena.inforecicla.controller;

import lombok.AllArgsConstructor;
import org.sena.inforecicla.dto.puntoEca.InventarioUpdateDTO;
import org.sena.inforecicla.dto.usuario.UsuarioGestorResponseDTO;
import org.sena.inforecicla.model.enums.UnidadMedida;
import org.sena.inforecicla.service.GestorEcaService;
import org.sena.inforecicla.service.InventarioService;
import org.sena.inforecicla.exception.InventarioNotFoundException;
import org.springframework.http.ResponseEntity;
import org.springframework.stereotype.Controller;
import org.springframework.ui.Model;
import org.springframework.web.bind.annotation.*;

import java.util.*;

@Controller
@AllArgsConstructor
@RequestMapping("/punto-eca")
public class PuntoEcaController {

    private final GestorEcaService gestorEcaService;
    private final InventarioService inventarioService;

    // Vista principal con usuarioId
    @GetMapping("/{nombrePunto}/{gestorId}")
    public String puntoEca(@PathVariable String nombrePunto, @PathVariable UUID gestorId, Model model) {
        UsuarioGestorResponseDTO usuario = gestorEcaService.buscarGestorPuntoEca(gestorId);

        model.addAttribute("usuario", usuario);
        model.addAttribute("gestor", usuario.nombrePunto());
        model.addAttribute("seccion", "resumen");
        model.addAttribute("inventarios", inventarioService.mostrarInventariosPuntoEca(usuario.puntoEcaId()));
        return "views/PuntoECA/puntoECA-layout";
    }

    // Navegación: /punto-eca/{usuarioId}?seccion=materiales
    @GetMapping(value = "/{nombrePunto}/{gestorId}", params = "seccion")
    public String puntoEcaPorQuery(@PathVariable String nombrePunto, @PathVariable UUID gestorId, @RequestParam String seccion, Model model) {
        UsuarioGestorResponseDTO usuario = gestorEcaService.buscarGestorPuntoEca(gestorId);

        model.addAttribute("usuario", usuario);
        model.addAttribute("gestor", usuario.nombrePunto());
        model.addAttribute("seccion", seccion);
        model.addAttribute("inventarios", inventarioService.mostrarInventariosPuntoEca(usuario.puntoEcaId()));
        return "views/PuntoECA/puntoECA-layout";
    }

    // Navegación por path: /punto-eca/{usuarioId}/{seccion}
    @GetMapping("/{nombrePunto}/{usuarioId}/{seccion}")
    public String puntoEcaPorPath(@PathVariable UUID usuarioId, @PathVariable String seccion, Model model) {
        UsuarioGestorResponseDTO usuario = gestorEcaService.buscarGestorPuntoEca(usuarioId);

        model.addAttribute("usuario", usuario);
        model.addAttribute("gestor", usuario.nombrePunto());
        model.addAttribute("seccion", seccion);
        model.addAttribute("inventarios", inventarioService.mostrarInventariosPuntoEca(usuario.puntoEcaId()));
        return "views/PuntoECA/puntoECA-layout";
    }


    // Endpoint PUT: actualizar inventario de un material
    // Ruta: /punto-eca/{gestor}/{usuarioId}/actualizar-inventario/{inventarioId}
    @PutMapping("/{nombrePunto}/{usuarioId}/actualizar-inventario/{inventarioId}")
    public ResponseEntity<?> actualizarInventario(
            @PathVariable String nombrePunto,
            @PathVariable UUID usuarioId,
            @PathVariable UUID inventarioId,
            @RequestBody InventarioUpdateDTO dto
    ) throws InventarioNotFoundException {
        var resultado = inventarioService.actualizarInventario(inventarioId, dto);
        return ResponseEntity.ok(resultado);
    }

}

// Controlador REST separado para la API
@RestController
@RequestMapping("/api")
@AllArgsConstructor
class ApiController {

    // Endpoint REST: obtener todas las unidades de medida disponibles
    @GetMapping("/unidades-medida")
    public List<Map<String, String>> obtenerUnidadesMedida() {
        return Arrays.stream(UnidadMedida.values()).map(unidad -> {
            Map<String, String> map = new HashMap<>();
            map.put("clave", unidad.name());
            map.put("nombre", unidad.getNombre());
            return map;
        }).toList();
    }
}

