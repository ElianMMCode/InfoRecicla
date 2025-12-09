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
import org.sena.inforecicla.service.EventoService;
import org.sena.inforecicla.dto.EventoVentaCreateDTO;

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
    private static final Map<String, LocalDateTime> ultimaVenta = new java.util.concurrent.ConcurrentHashMap<>();

    // Inyectar repositorios
    private final MaterialRepository materialRepository;
    private final PuntoEcaRepository puntoRepository;
    private final UsuarioRepository usuarioRepository;
    private final CentroAcopioRepository centroAcopioRepository;
    private final EventoRepository eventoRepository;
    private final VentasInventarioRepository ventasInventarioRepository;
    private final InventarioRepository inventarioRepository;
    private final EventoService eventoService;

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
            String fechaFinRepeticion = (String) request.get("fechaFinRepeticion");
            String color = (String) request.get("color");

            logger.info("📋 Datos recibidos del formulario:");
            logger.info("   Tipo de repetición: {}", tipoRepeticion);
            logger.info("   Fecha fin repetición recibida (raw): '{}'", fechaFinRepeticion);
            logger.info("   Fecha fin repetición es null: {}", fechaFinRepeticion == null);
            logger.info("   Fecha fin repetición está vacía: {}", fechaFinRepeticion != null && fechaFinRepeticion.isEmpty());
            logger.info("   Fecha fin repetición length: {}", fechaFinRepeticion != null ? fechaFinRepeticion.length() : 0);

            // Crear clave para debounce (evitar duplicados)
            String clave = usuarioId + "-" + materialId + "-" + titulo + "-" + fechaInicio;
            LocalDateTime ahora = LocalDateTime.now();

            // Verificar si hace poco se guardó un evento similar
            if (ultimaVenta.containsKey(clave)) {
                LocalDateTime ultimaGuardada = ultimaVenta.get(clave);
                if (ahora.isBefore(ultimaGuardada.plusSeconds(2))) {
                    logger.warn("⚠️ Evento duplicado detectado, ignorando (dentro de 2 segundos)");
                    return ResponseEntity.ok(Map.of(
                        "success", false,
                        "message", "Evento duplicado, por favor espere"
                    ));
                }
            }

            // Guardar tiempo de última venta
            ultimaVenta.put(clave, ahora);

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

            // Parsear fechaFinRepeticion si está presente
            LocalDateTime fechaFinRepeticionLDT = null;
            if (fechaFinRepeticion != null && !fechaFinRepeticion.isEmpty()) {
                try {
                    // Si viene como fecha (YYYY-MM-DD), convertir a LocalDateTime
                    if (fechaFinRepeticion.length() == 10) {
                        fechaFinRepeticionLDT = java.time.LocalDate.parse(fechaFinRepeticion)
                            .atTime(23, 59, 59); // Fin del día
                    } else {
                        fechaFinRepeticionLDT = LocalDateTime.parse(fechaFinRepeticion, formatter);
                    }
                    logger.info("   Fecha fin repetición: {}", fechaFinRepeticionLDT);
                } catch (Exception e) {
                    logger.warn("⚠️ Error parseando fechaFinRepeticion: {}", fechaFinRepeticion, e);
                }
            }

            // Crear VentaInventario
            VentaInventario venta = VentaInventario.builder()
                    .fechaVenta(LocalDateTime.now())
                    .cantidad(BigDecimal.ZERO)
                    .precioVenta(BigDecimal.ZERO)
                    .ctrAcopio(centro)
                    .build();

            VentaInventario ventaGuardada = ventasInventarioRepository.save(venta);
            logger.info("✅ VentaInventario guardada: {}", ventaGuardada.getVentaId());

            // Crear DTO para usar el servicio que genera instancias
            EventoVentaCreateDTO dto = EventoVentaCreateDTO.builder()
                    .materialId(materialUUID)
                    .puntoEcaId(puntoUUID)
                    .usuarioId(usuarioUUID)
                    .ventaInventarioId(ventaGuardada.getVentaId())
                    .centroAcopioId(centro != null ? centro.getCntAcpId() : null)
                    .titulo(titulo)
                    .descripcion(descripcion)
                    .fechaInicio(fechaInicioLDT)
                    .fechaFin(fechaFinLDT)
                    .tipoRepeticion(TipoRepeticion.valueOf(tipoRepeticion != null ? tipoRepeticion : "SIN_REPETICION"))
                    .fechaFinRepeticion(fechaFinRepeticionLDT) // Ahora con la fecha correcta
                    .color(color != null ? color : "#28a745")
                    .build();

            // Usar el servicio para crear el evento (que generará instancias automáticamente)
            Evento eventoGuardado = eventoService.crearEventoDesdeVenta(dto);
            logger.info("✅ Evento guardado en BD con instancias: {}", eventoGuardado.getEventoId());
            logger.info("   Tipo de repetición: {}", eventoGuardado.getTipoRepeticion());
            logger.info("   Total instancias generadas: {}", eventoGuardado.getInstancias() != null ? eventoGuardado.getInstancias().size() : 0);

            return ResponseEntity.ok(Map.of(
                "success", true,
                "message", "Evento creado correctamente",
                "id", eventoGuardado.getEventoId(),
                "titulo", eventoGuardado.getTitulo(),
                "fechaInicio", eventoGuardado.getFechaInicio(),
                "tipoRepeticion", eventoGuardado.getTipoRepeticion().getNombre(),
                "totalInstancias", eventoGuardado.getInstancias() != null ? eventoGuardado.getInstancias().size() : 0
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
    public ResponseEntity<?> obtenerEventosPunto(
            @PathVariable String puntoId,
            @RequestParam(required = false) String start,
            @RequestParam(required = false) String end) {
        try {
            logger.info("Obteniendo eventos del punto: {}", puntoId);

            // Obtener todos los eventos del punto
            List<Evento> eventos = eventoService.obtenerEventosPorPunto(puntoId);

            // Convertir a formato FullCalendar
            List<Map<String, Object>> eventosFormato = new ArrayList<>();

            // Primero, agregar los eventos base
            for (Evento evento : eventos) {
                logger.info("📍 Procesando evento: {} (Repetición: {})",
                    evento.getTitulo(),
                    evento.getTipoRepeticion() != null ? evento.getTipoRepeticion().getNombre() : "NINGUNA");

                Map<String, Object> map = new HashMap<>();
                map.put("id", evento.getEventoId());
                map.put("title", evento.getTitulo());
                map.put("start", evento.getFechaInicio());
                map.put("end", evento.getFechaFin());
                map.put("backgroundColor", evento.getColor() != null ? evento.getColor() : "#28a745");
                map.put("borderColor", evento.getColor() != null ? evento.getColor() : "#28a745");
                map.put("extendedProps", Map.of(
                    "descripcion", evento.getDescripcion() != null ? evento.getDescripcion() : "",
                    "material", evento.getVentaInventario() != null && evento.getVentaInventario().getInventario() != null
                        ? evento.getVentaInventario().getInventario().getMaterial().getNombre() : "",
                    "centro", evento.getCentroAcopio() != null ? evento.getCentroAcopio().getNombreCntAcp() : ""
                ));
                eventosFormato.add(map);

                // Si tiene repetición, agregar instancias
                if (evento.getTipoRepeticion() != null && !evento.getTipoRepeticion().getNombre().equals("SIN_REPETICION")) {
                    logger.info("📅 Evento repetido detectado: {}", evento.getTitulo());

                    // Obtener instancias del evento
                    List<EventoInstancia> instancias = evento.getInstancias();

                    logger.info("   Total instancias: {}", instancias != null ? instancias.size() : 0);

                    if (instancias != null && !instancias.isEmpty()) {
                        for (EventoInstancia instancia : instancias) {
                            logger.info("   ➕ Instancia: {} ({} - {})",
                                instancia.getNumeroRepeticion(),
                                instancia.getFechaInicio(),
                                instancia.getFechaFin());

                            Map<String, Object> mapInstancia = new HashMap<>();
                            mapInstancia.put("id", evento.getEventoId() + "-" + instancia.getInstanciaId());
                            mapInstancia.put("title", evento.getTitulo() + " (Repetición " + instancia.getNumeroRepeticion() + ")");
                            mapInstancia.put("start", instancia.getFechaInicio());
                            mapInstancia.put("end", instancia.getFechaFin());
                            mapInstancia.put("backgroundColor", evento.getColor() != null ? evento.getColor() : "#28a745");
                            mapInstancia.put("borderColor", evento.getColor() != null ? evento.getColor() : "#28a745");
                            mapInstancia.put("extendedProps", Map.of(
                                "descripcion", evento.getDescripcion() != null ? evento.getDescripcion() : "",
                                "material", evento.getVentaInventario() != null && evento.getVentaInventario().getInventario() != null
                                    ? evento.getVentaInventario().getInventario().getMaterial().getNombre() : "",
                                "centro", evento.getCentroAcopio() != null ? evento.getCentroAcopio().getNombreCntAcp() : "",
                                "esRepeticion", true
                            ));
                            eventosFormato.add(mapInstancia);
                        }
                    } else {
                        logger.warn("⚠️ No hay instancias para el evento repetido: {}", evento.getTitulo());
                    }
                }
            }

            logger.info("Total eventos retornados (incluyendo repeticiones): {}", eventosFormato.size());
            return ResponseEntity.ok(eventosFormato);
        } catch (Exception e) {
            logger.error("Error obteniendo eventos", e);
            return ResponseEntity.status(HttpStatus.INTERNAL_SERVER_ERROR)
                .body(Map.of("error", "Error al obtener eventos: " + e.getMessage()));
        }
    }

    /**
     * Borrar un evento por ID
     */
    @DeleteMapping("/{eventoId}")
    public ResponseEntity<?> borrarEvento(@PathVariable String eventoId) {
        try {
            logger.info("🗑️ Borrando evento: {}", eventoId);

            UUID eventoUUID = UUID.fromString(eventoId);

            // Verificar que existe
            Evento evento = eventoRepository.findById(eventoUUID)
                .orElseThrow(() -> new IllegalArgumentException("Evento no encontrado"));

            logger.info("✅ Evento encontrado: {}", evento.getTitulo());

            // Borrar la venta asociada también
            if (evento.getVentaInventario() != null) {
                ventasInventarioRepository.delete(evento.getVentaInventario());
                logger.info("✅ VentaInventario borrada");
            }

            // Borrar el evento
            eventoRepository.delete(evento);
            logger.info("✅ Evento borrado correctamente");

            return ResponseEntity.ok(Map.of(
                "success", true,
                "message", "Evento borrado correctamente"
            ));

        } catch (IllegalArgumentException e) {
            logger.warn("⚠️ Error de validación: {}", e.getMessage());
            return ResponseEntity.badRequest().body(Map.of("error", e.getMessage()));
        } catch (Exception e) {
            logger.error("❌ Error borrando evento: {}", e.getMessage());
            return ResponseEntity.status(HttpStatus.INTERNAL_SERVER_ERROR)
                .body(Map.of("error", "Error al borrar evento: " + e.getMessage()));
        }
    }
}
