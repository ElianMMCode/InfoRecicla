package org.sena.inforecicla.controller;

import jakarta.validation.Valid;
import lombok.AllArgsConstructor;
import org.sena.inforecicla.dto.puntoEca.inventario.InventarioGuardarDTO;
import org.sena.inforecicla.dto.puntoEca.inventario.InventarioUpdateDTO;
import org.sena.inforecicla.dto.puntoEca.materiales.MaterialInvResponseDTO;
import org.sena.inforecicla.dto.usuario.UsuarioGestorResponseDTO;
import org.sena.inforecicla.exception.InventarioFoundExistException;
import org.sena.inforecicla.exception.InventarioNotFoundException;
import org.sena.inforecicla.exception.MaterialNotFoundException;
import org.sena.inforecicla.exception.PuntoEcaNotFoundException;
import org.sena.inforecicla.model.enums.Alerta;
import org.sena.inforecicla.model.enums.UnidadMedida;
import org.sena.inforecicla.service.GestorEcaService;
import org.sena.inforecicla.service.InventarioService;
import org.springframework.http.HttpStatus;
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
        // Usar el path variable nombrePunto para evitar warning por parámetro no usado
        model.addAttribute("gestor", nombrePunto);
        model.addAttribute("seccion", "resumen");
        model.addAttribute("inventarios", inventarioService.mostrarInventarioPuntoEca(usuario.puntoEcaId()));
        return "views/PuntoECA/puntoECA-layout";
    }

    // Navegación por path con filtros: /punto-eca/{nombrePunto}/{gestorId}/{seccion}?texto=...&alerta=...
    @GetMapping("/{nombrePunto}/{gestorId}/{seccion}")
    public String puntoEca(
            @PathVariable String nombrePunto,
            @PathVariable UUID gestorId,
            @PathVariable String seccion,
            @RequestParam(required = false) String texto,
            @RequestParam(required = false) String categoria,
            @RequestParam(required = false) String tipo,
            @RequestParam(required = false) Alerta alerta,
            @RequestParam(required = false) String unidad,
            @RequestParam(required = false) String ocupacion,
            Model model
    ) {
        // Normalizar sección a minúsculas para que coincida con los casos del layout
        seccion = seccion != null ? seccion.toLowerCase() : "resumen";

        // Referencia de path variables para evitar warnings
        Objects.requireNonNull(nombrePunto);

        // Obtener información del usuario/gestor
        UsuarioGestorResponseDTO usuario = gestorEcaService.buscarGestorPuntoEca(gestorId);

        // Aquí filtras el inventario según los parámetros
        List<?> inventarioFiltrado = Collections.emptyList();
        String mensajeAlerta = null;

        // Verificar si hay algún filtro activo
        boolean hayFiltros = (texto != null && !texto.trim().isEmpty()) ||
                             (categoria != null && !categoria.trim().isEmpty()) ||
                             (tipo != null && !tipo.trim().isEmpty()) ||
                             (alerta != null) ||
                             (unidad != null && !unidad.trim().isEmpty()) ||
                             (ocupacion != null && !ocupacion.trim().isEmpty());

        try {
            if (hayFiltros) {
                // Si hay filtros, aplicar búsqueda filtrada
                List<?> resultado = inventarioService.filtraInventario(usuario.puntoEcaId(), texto, categoria, tipo, alerta, unidad, ocupacion);

                if (resultado == null || resultado.isEmpty()) {
                    mensajeAlerta = "No se encontraron coincidencias con los filtros aplicados.";
                } else {
                    inventarioFiltrado = resultado;
                }
            } else {
                // Si NO hay filtros, mostrar todos los materiales del inventario
                inventarioFiltrado = inventarioService.mostrarInventarioPuntoEca(usuario.puntoEcaId());
            }
        } catch (Exception e) {
            // Error en la búsqueda
            mensajeAlerta = "No se encontraron coincidencias con los filtros aplicados.";
        }

        Map<String, Object> atributos = Map.of(
                "usuario", usuario,
                "gestor", nombrePunto,
                "seccion", seccion,
                "inventario", inventarioFiltrado,
                "categoriaMateriales", inventarioService.listarCategoriasMateriales(),
                "tiposMateriales", inventarioService.listarTiposMateriales(),
                "unidadesMedida", Arrays.stream(UnidadMedida.values()).map(unidadMd -> {
                    Map<String, String> map = new HashMap<>();
                    map.put("clave", unidadMd.name());
                    map.put("nombre", unidadMd.getNombre());
                    return map;
                }).toList(),
                "alerta", Arrays.stream(Alerta.values()).map(alertaTp -> {
                    Map<String, String> map2 = new HashMap<>();
                    map2.put("clave", alertaTp.name());
                    map2.put("tipo", alertaTp.getTipo());
                    return map2;
                }).toList()
        );

        // Y cargas los materiales filtrados
        model.addAllAttributes(atributos);

        // Agregar mensaje de alerta si existe
        if (mensajeAlerta != null) {
            model.addAttribute("mensajeAlerta", mensajeAlerta);
        }

        return "views/PuntoECA/puntoECA-layout";
    }

    // Endpoint REST: búsqueda de materiales - Devuelve JSON
    @GetMapping("/catalogo/materiales/buscar")
    @ResponseBody
    public ResponseEntity<?> buscarMateriales(
            @RequestParam UUID puntoId,
            @RequestParam(required = false, defaultValue = "") String texto,
            @RequestParam(required = false, defaultValue = "") String categoria,
            @RequestParam(required = false, defaultValue = "") String tipo
    ) {
        try {
            List<MaterialInvResponseDTO> resultados = inventarioService.buscarMaterialFiltrandoInventario(puntoId, texto, categoria, tipo);
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

    // Endpoint: Eliminar material del inventario
    @DeleteMapping("/{nombrePunto}/{gestorId}/eliminar-inventario/{inventarioId}")
    @ResponseBody
    public ResponseEntity<?> eliminarInventario(
            @PathVariable String nombrePunto,
            @PathVariable UUID gestorId,
            @PathVariable UUID inventarioId
    ) {
        try {
            inventarioService.eliminarInventario(inventarioId);
            return ResponseEntity.ok(Map.of(
                    "success", true,
                    "mensaje", "Material eliminado del inventario correctamente"
            ));
        } catch (InventarioNotFoundException e) {
            return ResponseEntity.status(HttpStatus.NOT_FOUND).body(Map.of(
                    "error", true,
                    "mensaje", "Inventario no encontrado: " + e.getMessage()
            ));
        } catch (Exception e) {
            return ResponseEntity.status(HttpStatus.INTERNAL_SERVER_ERROR).body(Map.of(
                    "error", true,
                    "mensaje", "Error al eliminar inventario: " + e.getMessage()
            ));
        }
    }

}
