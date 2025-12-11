package org.sena.inforecicla.controller;

import lombok.AllArgsConstructor;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;
import org.springframework.http.HttpStatus;
import org.springframework.http.ResponseEntity;
import org.springframework.http.converter.HttpMessageNotReadableException;
import org.springframework.transaction.annotation.Transactional;
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
public class EventoController {

    private static final Logger logger = LoggerFactory.getLogger(EventoController.class);
    private static final Map<String, LocalDateTime> ultimaVenta = new java.util.concurrent.ConcurrentHashMap<>();

    private final MaterialRepository materialRepository;
    private final PuntoEcaRepository puntoRepository;
    private final UsuarioRepository usuarioRepository;
    private final CentroAcopioRepository centroAcopioRepository;
    private final EventoRepository eventoRepository;
    private final VentasInventarioRepository ventasInventarioRepository;
    private final EventoInstanciaRepository eventoInstanciaRepository;
    private final EventoService eventoService;

    /**
     * Crear un nuevo evento de venta
     */
    @PostMapping("/crear-venta")
    public ResponseEntity<?> crearEventoVenta(@RequestBody(required = false) Map<String, Object> request) {
        logger.info("═══════════════════════════════════════════════════════════");
        logger.info("🎯 INICIO: crearEventoVenta() - POST /api/eventos/crear-venta");
        logger.info("═══════════════════════════════════════════════════════════");

        try {
            if (request == null) {
                logger.error("❌ Request es null");
                return ResponseEntity.badRequest().body(Map.of("error", "Request body requerido"));
            }

            logger.info("✅ Request body recibido correctamente");
            logger.info("   Datos: {}", request);

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
            logger.info("   materialId: {}", materialId);
            logger.info("   puntoEcaId: {}", puntoEcaId);
            logger.info("   usuarioId: {}", usuarioId);

            // Crear clave para debounce (evitar duplicados)
            int hash = (usuarioId + "-" + materialId + "-" + titulo + "-" + fechaInicio).hashCode();
            String clave = "evento_" + Math.abs(hash);
            LocalDateTime ahora = LocalDateTime.now();

            if (ultimaVenta.containsKey(clave)) {
                LocalDateTime ultimaGuardada = ultimaVenta.get(clave);
                if (ahora.isBefore(ultimaGuardada.plusSeconds(3))) {
                    logger.warn("⚠️ Evento duplicado detectado");
                    return ResponseEntity.ok(Map.of(
                        "success", false,
                        "message", "Evento duplicado, por favor espere",
                        "duplicado", true
                    ));
                }
            }

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

            logger.info("✅ Entidades obtenidas correctamente");

            // Obtener el inventario del material para el punto
            Inventario inventario = null;
            if (material.getInventario() != null && !material.getInventario().isEmpty()) {
                inventario = material.getInventario().stream()
                    .filter(inv -> inv.getPuntoEca().getPuntoEcaID().equals(punto.getPuntoEcaID()))
                    .findFirst()
                    .orElse(material.getInventario().getFirst());
            }

            // Parsear fechas
            DateTimeFormatter formatter = DateTimeFormatter.ISO_DATE_TIME;
            LocalDateTime fechaInicioLDT = LocalDateTime.parse(fechaInicio, formatter);
            LocalDateTime fechaFinLDT = LocalDateTime.parse(fechaFin, formatter);

            // Parsear fechaFinRepeticion si está presente
            LocalDateTime fechaFinRepeticionLDT = null;
            if (fechaFinRepeticion != null && !fechaFinRepeticion.isEmpty()) {
                try {
                    if (fechaFinRepeticion.length() == 10) {
                        fechaFinRepeticionLDT = java.time.LocalDate.parse(fechaFinRepeticion)
                            .atTime(23, 59, 59);
                    } else {
                        fechaFinRepeticionLDT = LocalDateTime.parse(fechaFinRepeticion, formatter);
                    }
                } catch (Exception e) {
                    logger.warn("⚠️ Error parseando fechaFinRepeticion: {}", fechaFinRepeticion, e);
                }
            }

            // Crear VentaInventario
            logger.info("🛠️ Creando VentaInventario...");
            VentaInventario venta = VentaInventario.builder()
                    .inventario(inventario)
                    .fechaVenta(LocalDateTime.now())
                    .cantidad(BigDecimal.ZERO)
                    .precioVenta(BigDecimal.ZERO)
                    .ctrAcopio(centro)
                    .build();

            VentaInventario ventaGuardada = ventasInventarioRepository.save(venta);
            logger.info("✅ VentaInventario guardada: {}", ventaGuardada.getVentaId());

            // Crear DTO para usar el servicio que genera instancias
            logger.info("🛠️ Creando EventoVentaCreateDTO...");
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
                    .fechaFinRepeticion(fechaFinRepeticionLDT)
                    .color(color != null ? color : "#28a745")
                    .build();

            // Usar el servicio para crear el evento
            logger.info("🎬 Llamando a eventoService.crearEventoDesdeVenta()...");
            Evento eventoGuardado = eventoService.crearEventoDesdeVenta(dto);
            logger.info("✅ Evento guardado en BD con instancias: {}", eventoGuardado.getEventoId());

            return ResponseEntity.ok(Map.of(
                "success", true,
                "message", "Evento creado correctamente",
                "id", eventoGuardado.getEventoId(),
                "titulo", eventoGuardado.getTitulo(),
                "totalInstancias", eventoGuardado.getInstancias() != null ? eventoGuardado.getInstancias().size() : 0
            ));

        } catch (IllegalArgumentException e) {
            logger.warn("⚠️ Error de validación: {}", e.getMessage());
            return ResponseEntity.badRequest().body(Map.of("error", e.getMessage()));
        } catch (Exception e) {
            logger.error("❌ Error creando evento: {}", e.getMessage());
            logger.error("Stack trace:", e);
            return ResponseEntity.status(HttpStatus.INTERNAL_SERVER_ERROR)
                .body(Map.of(
                    "error", "Error al crear evento: " + e.getMessage(),
                    "tipo", e.getClass().getSimpleName()
                ));
        }
    }

    /**
     * Obtener eventos de un punto (formato FullCalendar)
     */
    @GetMapping("/punto/{puntoId}/eventos")
    public ResponseEntity<?> obtenerEventosPunto(@PathVariable String puntoId) {
        try {
            logger.info("Obteniendo eventos del punto: {}", puntoId);
            List<Evento> eventos = eventoService.obtenerEventosPorPunto(puntoId);
            List<Map<String, Object>> eventosFormato = new ArrayList<>();

            for (Evento evento : eventos) {
                logger.info("📍 Procesando evento: {}", evento.getTitulo());

                String nombreMaterial = "Sin material";
                String nombreCentro = "Sin asignar";

                if (evento.getVentaInventario() != null && evento.getVentaInventario().getInventario() != null) {
                    Material m = evento.getVentaInventario().getInventario().getMaterial();
                    if (m != null) {
                        nombreMaterial = m.getNombre();
                    }
                }

                if (evento.getCentroAcopio() != null) {
                    nombreCentro = evento.getCentroAcopio().getNombreCntAcp();
                }

                boolean tieneInstancias = evento.getInstancias() != null && !evento.getInstancias().isEmpty();

                if (!evento.getTipoRepeticion().tieneRepeticion() && !tieneInstancias) {
                    Map<String, Object> map = new HashMap<>();
                    map.put("id", evento.getEventoId());
                    map.put("title", evento.getTitulo());
                    map.put("start", evento.getFechaInicio());
                    map.put("end", evento.getFechaFin());
                    map.put("backgroundColor", evento.getColor() != null ? evento.getColor() : "#28a745");
                    map.put("borderColor", evento.getColor() != null ? evento.getColor() : "#28a745");
                    map.put("extendedProps", Map.of(
                        "descripcion", evento.getDescripcion() != null ? evento.getDescripcion() : "",
                        "material", nombreMaterial,
                        "centro", nombreCentro
                    ));
                    eventosFormato.add(map);
                } else if (tieneInstancias) {
                    for (EventoInstancia instancia : evento.getInstancias()) {
                        Map<String, Object> mapInstancia = new HashMap<>();
                        mapInstancia.put("id", evento.getEventoId());
                        mapInstancia.put("title", evento.getTitulo());
                        mapInstancia.put("start", instancia.getFechaInicio());
                        mapInstancia.put("end", instancia.getFechaFin());
                        mapInstancia.put("backgroundColor", evento.getColor() != null ? evento.getColor() : "#28a745");
                        mapInstancia.put("borderColor", evento.getColor() != null ? evento.getColor() : "#28a745");
                        mapInstancia.put("extendedProps", Map.of(
                            "descripcion", evento.getDescripcion() != null ? evento.getDescripcion() : "",
                            "material", nombreMaterial,
                            "centro", nombreCentro,
                            "esRepeticion", true
                        ));
                        eventosFormato.add(mapInstancia);
                    }
                }
            }

            return ResponseEntity.ok(eventosFormato);

        } catch (Exception e) {
            logger.error("Error obteniendo eventos", e);
            return ResponseEntity.status(HttpStatus.INTERNAL_SERVER_ERROR)
                .body(Map.of("error", "Error al obtener eventos: " + e.getMessage()));
        }
    }

    /**
     * Obtener un evento por ID
     */
    @GetMapping("/{eventoId}")
    public ResponseEntity<?> obtenerEvento(@PathVariable String eventoId) {
        try {
            logger.info("📖 Obteniendo evento: {}", eventoId);
            UUID eventoUUID = UUID.fromString(eventoId);

            Evento evento = eventoRepository.findById(eventoUUID)
                .orElseThrow(() -> new IllegalArgumentException("Evento no encontrado"));

            Material material = null;
            if (evento.getVentaInventario() != null && evento.getVentaInventario().getInventario() != null) {
                material = evento.getVentaInventario().getInventario().getMaterial();
            }

            Map<String, Object> eventoData = new HashMap<>();
            eventoData.put("id", evento.getEventoId());
            eventoData.put("titulo", evento.getTitulo());
            eventoData.put("descripcion", evento.getDescripcion());
            eventoData.put("fechaInicio", evento.getFechaInicio());
            eventoData.put("fechaFin", evento.getFechaFin());
            eventoData.put("color", evento.getColor());
            eventoData.put("tipoRepeticion", evento.getTipoRepeticion().getNombre());
            eventoData.put("materialId", material != null ? material.getMaterialId() : null);
            eventoData.put("materialNombre", material != null ? material.getNombre() : null);
            eventoData.put("centroAcopioId", evento.getCentroAcopio() != null ? evento.getCentroAcopio().getCntAcpId() : null);
            eventoData.put("centroAcopioNombre", evento.getCentroAcopio() != null ? evento.getCentroAcopio().getNombreCntAcp() : null);

            logger.info("✅ Evento obtenido: {}", evento.getTitulo());
            return ResponseEntity.ok(eventoData);

        } catch (IllegalArgumentException e) {
            logger.warn("⚠️ Error de validación: {}", e.getMessage());
            return ResponseEntity.badRequest().body(Map.of("error", e.getMessage()));
        } catch (Exception e) {
            logger.error("❌ Error obteniendo evento: {}", e.getMessage());
            return ResponseEntity.status(HttpStatus.INTERNAL_SERVER_ERROR)
                .body(Map.of("error", "Error al obtener evento: " + e.getMessage()));
        }
    }

    /**
     * Actualizar un evento
     */
    @PutMapping("/{eventoId}")
    public ResponseEntity<?> actualizarEvento(@PathVariable String eventoId, @RequestBody Map<String, Object> request) {
        try {
            logger.info("✏️ Actualizando evento: {}", eventoId);
            UUID eventoUUID = UUID.fromString(eventoId);

            Evento evento = eventoRepository.findById(eventoUUID)
                .orElseThrow(() -> new IllegalArgumentException("Evento no encontrado"));

            String titulo = (String) request.get("titulo");
            String descripcion = (String) request.get("descripcion");
            String color = (String) request.get("color");
            String fechaInicio = (String) request.get("fechaInicio");
            String fechaFin = (String) request.get("fechaFin");
            String tipoRepeticion = (String) request.get("tipoRepeticion");
            String fechaFinRepeticion = (String) request.get("fechaFinRepeticion");

            if (titulo != null && !titulo.isEmpty()) {
                evento.setTitulo(titulo);
            }
            if (descripcion != null) {
                evento.setDescripcion(descripcion);
            }
            if (color != null) {
                evento.setColor(color);
            }

            if (fechaInicio != null && fechaFin != null) {
                DateTimeFormatter formatter = DateTimeFormatter.ISO_DATE_TIME;
                LocalDateTime fechaInicioLDT = LocalDateTime.parse(fechaInicio, formatter);
                LocalDateTime fechaFinLDT = LocalDateTime.parse(fechaFin, formatter);
                evento.setFechaInicio(fechaInicioLDT);
                evento.setFechaFin(fechaFinLDT);
            }

            if (tipoRepeticion != null && !tipoRepeticion.isEmpty()) {
                evento.setTipoRepeticion(TipoRepeticion.valueOf(tipoRepeticion));
            }

            if (fechaFinRepeticion != null && !fechaFinRepeticion.isEmpty()) {
                try {
                    if (fechaFinRepeticion.length() == 10) {
                        LocalDateTime fechaFinRepeticionLDT = java.time.LocalDate.parse(fechaFinRepeticion)
                            .atTime(23, 59, 59);
                        evento.setFechaFinRepeticion(fechaFinRepeticionLDT);
                    } else {
                        DateTimeFormatter formatter = DateTimeFormatter.ISO_DATE_TIME;
                        evento.setFechaFinRepeticion(LocalDateTime.parse(fechaFinRepeticion, formatter));
                    }
                } catch (Exception e) {
                    logger.warn("⚠️ Error parseando fechaFinRepeticion: {}", fechaFinRepeticion, e);
                }
            }

            evento = eventoRepository.save(evento);
            logger.info("✅ Evento actualizado: {}", evento.getTitulo());

            if (evento.getInstancias() != null && !evento.getInstancias().isEmpty()) {
                logger.info("🗑️ Borrando instancias antiguas");
                List<EventoInstancia> instanciasABorrar = new ArrayList<>(evento.getInstancias());
                for (EventoInstancia instancia : instanciasABorrar) {
                    eventoInstanciaRepository.delete(instancia);
                }
                evento.getInstancias().clear();
                eventoRepository.save(evento);
            }

            logger.info("🔄 Regenerando instancias...");
            eventoService.generarInstancias(evento);
            evento = eventoRepository.findById(evento.getEventoId()).orElse(evento);

            return ResponseEntity.ok(Map.of(
                "success", true,
                "message", "Evento actualizado correctamente",
                "id", evento.getEventoId(),
                "titulo", evento.getTitulo()
            ));

        } catch (IllegalArgumentException e) {
            logger.warn("⚠️ Error de validación: {}", e.getMessage());
            return ResponseEntity.badRequest().body(Map.of("error", e.getMessage()));
        } catch (Exception e) {
            logger.error("❌ Error actualizando evento: {}", e.getMessage());
            return ResponseEntity.status(HttpStatus.INTERNAL_SERVER_ERROR)
                .body(Map.of("error", "Error al actualizar evento: " + e.getMessage()));
        }
    }

    /**
     * Borrar solo una instancia de un evento repetido
     */
    @DeleteMapping("/{eventoId}/instancia")
    @Transactional
    public ResponseEntity<?> borrarInstancia(@PathVariable String eventoId, @RequestBody Map<String, Object> request) {
        try {
            logger.info("🗑️ Borrando instancia del evento: {}", eventoId);

            if (eventoId == null || eventoId.trim().isEmpty()) {
                return ResponseEntity.badRequest().body(Map.of("error", "ID de evento requerido"));
            }

            UUID eventoUUID = UUID.fromString(eventoId);
            Evento evento = eventoRepository.findById(eventoUUID)
                .orElseThrow(() -> new IllegalArgumentException("Evento no encontrado"));

            String instanciaId = (String) request.get("instanciaId");
            String fechaInstancia = (String) request.get("fechaInstancia");

            if (evento.getInstancias() == null || evento.getInstancias().isEmpty()) {
                return ResponseEntity.badRequest().body(Map.of("error", "El evento no tiene instancias"));
            }

            EventoInstancia instanciaABorrar = null;

            if (instanciaId != null && !instanciaId.isEmpty()) {
                try {
                    UUID instanciaUUID = UUID.fromString(instanciaId);
                    instanciaABorrar = evento.getInstancias().stream()
                        .filter(inst -> inst.getInstanciaId().equals(instanciaUUID))
                        .findFirst()
                        .orElse(null);
                } catch (IllegalArgumentException e) {
                    logger.warn("⚠️ instanciaId no es UUID válido");
                }
            }

            if (instanciaABorrar == null && fechaInstancia != null && !fechaInstancia.isEmpty()) {
                String busquedaFechaStr = fechaInstancia.substring(0, Math.min(10, fechaInstancia.length()));
                instanciaABorrar = evento.getInstancias().stream()
                    .filter(inst -> inst.getFechaInicio().toString().startsWith(busquedaFechaStr))
                    .findFirst()
                    .orElse(null);
            }

            if (instanciaABorrar != null) {
                evento.getInstancias().remove(instanciaABorrar);
                eventoInstanciaRepository.delete(instanciaABorrar);
                logger.info("✅ Instancia borrada correctamente");

                return ResponseEntity.ok(Map.of(
                    "success", true,
                    "message", "Instancia borrada correctamente",
                    "instanciasRestantes", evento.getInstancias().size()
                ));
            } else {
                return ResponseEntity.badRequest().body(Map.of("error", "No se encontró la instancia"));
            }

        } catch (IllegalArgumentException e) {
            logger.warn("⚠️ Error de validación: {}", e.getMessage());
            return ResponseEntity.badRequest().body(Map.of("error", e.getMessage()));
        } catch (Exception e) {
            logger.error("❌ Error borrando instancia: {}", e.getMessage());
            return ResponseEntity.status(HttpStatus.INTERNAL_SERVER_ERROR)
                .body(Map.of("error", "Error al borrar instancia: " + e.getMessage()));
        }
    }

    /**
     * Borrar un evento por ID
     */
    @DeleteMapping("/{eventoId}")
    @Transactional
    public ResponseEntity<?> borrarEvento(@PathVariable String eventoId) {
        try {
            logger.info("🗑️ Borrando evento: {}", eventoId);

            if (eventoId == null || eventoId.trim().isEmpty()) {
                return ResponseEntity.badRequest().body(Map.of("error", "ID de evento requerido"));
            }

            UUID eventoUUID = UUID.fromString(eventoId);
            Evento evento = eventoRepository.findById(eventoUUID)
                .orElseThrow(() -> new IllegalArgumentException("Evento no encontrado"));

            logger.info("✅ Evento encontrado: {}", evento.getTitulo());

            if (evento.getInstancias() != null && !evento.getInstancias().isEmpty()) {
                logger.info("🗑️ Borrando instancias");
                List<EventoInstancia> instanciasABorrar = new ArrayList<>(evento.getInstancias());
                for (EventoInstancia instancia : instanciasABorrar) {
                    eventoInstanciaRepository.delete(instancia);
                }
                evento.getInstancias().clear();
            }

            VentaInventario ventaABorrar = evento.getVentaInventario();
            if (ventaABorrar != null) {
                evento.setVentaInventario(null);
            }

            eventoRepository.delete(evento);
            logger.info("✅ Evento borrado de BD");

            if (ventaABorrar != null) {
                try {
                    ventasInventarioRepository.delete(ventaABorrar);
                    logger.info("✅ VentaInventario borrada");
                } catch (Exception e) {
                    logger.warn("⚠️ Advertencia al borrar VentaInventario");
                }
            }

            return ResponseEntity.ok(Map.of(
                "success", true,
                "message", "Evento borrado correctamente",
                "eventoId", eventoUUID.toString()
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

    /**
     * Manejador para JSON inválido
     */
    @ExceptionHandler(HttpMessageNotReadableException.class)
    public ResponseEntity<?> manejarJsonInvalido(HttpMessageNotReadableException e) {
        logger.error("❌ JSON inválido en request body: {}", e.getMessage());
        return ResponseEntity.badRequest()
            .body(Map.of(
                "error", "JSON inválido",
                "tipo", "JSON_PARSE_ERROR",
                "detalles", e.getMostSpecificCause().getMessage()
            ));
    }

    /**
     * Manejador global de excepciones
     */
    @ExceptionHandler(Exception.class)
    public ResponseEntity<?> manejarExcepcionGlobal(Exception e) {
        logger.error("❌ Excepción no manejada: {}", e.getMessage());
        logger.error("Stack trace:", e);
        return ResponseEntity.status(HttpStatus.INTERNAL_SERVER_ERROR)
            .body(Map.of(
                "error", "Error interno del servidor",
                "tipo", e.getClass().getSimpleName(),
                "mensaje", e.getMessage()
            ));
    }
}

