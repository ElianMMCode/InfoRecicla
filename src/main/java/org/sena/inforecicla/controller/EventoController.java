package org.sena.inforecicla.controller;

import lombok.AllArgsConstructor;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;
import org.springframework.http.HttpStatus;
import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.*;
import org.sena.inforecicla.model.*;
import org.sena.inforecicla.model.enums.TipoRepeticion;
import org.sena.inforecicla.repository.*;

import java.time.LocalDateTime;
import java.time.format.DateTimeFormatter;
import java.math.BigDecimal;
import java.util.*;

@RestController
@RequestMapping("/api/eventos")
@AllArgsConstructor
@CrossOrigin(origins = "*")
public class EventoController {

    private static final Logger logger = LoggerFactory.getLogger(EventoController.class);

    // Inyectar repositorios
    private final MaterialRepository materialRepository;
    private final PuntoEcaRepository puntoRepository;
    private final UsuarioRepository usuarioRepository;
    private final CentroAcopioRepository centroAcopioRepository;
    private final EventoRepository eventoRepository;
    private final VentasInventarioRepository ventasInventarioRepository;
    private final InventarioRepository inventarioRepository;

    /**
     * Crear un nuevo evento de venta
     */
    @PostMapping("/crear-venta")
    public ResponseEntity<?> crearEventoVenta(@RequestBody Map<String, Object> request) {
        try {
            logger.info("🎯 Creando evento de venta");
            logger.info("📦 Datos recibidos: {}", request);

            // Extraer datos del request
            String materialId = (String) request.get("materialId");
            String puntoEcaId = (String) request.get("puntoEcaId");
            String usuarioId = (String) request.get("usuarioId");
            String centroAcopioId = (String) request.get("centroAcopioId");
            String titulo = (String) request.get("titulo");
            String descripcion = (String) request.get("descripcion");
            String fechaInicio = (String) request.get("fechaInicio");
            String fechaFin = (String) request.get("fechaFin");
            String tipoRepeticion = (String) request.get("tipoRepeticion");
            String color = (String) request.get("color");

            // Validaciones
            if (materialId == null || materialId.isEmpty()) {
                logger.warn("❌ Material es obligatorio");
                return ResponseEntity.badRequest().body(Map.of("error", "Material es obligatorio"));
            }
            if (titulo == null || titulo.isEmpty()) {
                logger.warn("❌ Título es obligatorio");
                return ResponseEntity.badRequest().body(Map.of("error", "Título es obligatorio"));
            }
            if (puntoEcaId == null || usuarioId == null) {
                logger.warn("❌ Punto y usuario son obligatorios");
                return ResponseEntity.badRequest().body(Map.of("error", "Punto y usuario son obligatorios"));
            }

            // Obtener entidades desde la BD
            UUID materialUUID = UUID.fromString(materialId);
            UUID puntoUUID = UUID.fromString(puntoEcaId);
            UUID usuarioUUID = UUID.fromString(usuarioId);

            Material material = materialRepository.findById(materialUUID)
                    .orElseThrow(() -> new IllegalArgumentException("Material no encontrado"));
            PuntoECA punto = puntoRepository.findById(puntoUUID)
                    .orElseThrow(() -> new IllegalArgumentException("Punto ECA no encontrado"));
            Usuario usuario = usuarioRepository.findById(usuarioUUID)
                    .orElseThrow(() -> new IllegalArgumentException("Usuario no encontrado"));

            CentroAcopio centro = null;
            if (centroAcopioId != null && !centroAcopioId.isEmpty()) {
                UUID centroUUID = UUID.fromString(centroAcopioId);
                centro = centroAcopioRepository.findById(centroUUID).orElse(null);
            }

            logger.info("✅ Entidades obtenidas:");
            logger.info("   Material: {}", material.getDescripcion());
            logger.info("   Punto: {}", punto.getNombrePunto());
            logger.info("   Usuario: {} {}", usuario.getNombres(), usuario.getApellidos());
            logger.info("   Centro: {}", centro != null ? centro.getNombreCntAcp() : "Ninguno");

            // Parsear fechas
            DateTimeFormatter formatter = DateTimeFormatter.ISO_DATE_TIME;
            LocalDateTime fechaInicioLDT = LocalDateTime.parse(fechaInicio, formatter);
            LocalDateTime fechaFinLDT = LocalDateTime.parse(fechaFin, formatter);

            // Crear VentaInventario
            VentaInventario venta = VentaInventario.builder()
                    .fechaVenta(LocalDateTime.now())
                    .cantidad(BigDecimal.ZERO)
                    .precioVenta(BigDecimal.ZERO)
                    .ctrAcopio(centro)
                    .build();

            VentaInventario ventaGuardada = ventasInventarioRepository.save(venta);
            logger.info("✅ VentaInventario guardada: {}", ventaGuardada.getVentaId());

            // Crear Evento
            Evento evento = Evento.builder()
                    .ventaInventario(ventaGuardada)
                    .material(material)
                    .centroAcopio(centro)
                    .puntoEca(punto)
                    .usuario(usuario)
                    .titulo(titulo)
                    .descripcion(descripcion)
                    .fechaInicio(fechaInicioLDT)
                    .fechaFin(fechaFinLDT)
                    .color(color != null ? color : "#28a745")
                    .tipoRepeticion(TipoRepeticion.valueOf(tipoRepeticion != null ? tipoRepeticion : "SIN_REPETICION"))
                    .esEventoGenerado(false)
                    .build();

            Evento eventoGuardado = eventoRepository.save(evento);
            logger.info("✅ Evento guardado en BD: {}", eventoGuardado.getEventoId());

            return ResponseEntity.ok(Map.of(
                "success", true,
                "message", "Evento creado correctamente",
                "id", eventoGuardado.getEventoId(),
                "titulo", eventoGuardado.getTitulo(),
                "fechaInicio", eventoGuardado.getFechaInicio()
            ));

        } catch (IllegalArgumentException e) {
            logger.warn("⚠️ Error de validación: {}", e.getMessage());
            return ResponseEntity.badRequest().body(Map.of("error", e.getMessage()));
        } catch (Exception e) {
            logger.error("❌ Error creando evento: {}", e.getMessage());
            return ResponseEntity.status(HttpStatus.INTERNAL_SERVER_ERROR)
                .body(Map.of("error", "Error al crear evento: " + e.getMessage()));
        }
    }

    /**
     * Obtener eventos de un punto (formato FullCalendar)
     */
    @GetMapping("/punto/{puntoId}/eventos")
    public ResponseEntity<?> obtenerEventosPunto(@PathVariable String puntoId) {
        try {
            logger.info("Obteniendo eventos del punto: {}", puntoId);
            return ResponseEntity.ok(new ArrayList<>());
        } catch (Exception e) {
            logger.error("Error obteniendo eventos", e);
            return ResponseEntity.status(HttpStatus.INTERNAL_SERVER_ERROR)
                .body(Map.of("error", "Error al obtener eventos"));
        }
    }
}
