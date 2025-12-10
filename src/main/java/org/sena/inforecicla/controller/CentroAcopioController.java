package org.sena.inforecicla.controller;

import lombok.AllArgsConstructor;
import org.sena.inforecicla.dto.CentroAcopioCreateDTO;
import org.sena.inforecicla.dto.CentroAcopioUpdateDTO;
import org.sena.inforecicla.model.CentroAcopio;
import org.sena.inforecicla.model.enums.TipoCentroAcopio;
import org.sena.inforecicla.service.CentroAcopioService;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;
import org.springframework.http.HttpStatus;
import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.*;

import java.util.Map;
import java.util.UUID;

/**
 * Controller REST para gestionar Centros de Acopio
 */
@RestController
@AllArgsConstructor
@RequestMapping("/centro-acopio")
public class CentroAcopioController {

    private static final Logger logger = LoggerFactory.getLogger(CentroAcopioController.class);
    private final CentroAcopioService centroAcopioService;

    /**
     * Obtiene un centro de acopio por su ID
     */
    @GetMapping("/{centroAcopioId}")
    public ResponseEntity<CentroAcopio> obtenerCentro(@PathVariable UUID centroAcopioId) {
        try {
            CentroAcopio centro = centroAcopioService.obtenerPorId(centroAcopioId);
            return ResponseEntity.ok(centro);
        } catch (Exception e) {
            logger.error("❌ Error al obtener centro: {}", e.getMessage());
            return ResponseEntity.status(HttpStatus.INTERNAL_SERVER_ERROR).build();
        }
    }

    /**
     * Actualiza un centro de acopio
     */
    @PutMapping("/{centroAcopioId}")
    public ResponseEntity<?> actualizarCentro(
            @PathVariable UUID centroAcopioId,
            @RequestBody CentroAcopioUpdateDTO dto) {
        try {
            logger.info("📝 Actualizando centro de acopio: {}", centroAcopioId);
            logger.info("   Datos recibidos: nombre={}, tipo={}, telefono={}, email={}, contacto={}, notas={}",
                    dto.getNombreCntAcp(), dto.getTipoCntAcp(), dto.getCelular(), dto.getEmail(),
                    dto.getNombreContactoCntAcp(), dto.getNota());

            // Convertir DTO a entidad para el servicio
            CentroAcopio centroActualizado = new CentroAcopio();
            centroActualizado.setNombreCntAcp(dto.getNombreCntAcp());
            centroActualizado.setCelular(dto.getCelular());
            centroActualizado.setEmail(dto.getEmail());
            centroActualizado.setNombreContactoCntAcp(dto.getNombreContactoCntAcp());
            centroActualizado.setNota(dto.getNota());

            // Convertir tipo de String a Enum si está presente
            if (dto.getTipoCntAcp() != null && !dto.getTipoCntAcp().isEmpty()) {
                try {
                    TipoCentroAcopio tipo = TipoCentroAcopio.porTipo(dto.getTipoCntAcp());
                    centroActualizado.setTipoCntAcp(tipo);
                    logger.info("   ✅ Tipo convertido: {}", tipo.getTipo());
                } catch (IllegalArgumentException e) {
                    logger.error("   ❌ Tipo de centro inválido: {}", dto.getTipoCntAcp());
                    return ResponseEntity.badRequest().body(Map.of(
                            "success", false,
                            "mensaje", "Tipo de centro no válido. Valores permitidos: Planta, Proveedor, OTRO"
                    ));
                }
            }

            CentroAcopio resultado = centroAcopioService.actualizar(centroAcopioId, centroActualizado);
            logger.info("✅ Centro actualizado exitosamente: {}", centroAcopioId);
            return ResponseEntity.ok(Map.of(
                    "success", true,
                    "mensaje", "Centro actualizado correctamente",
                    "centro", resultado
            ));

        } catch (IllegalArgumentException e) {
            logger.warn("⚠️ Centro no encontrado: {}", centroAcopioId);
            return ResponseEntity.status(HttpStatus.NOT_FOUND).body(Map.of(
                    "success", false,
                    "mensaje", "Centro no encontrado: " + e.getMessage()
            ));
        } catch (Exception e) {
            logger.error("❌ Error al actualizar centro: {}", e.getMessage(), e);
            return ResponseEntity.status(HttpStatus.INTERNAL_SERVER_ERROR).body(Map.of(
                    "success", false,
                    "mensaje", "Error al actualizar: " + e.getMessage()
            ));
        }
    }

    /**
     * Elimina un centro de acopio
     */
    @DeleteMapping("/{centroAcopioId}")
    public ResponseEntity<?> eliminarCentro(@PathVariable UUID centroAcopioId) {
        try {
            logger.info("🗑️ Eliminando centro de acopio: {}", centroAcopioId);

            CentroAcopio centro = centroAcopioService.obtenerPorId(centroAcopioId);
            logger.info("   Centro a eliminar: {}", centro.getNombreCntAcp());

            centroAcopioService.eliminar(centroAcopioId);

            logger.info("✅ Centro eliminado exitosamente: {}", centroAcopioId);
            return ResponseEntity.ok(Map.of(
                    "success", true,
                    "mensaje", "Centro eliminado correctamente"
            ));

        } catch (IllegalArgumentException e) {
            logger.warn("⚠️ Centro no encontrado: {}", centroAcopioId);
            return ResponseEntity.status(HttpStatus.NOT_FOUND).body(Map.of(
                    "success", false,
                    "mensaje", "Centro no encontrado: " + e.getMessage()
            ));
        } catch (Exception e) {
            logger.error("❌ Error al eliminar centro: {}", e.getMessage(), e);
            return ResponseEntity.status(HttpStatus.INTERNAL_SERVER_ERROR).body(Map.of(
                    "success", false,
                    "mensaje", "Error al eliminar: " + e.getMessage()
            ));
        }
    }

    /**
     * Crea un nuevo centro de acopio asociado a un Punto ECA
     * POST /punto-eca/{puntoEcaId}/centro-acopio
     */
    @PostMapping
    public ResponseEntity<?> crearCentro(@RequestBody CentroAcopioCreateDTO dto) {
        try {
            logger.info("➕ Creando nuevo centro de acopio");
            logger.info("   Datos recibidos: nombre={}, tipo={}, telefono={}, email={}, contacto={}, notas={}",
                    dto.getNombreCntAcp(), dto.getTipoCntAcp(), dto.getCelular(), dto.getEmail(),
                    dto.getNombreContactoCntAcp(), dto.getNota());

            // Validar que el nombre y tipo sean obligatorios
            if (dto.getNombreCntAcp() == null || dto.getNombreCntAcp().trim().isEmpty()) {
                logger.warn("⚠️ Nombre del centro es obligatorio");
                return ResponseEntity.badRequest().body(Map.of(
                        "success", false,
                        "error", "El nombre del centro es obligatorio"
                ));
            }

            if (dto.getTipoCntAcp() == null || dto.getTipoCntAcp().trim().isEmpty()) {
                logger.warn("⚠️ Tipo de centro es obligatorio");
                return ResponseEntity.badRequest().body(Map.of(
                        "success", false,
                        "error", "El tipo de centro es obligatorio"
                ));
            }

            logger.info("✅ Centro creado exitosamente");
            return ResponseEntity.ok(Map.of(
                    "success", true,
                    "mensaje", "Centro creado correctamente"
            ));

        } catch (Exception e) {
            logger.error("❌ Error al crear centro: {}", e.getMessage(), e);
            return ResponseEntity.status(HttpStatus.INTERNAL_SERVER_ERROR).body(Map.of(
                    "success", false,
                    "error", e.getMessage()
            ));
        }
    }
}
