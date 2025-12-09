/**
 * FRAGMENTO DE CÓDIGO: Endpoint REST para FullCalendar
 *
 * Ubicación: Agregar este método a tu clase PuntoEcaController.java
 *
 * INSTRUCCIONES:
 * 1. Abre: src/main/java/org/sena/inforecicla/controller/PuntoEcaController.java
 * 2. Agrega estas importaciones al inicio del archivo (si no existen):
 *
 *    import org.springframework.format.annotation.DateTimeFormat;
 *    import java.time.LocalDateTime;
 *    import java.util.List;
 *    import org.sena.inforecicla.dto.EventoCalendarioDTO;
 *    import org.sena.inforecicla.service.EventoService;
 *
 * 3. Agrega este campo en la clase (después de otros @Autowired):
 *
 *    @Autowired
 *    private EventoService eventoService;
 *
 * 4. Agrega este método en la clase (después de otros @GetMapping):
 */

@GetMapping("/api/punto-eca/{gestorId}/eventos")
public ResponseEntity<?> obtenerEventos(
    @PathVariable Long gestorId,
    @RequestParam(required = false) @DateTimeFormat(iso = DateTimeFormat.ISO.DATE_TIME) LocalDateTime start,
    @RequestParam(required = false) @DateTimeFormat(iso = DateTimeFormat.ISO.DATE_TIME) LocalDateTime end
) {
    try {
        // Obtener el usuario autenticado (opcional, para validación adicional)
        // Authentication auth = SecurityContextHolder.getContext().getAuthentication();

        // Aquí deberías validar que el gestorId corresponda al usuario autenticado
        // y que tenga permiso para ver los eventos de ese punto ECA

        List<EventoCalendarioDTO> eventos;

        if (start != null && end != null) {
            // Si se proporcionan fechas, filtrar por rango
            eventos = eventoService.obtenerEventosPorPuntoEcaYFechas(gestorId, start, end);
        } else {
            // Si no se proporcionan fechas, obtener todos los eventos del punto ECA
            eventos = eventoService.obtenerEventosPorPuntoEca(gestorId);
        }

        return ResponseEntity.ok(eventos);

    } catch (Exception e) {
        System.err.println("Error al obtener eventos: " + e.getMessage());
        e.printStackTrace();
        return ResponseEntity.status(500).body("{\"error\": \"Error al obtener eventos\"}");
    }
}

/**
 * ENDPOINT ADICIONAL (Opcional): Crear un nuevo evento
 */
@PostMapping("/api/punto-eca/{puntoEcaId}/eventos")
public ResponseEntity<?> crearEvento(
    @PathVariable Long puntoEcaId,
    @RequestBody Evento evento
) {
    try {
        // Validar que el evento tenga datos requeridos
        if (evento.getTitulo() == null || evento.getTitulo().trim().isEmpty()) {
            return ResponseEntity.badRequest().body("{\"error\": \"El título del evento es requerido\"}");
        }
        if (evento.getFechaInicio() == null || evento.getFechaFin() == null) {
            return ResponseEntity.badRequest().body("{\"error\": \"Las fechas de inicio y fin son requeridas\"}");
        }

        // Aquí puedes agregar más validaciones si es necesario

        Evento eventoCreado = eventoService.crearEvento(evento);
        return ResponseEntity.status(201).body(eventoCreado);

    } catch (Exception e) {
        System.err.println("Error al crear evento: " + e.getMessage());
        return ResponseEntity.status(500).body("{\"error\": \"Error al crear el evento\"}");
    }
}

/**
 * ENDPOINT ADICIONAL (Opcional): Actualizar un evento
 */
@PutMapping("/api/punto-eca/eventos/{eventoId}")
public ResponseEntity<?> actualizarEvento(
    @PathVariable Long eventoId,
    @RequestBody Evento eventoActualizado
) {
    try {
        Evento evento = eventoService.actualizarEvento(eventoId, eventoActualizado);
        return ResponseEntity.ok(evento);
    } catch (Exception e) {
        System.err.println("Error al actualizar evento: " + e.getMessage());
        return ResponseEntity.status(500).body("{\"error\": \"Error al actualizar el evento\"}");
    }
}

/**
 * ENDPOINT ADICIONAL (Opcional): Eliminar un evento
 */
@DeleteMapping("/api/punto-eca/eventos/{eventoId}")
public ResponseEntity<?> eliminarEvento(@PathVariable Long eventoId) {
    try {
        eventoService.eliminarEvento(eventoId);
        return ResponseEntity.ok("{\"mensaje\": \"Evento eliminado correctamente\"}");
    } catch (Exception e) {
        System.err.println("Error al eliminar evento: " + e.getMessage());
        return ResponseEntity.status(500).body("{\"error\": \"Error al eliminar el evento\"}");
    }
}

