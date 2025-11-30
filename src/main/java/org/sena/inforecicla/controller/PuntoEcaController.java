package org.sena.inforecicla.controller;

import jakarta.validation.Valid;
import lombok.AllArgsConstructor;
import org.sena.inforecicla.dto.puntoEca.inventario.InventarioGuardarDTO;
import org.sena.inforecicla.dto.puntoEca.inventario.InventarioUpdateDTO;
import org.sena.inforecicla.dto.puntoEca.materiales.MaterialInvResponseDTO;
import org.sena.inforecicla.dto.usuario.UsuarioGestorResponseDTO;
import org.sena.inforecicla.exception.InventarioFoundExistException;
import org.sena.inforecicla.exception.MaterialNotFoundException;
import org.sena.inforecicla.exception.PuntoEcaNotFoundException;
import org.sena.inforecicla.model.enums.UnidadMedida;
import org.sena.inforecicla.service.GestorEcaService;
import org.sena.inforecicla.service.InventarioService;
import org.sena.inforecicla.exception.InventarioNotFoundException;
import org.springframework.http.HttpStatus;
import org.springframework.http.ResponseEntity;
import org.springframework.stereotype.Controller;
import org.springframework.ui.Model;
import org.springframework.web.bind.annotation.*;
import org.springframework.web.bind.annotation.RestController;

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
        // Usar el path variable nombrePunto para evitar warning por parámetro no usado
        model.addAttribute("gestor", nombrePunto);
        model.addAttribute("seccion", "resumen");
        model.addAttribute("inventarios", inventarioService.mostrarInventarioPuntoEca(usuario.puntoEcaId()));
        return "views/PuntoECA/puntoECA-layout";
    }


    // Navegación por path: /punto-eca/{usuarioId}/{seccion}
    @GetMapping("/{nombrePunto}/{gestorId}/{seccion}")
    public String puntoEca( @PathVariable String nombrePunto, @PathVariable UUID gestorId, @PathVariable String seccion, Model model) {
        //Información Gestor y Punto Eca
        UsuarioGestorResponseDTO usuario = gestorEcaService.buscarGestorPuntoEca(gestorId);
        model.addAttribute("usuario", usuario);
        // Usar el path variable nombrePunto para evitar warning por parámetro no usado
        model.addAttribute("gestor", nombrePunto);
        model.addAttribute("seccion", seccion);
        //Información Inventario
        if (seccion.equalsIgnoreCase("Materiales")) {
            Map<String, Object> atributos = Map.of(
                    "inventario", inventarioService.mostrarInventarioPuntoEca(usuario.puntoEcaId()),
                    "categoriaMateriales", inventarioService.listarCategoriasMateriales(),
                    "tiposMateriales", inventarioService.listarTiposMateriales(),
                    "unidadesMedida", Arrays.stream(UnidadMedida.values()).map(unidad -> {
                        Map<String, String> map = new HashMap<>();
                        map.put("clave", unidad.name());
                        map.put("nombre", unidad.getNombre());
                        return map;
                    }).toList()
            );

            model.addAllAttributes(atributos);
        }
        return "views/PuntoECA/puntoECA-layout";
    }

    // Endpoint REST: búsqueda de materiales - Devuelve JSON
    @GetMapping("/catalogo/materiales/buscar")
    @ResponseBody
    public ResponseEntity<?> buscarMateriales(
            @RequestParam UUID puntoId,
            @RequestParam (required = false, defaultValue = "") String texto,
            @RequestParam (required = false, defaultValue = "") String categoria,
            @RequestParam (required = false, defaultValue = "") String tipo
    ){
        try {
            List<MaterialInvResponseDTO> resultados = inventarioService.buscarMaterial(puntoId, texto, categoria, tipo);
            return ResponseEntity.ok(resultados);
        } catch (InventarioFoundExistException e) {
            // Devolver el mensaje de error de forma legible al frontend
            return ResponseEntity.badRequest().body(Map.of(
                "error", true,
                "mensaje", e.getMessage()
            ));
        } catch (Exception e) {
            return ResponseEntity.internalServerError().body(Map.of(
                "error", true,
                "mensaje", "Error al buscar materiales: " + e.getMessage()
            ));
        }
    }


    // Ruta: /punto-eca/{gestor}/{usuarioId}/actualizar-inventario/{inventarioId}
    @PutMapping("/{nombrePunto}/{usuarioId}/actualizar-inventario/{inventarioId}")
    public ResponseEntity<?> actualizarInventario(
            @PathVariable String nombrePunto,
            @PathVariable UUID usuarioId,
            @PathVariable UUID inventarioId,
            @RequestBody InventarioUpdateDTO dto
    ) throws InventarioNotFoundException {
        // Referenciar los path variables para evitar warnings de parámetros no usados
        Objects.requireNonNull(nombrePunto);
        Objects.requireNonNull(usuarioId);

        var resultado = inventarioService.actualizarInventario(inventarioId, dto);
        return ResponseEntity.ok(resultado);
    }

    @PostMapping("/inventario/agregar")
    @ResponseBody
    public ResponseEntity<?> agregarInventario(
            @Valid @RequestBody InventarioGuardarDTO dto
    ) {
        try {
            inventarioService.guardarInventario(dto);
            return ResponseEntity.status(HttpStatus.CREATED).body(Map.of(
                    "error", false,
                    "mensaje", "Inventario guardado exitosamente"
            ));
        } catch (MaterialNotFoundException | PuntoEcaNotFoundException e) {
            System.err.println("❌ Entidad no encontrada: " + e.getMessage());
            e.printStackTrace();
            return ResponseEntity.status(HttpStatus.NOT_FOUND).body(Map.of(
                    "error", true,
                    "mensaje", e.getMessage()
            ));
        } catch (Exception e) {
            System.err.println("❌ Error al guardar inventario: " + e.getMessage());
            System.err.println("Stack trace:");
            e.printStackTrace();
            return ResponseEntity.internalServerError().body(Map.of(
                    "error", true,
                    "mensaje", "Error al guardar inventario: " + e.getMessage(),
                    "detalles", e.getClass().getSimpleName()
            ));
        }
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
