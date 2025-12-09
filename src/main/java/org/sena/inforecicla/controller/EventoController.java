package org.sena.inforecicla.controller;

import lombok.AllArgsConstructor;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;
import org.springframework.http.HttpStatus;
import org.springframework.http.ResponseEntity;
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
    private final EventoInstanciaRepository eventoInstanciaRepository;
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
            // Usar hash de los datos más importantes para que sea más robusto
            int hash = (usuarioId + "-" + materialId + "-" + titulo + "-" + fechaInicio).hashCode();
            String clave = "evento_" + Math.abs(hash);
            LocalDateTime ahora = LocalDateTime.now();

            // Verificar si hace poco se guardó un evento similar
            if (ultimaVenta.containsKey(clave)) {
                LocalDateTime ultimaGuardada = ultimaVenta.get(clave);
                if (ahora.isBefore(ultimaGuardada.plusSeconds(3))) { // Aumentado a 3 segundos
                    logger.warn("⚠️ Evento duplicado detectado (mismo evento en menos de 3s), ignorando");
                    logger.warn("   Clave: {}", clave);
                    logger.warn("   Última guardada: {}, Ahora: {}", ultimaGuardada, ahora);
                    return ResponseEntity.ok(Map.of(
                        "success", false,
                        "message", "Evento duplicado, por favor espere",
                        "duplicado", true
                    ));
                }
            }

            // Guardar tiempo de última venta
            ultimaVenta.put(clave, ahora);
            logger.info("✅ Clave de debounce guardada: {}", clave);

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

            // Obtener el inventario del material para el punto
            Inventario inventario = null;
            if (material.getInventario() != null && !material.getInventario().isEmpty()) {
                // Buscar el inventario que pertenece a este punto
                inventario = material.getInventario().stream()
                    .filter(inv -> inv.getPuntoEca().getPuntoEcaID().equals(punto.getPuntoEcaID()))
                    .findFirst()
                    .orElse(material.getInventario().get(0)); // Si no encuentra del punto, toma el primero
                logger.info("   Inventario: {}", inventario != null ? inventario.getInventarioId() : "Ninguno");
            }

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
                    .inventario(inventario)  // ✅ Asignar el inventario
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

                // Obtener material y centro
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

                // Verificar si tiene instancias
                boolean tieneInstancias = evento.getInstancias() != null && !evento.getInstancias().isEmpty();

                // Si NO tiene repetición, mostrar el evento base directamente
                // PERO SOLO si NO tiene instancias (para evitar duplicación)
                if (!evento.getTipoRepeticion().tieneRepeticion() && !tieneInstancias) {
                    logger.info("✅ Evento sin repetición y sin instancias, mostrando evento base");

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
                    // Si tiene repetición Y tiene instancias, mostrar SOLO las instancias
                    logger.info("📅 Evento repetido detectado con {} instancias", evento.getInstancias().size());

                    for (EventoInstancia instancia : evento.getInstancias()) {
                        logger.info("   ➕ Instancia: {} ({} - {})",
                            instancia.getNumeroRepeticion(),
                            instancia.getFechaInicio(),
                            instancia.getFechaFin());

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
                } else {
                    // Si tiene repetición pero NO tiene instancias, mostrar el evento base
                    logger.warn("⚠️ Evento con repetición pero sin instancias: {}", evento.getTitulo());

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
     * Obtener un evento por ID
     */
    @GetMapping("/{eventoId}")
    public ResponseEntity<?> obtenerEvento(@PathVariable String eventoId) {
        try {
            logger.info("📖 Obteniendo evento: {}", eventoId);

            UUID eventoUUID = UUID.fromString(eventoId);

            Evento evento = eventoRepository.findById(eventoUUID)
                .orElseThrow(() -> new IllegalArgumentException("Evento no encontrado"));

            // Obtener el material
            Material material = null;
            logger.info("🔍 Buscando material...");
            logger.info("   VentaInventario: {}", evento.getVentaInventario() != null ? evento.getVentaInventario().getVentaId() : "NULL");
            if (evento.getVentaInventario() != null) {
                logger.info("   Inventario: {}", evento.getVentaInventario().getInventario() != null ? evento.getVentaInventario().getInventario().getInventarioId() : "NULL");
                if (evento.getVentaInventario().getInventario() != null) {
                    material = evento.getVentaInventario().getInventario().getMaterial();
                    logger.info("   Material encontrado: {}", material != null ? material.getNombre() : "NULL");
                }
            }

            // Convertir a mapa para enviar al cliente
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

            logger.info("✅ Evento obtenido: {} (Material: {})", evento.getTitulo(), material != null ? material.getNombre() : "ninguno");
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

            // Actualizar campos
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
            if (color != null && !color.isEmpty()) {
                evento.setColor(color);
            }

            // Parsear fechas si están presentes
            if (fechaInicio != null && fechaFin != null) {
                DateTimeFormatter formatter = DateTimeFormatter.ISO_DATE_TIME;
                LocalDateTime fechaInicioLDT = LocalDateTime.parse(fechaInicio, formatter);
                LocalDateTime fechaFinLDT = LocalDateTime.parse(fechaFin, formatter);
                evento.setFechaInicio(fechaInicioLDT);
                evento.setFechaFin(fechaFinLDT);
            }

            // Actualizar tipo de repetición y fecha fin si están presentes
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

            // Guardar cambios del evento
            evento = eventoRepository.save(evento);
            logger.info("✅ Evento actualizado: {}", evento.getTitulo());

            // IMPORTANTE: Borrar todas las instancias antiguas usando el repositorio
            if (evento.getInstancias() != null && !evento.getInstancias().isEmpty()) {
                logger.info("🗑️ Borrando {} instancias antiguas", evento.getInstancias().size());

                // Crear una copia de la lista para iterar
                List<EventoInstancia> instanciasABorrar = new ArrayList<>(evento.getInstancias());

                // Borrar cada instancia del repositorio
                for (EventoInstancia instancia : instanciasABorrar) {
                    eventoInstanciaRepository.delete(instancia);
                    logger.debug("   ✅ Instancia borrada: {}", instancia.getInstanciaId());
                }

                // Limpiar la lista del evento
                evento.getInstancias().clear();
                eventoRepository.save(evento);
                logger.info("✅ Todas las instancias borradas");
            }

            // Regenerar instancias con los nuevos parámetros
            logger.info("🔄 Regenerando instancias...");
            eventoService.generarInstancias(evento);

            // Recargar el evento actualizado con las nuevas instancias
            evento = eventoRepository.findById(evento.getEventoId()).orElse(evento);
            logger.info("✅ Instancias regeneradas: {}", evento.getInstancias() != null ? evento.getInstancias().size() : 0);

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
            logger.info("🗑️ Borrando instancia del evento");
            logger.info("   eventoId recibido: '{}'", eventoId);

            // Validar eventoId
            if (eventoId == null || eventoId.trim().isEmpty()) {
                logger.warn("❌ eventoId está vacío");
                return ResponseEntity.badRequest().body(Map.of("error", "ID de evento requerido"));
            }

            UUID eventoUUID;
            try {
                eventoUUID = UUID.fromString(eventoId);
                logger.info("✅ eventoId parseado como UUID");
            } catch (IllegalArgumentException e) {
                logger.warn("❌ eventoId inválido: {}", e.getMessage());
                return ResponseEntity.badRequest().body(Map.of("error", "ID de evento inválido"));
            }

            Evento evento = eventoRepository.findById(eventoUUID)
                .orElseThrow(() -> new IllegalArgumentException("Evento no encontrado"));

            logger.info("✅ Evento encontrado: {}", evento.getTitulo());

            // Obtener el ID de la instancia o la fecha de la instancia a borrar
            String instanciaId = (String) request.get("instanciaId");
            String fechaInstancia = (String) request.get("fechaInstancia");

            logger.info("   instanciaId recibido: {}", instanciaId);
            logger.info("   fechaInstancia recibida: {}", fechaInstancia);
            logger.info("   Total instancias antes: {}", evento.getInstancias() != null ? evento.getInstancias().size() : 0);

            if (evento.getInstancias() == null || evento.getInstancias().isEmpty()) {
                logger.warn("⚠️ El evento no tiene instancias");
                return ResponseEntity.badRequest().body(Map.of("error", "El evento no tiene instancias"));
            }

            EventoInstancia instanciaABorrar = null;

            // Primero intentar borrar por instanciaId si se proporciona
            if (instanciaId != null && !instanciaId.isEmpty()) {
                logger.info("   Intentando borrar por instanciaId: {}", instanciaId);
                try {
                    UUID instanciaUUID = UUID.fromString(instanciaId);
                    instanciaABorrar = evento.getInstancias().stream()
                        .filter(inst -> inst.getInstanciaId().equals(instanciaUUID))
                        .findFirst()
                        .orElse(null);
                    if (instanciaABorrar != null) {
                        logger.info("   ✅ Instancia encontrada por ID");
                    }
                } catch (IllegalArgumentException e) {
                    logger.warn("   ⚠️ instanciaId no es UUID válido, intentando por fecha");
                }
            }

            // Si no se encontró por ID, intentar por fecha
            if (instanciaABorrar == null && fechaInstancia != null && !fechaInstancia.isEmpty()) {
                logger.info("   Buscando por fecha: {}", fechaInstancia);
                String busquedaFechaStr = fechaInstancia.substring(0, Math.min(10, fechaInstancia.length()));
                logger.info("   Comparando fechas: buscando '{}'", busquedaFechaStr);

                instanciaABorrar = evento.getInstancias().stream()
                    .filter(inst -> {
                        String instFechaStr = inst.getFechaInicio().toString();
                        boolean matches = instFechaStr.startsWith(busquedaFechaStr);
                        logger.debug("   {} vs {} = {}", instFechaStr, busquedaFechaStr, matches);
                        return matches;
                    })
                    .findFirst()
                    .orElse(null);

                if (instanciaABorrar != null) {
                    logger.info("   ✅ Instancia encontrada por fecha");
                }
            }

            if (instanciaABorrar != null) {
                logger.info("   ✅ Instancia encontrada: {}", instanciaABorrar.getInstanciaId());

                // Paso 1: Remover de la lista (SIN guardar el evento)
                evento.getInstancias().remove(instanciaABorrar);
                logger.info("   ✅ Instancia removida de la lista del evento");

                // Paso 2: Borrar del repositorio directamente
                eventoInstanciaRepository.delete(instanciaABorrar);
                logger.info("   ✅ Instancia borrada del repositorio");

                logger.info("✅ INSTANCIA BORRADA CORRECTAMENTE - Evento mantiene {} instancias",
                    evento.getInstancias().size());

                return ResponseEntity.ok(Map.of(
                    "success", true,
                    "message", "Instancia borrada correctamente",
                    "instanciasRestantes", evento.getInstancias().size()
                ));
            } else {
                logger.warn("⚠️ No se encontró instancia");
                logger.warn("   Instancias disponibles: {}", evento.getInstancias().size());
                evento.getInstancias().forEach(inst ->
                    logger.warn("      - {} ({} a {})", inst.getInstanciaId(), inst.getFechaInicio(), inst.getFechaFin())
                );
                return ResponseEntity.badRequest().body(Map.of("error", "No se encontró la instancia"));
            }

        } catch (IllegalArgumentException e) {
            logger.warn("⚠️ Error de validación: {}", e.getMessage());
            return ResponseEntity.badRequest().body(Map.of("error", e.getMessage()));
        } catch (Exception e) {
            logger.error("❌ Error borrando instancia: {}", e.getMessage(), e);
            e.printStackTrace();
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
            logger.info("🗑️ Iniciando borrado de evento");
            logger.info("   eventoId recibido: '{}'", eventoId);

            // Validar que no esté vacío
            if (eventoId.trim().isEmpty()) {
                logger.warn("❌ eventoId está vacío");
                return ResponseEntity.badRequest().body(Map.of("error", "ID de evento requerido"));
            }

            // Intentar parsear como UUID
            UUID eventoUUID;
            try {
                eventoUUID = UUID.fromString(eventoId);
                logger.info("✅ eventoId parseado como UUID: {}", eventoUUID);
            } catch (IllegalArgumentException e) {
                logger.warn("❌ eventoId no es UUID válido: '{}'", eventoId);
                return ResponseEntity.badRequest().body(Map.of(
                    "error", "ID de evento inválido: " + e.getMessage()
                ));
            }

            // Verificar que existe
            Evento evento = eventoRepository.findById(eventoUUID)
                .orElseThrow(() -> {
                    logger.warn("❌ Evento no encontrado con ID: {}", eventoUUID);
                    return new IllegalArgumentException("Evento no encontrado");
                });

            logger.info("✅ Evento encontrado: {} ({})", evento.getTitulo(), evento.getEventoId());

            // Paso 1: Borrar todas las instancias primero
            if (evento.getInstancias() != null && !evento.getInstancias().isEmpty()) {
                logger.info("🗑️ Paso 1: Borrando {} instancias", evento.getInstancias().size());

                // Crear copia de la lista para evitar ConcurrentModificationException
                List<EventoInstancia> instanciasABorrar = new ArrayList<>(evento.getInstancias());

                // Borrar cada instancia del repositorio explícitamente
                for (EventoInstancia instancia : instanciasABorrar) {
                    logger.debug("   - Borrando instancia: {}", instancia.getInstanciaId());
                    eventoInstanciaRepository.delete(instancia);
                }

                // Limpiar la lista SIN guardar el evento
                evento.getInstancias().clear();
                logger.info("✅ Instancias borradas del repositorio");
            }

            // Paso 2: Desasociar la venta del evento ANTES de borrar
            VentaInventario ventaABorrar = evento.getVentaInventario();
            if (ventaABorrar != null) {
                logger.info("🗑️ Paso 2: Desasociando VentaInventario: {}", ventaABorrar.getVentaId());
                evento.setVentaInventario(null);
                logger.info("✅ Referencia a VentaInventario limpiada");
            }

            // Paso 3: Ahora borrar el evento directamente SIN guardar (para no mergear)
            logger.info("🗑️ Paso 3: Borrando evento base");
            eventoRepository.delete(evento);
            logger.info("✅ Evento borrado de BD");

            // Paso 4: Finalmente, borrar la venta (sin restricción de FK)
            if (ventaABorrar != null) {
                logger.info("🗑️ Paso 4: Borrando VentaInventario: {}", ventaABorrar.getVentaId());
                try {
                    ventasInventarioRepository.delete(ventaABorrar);
                    logger.info("✅ VentaInventario borrada correctamente");
                } catch (Exception e) {
                    logger.warn("⚠️ Advertencia al borrar VentaInventario: {}", e.getMessage());
                    logger.warn("   (El evento ya fue borrado exitosamente)");
                }
            }

            logger.info("✅ BORRADO COMPLETO Y EXITOSO");

            return ResponseEntity.ok(Map.of(
                "success", true,
                "message", "Evento borrado correctamente",
                "eventoId", eventoUUID.toString()
            ));

        } catch (IllegalArgumentException e) {
            logger.warn("⚠️ Error de validación: {}", e.getMessage());
            return ResponseEntity.badRequest().body(Map.of("error", e.getMessage()));
        } catch (Exception e) {
            logger.error("❌ Error borrando evento: {}", e.getMessage(), e);
            e.printStackTrace();
            return ResponseEntity.status(HttpStatus.INTERNAL_SERVER_ERROR)
                .body(Map.of("error", "Error al borrar evento: " + e.getMessage()));
        }
    }
}


