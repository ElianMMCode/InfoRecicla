package org.sena.inforecicla.service.impl;

import lombok.RequiredArgsConstructor;
import lombok.extern.slf4j.Slf4j;
import org.sena.inforecicla.service.EventoService;
import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;
import org.sena.inforecicla.dto.EventoCalendarioDTO;
import org.sena.inforecicla.dto.EventoVentaCreateDTO;
import org.sena.inforecicla.model.*;
import org.sena.inforecicla.model.enums.TipoRepeticion;
import org.sena.inforecicla.repository.*;

import java.time.LocalDateTime;
import java.time.format.DateTimeFormatter;
import java.util.*;
import java.util.stream.Collectors;

/**
 * Implementación del servicio de eventos del calendario.
 *
 * Responsable de:
 * - Crear eventos automáticamente desde ventas de material
 * - Generar instancias con repetición automática
 * - Manejar seguridad (usuario + punto ECA)
 * - Convertir instancias a DTOs para FullCalendar
 */
@Service
@RequiredArgsConstructor
@Transactional
@Slf4j
public class EventoServiceImpl implements EventoService {

    private static final DateTimeFormatter DATE_FORMATTER = DateTimeFormatter.ISO_DATE_TIME;
    private static final String COLOR_DEFAULT = "#28a745"; // Verde Inforecicla
    private static final int MAX_INSTANCIAS_FUTURAS = 365; // Máximo 1 año adelante

    // ===== INYECCIONES DE DEPENDENCIAS =====

    private final EventoRepository eventoRepository;
    private final EventoInstanciaRepository eventoInstanciaRepository;
    private final VentasInventarioRepository ventaInventarioRepository;
    private final MaterialRepository materialRepository;
    private final CentroAcopioRepository centroAcopioRepository;
    private final PuntoEcaRepository puntoECARepository;
    private final UsuarioRepository usuarioRepository;

    // ===== OPERACIONES DE EVENTO BASE =====

    @Override
    public Evento crearEventoDesdeVenta(EventoVentaCreateDTO dto) {
        log.info("Creando evento desde venta: {}", dto.getVentaInventarioId());

        // Obtener las entidades relacionadas
        VentaInventario venta = ventaInventarioRepository
            .findById(dto.getVentaInventarioId())
            .orElseThrow(() -> new IllegalArgumentException("Venta no encontrada: " + dto.getVentaInventarioId()));

        Material material = materialRepository
            .findById(dto.getMaterialId())
            .orElseThrow(() -> new IllegalArgumentException("Material no encontrado: " + dto.getMaterialId()));

        PuntoECA puntoEca = puntoECARepository
            .findById(dto.getPuntoEcaId())
            .orElseThrow(() -> new IllegalArgumentException("Punto ECA no encontrado: " + dto.getPuntoEcaId()));

        Usuario usuario = usuarioRepository
            .findById(dto.getUsuarioId())
            .orElseThrow(() -> new IllegalArgumentException("Usuario no encontrado: " + dto.getUsuarioId()));

        CentroAcopio centroAcopio = null;
        if (dto.getCentroAcopioId() != null) {
            centroAcopio = centroAcopioRepository
                .findById(dto.getCentroAcopioId())
                .orElse(null);
        }

        // Crear el evento base
        Evento evento = Evento.builder()
            .ventaInventario(venta)
            .material(material)
            .centroAcopio(centroAcopio)
            .puntoEca(puntoEca)
            .usuario(usuario)
            .titulo(dto.getTitulo())
            .descripcion(dto.getDescripcion())
            .fechaInicio(dto.getFechaInicio())
            .fechaFin(dto.getFechaFin())
            .color(dto.getColor() != null ? dto.getColor() : COLOR_DEFAULT)
            .tipoRepeticion(dto.getTipoRepeticion() != null ? dto.getTipoRepeticion() : TipoRepeticion.SIN_REPETICION)
            .fechaFinRepeticion(dto.getFechaFinRepeticion())
            .esEventoGenerado(false)
            .build();

        // Guardar el evento
        evento = eventoRepository.save(evento);
        log.info("Evento creado: {}", evento.getEventoId());

        // Generar instancias automáticamente
        generarInstancias(evento);

        return evento;
    }

    @Override
    public Evento obtenerEvento(UUID eventoId, UUID usuarioId) throws IllegalAccessException {
        Evento evento = eventoRepository.findById(eventoId)
            .orElseThrow(() -> new IllegalArgumentException("Evento no encontrado: " + eventoId));

        if (!evento.getUsuario().getUsuarioId().equals(usuarioId)) {
            throw new IllegalAccessException("No tienes permiso para acceder a este evento");
        }

        return evento;
    }

    @Override
    public Evento actualizarEvento(UUID eventoId, Evento eventoActualizado, UUID usuarioId) {
        Evento evento;
        try {
            evento = obtenerEvento(eventoId, usuarioId);
        } catch (IllegalAccessException e) {
            throw new RuntimeException("No tienes permiso para actualizar este evento", e);
        }

        evento.setTitulo(eventoActualizado.getTitulo());
        evento.setDescripcion(eventoActualizado.getDescripcion());
        evento.setFechaInicio(eventoActualizado.getFechaInicio());
        evento.setFechaFin(eventoActualizado.getFechaFin());
        evento.setColor(eventoActualizado.getColor());
        evento.setTipoRepeticion(eventoActualizado.getTipoRepeticion());
        evento.setFechaFinRepeticion(eventoActualizado.getFechaFinRepeticion());

        Evento eventoGuardado = eventoRepository.save(evento);

        // Si cambió la repetición, regenerar instancias
        if (!evento.getTipoRepeticion().equals(eventoActualizado.getTipoRepeticion())) {
            regenerarInstancias(eventoId, usuarioId);
        }

        return eventoGuardado;
    }

    @Override
    public void eliminarEvento(UUID eventoId, UUID usuarioId) {
        Evento evento;
        try {
            evento = obtenerEvento(eventoId, usuarioId);
        } catch (IllegalAccessException e) {
            throw new RuntimeException("No tienes permiso para eliminar este evento", e);
        }

        eventoRepository.delete(evento);
        log.info("Evento eliminado: {}", eventoId);
    }

    @Override
    public List<Evento> obtenerEventosDelUsuario(UUID usuarioId, UUID puntoEcaId) {
        return eventoRepository.findByUsuarioAndPuntoEca(usuarioId, puntoEcaId);
    }

    @Override
    public List<Evento> obtenerEventosConRepeticion(UUID usuarioId, TipoRepeticion tipoRepeticion) {
        return eventoRepository.findEventosConRepeticion(usuarioId, tipoRepeticion);
    }

    // ===== OPERACIONES DE INSTANCIAS =====

    @Override
    @Transactional(readOnly = true)
    public List<EventoCalendarioDTO> obtenerInstanciasCalendario(
        UUID usuarioId,
        UUID puntoEcaId,
        LocalDateTime inicio,
        LocalDateTime fin
    ) {
        log.debug("Obteniendo instancias para calendario: usuario={}, puntoEca={}, inicio={}, fin={}",
            usuarioId, puntoEcaId, inicio, fin);

        List<EventoInstancia> instancias = eventoInstanciaRepository
            .findInstanciasByUsuarioAndPuntoEcaAndDateRange(usuarioId, puntoEcaId, inicio, fin);

        return instancias.stream()
            .map(this::convertirADTO)
            .collect(Collectors.toList());
    }

    @Override
    @Transactional(readOnly = true)
    public List<EventoInstancia> obtenerInstanciasPendientes(UUID usuarioId, UUID puntoEcaId) {
        return eventoInstanciaRepository.findInstanciasPendientes(usuarioId, puntoEcaId);
    }

    @Override
    @Transactional(readOnly = true)
    public List<EventoInstancia> obtenerInstanciasCompletadas(UUID usuarioId, UUID puntoEcaId) {
        return eventoInstanciaRepository.findInstanciasCompletadas(usuarioId, puntoEcaId);
    }

    @Override
    @Transactional(readOnly = true)
    public List<EventoInstancia> obtenerInstanciasProximas(UUID usuarioId, int dias) {
        return eventoInstanciaRepository.findInstanciasProximas(usuarioId, LocalDateTime.now(), dias);
    }

    @Override
    public void marcarInstanciaCompletada(UUID instanciaId, UUID usuarioId, String observaciones) {
        EventoInstancia instancia;
        try {
            instancia = obtenerInstancia(instanciaId, usuarioId);
        } catch (IllegalAccessException e) {
            throw new RuntimeException("No tienes permiso para actualizar esta instancia", e);
        }

        instancia.marcarCompletada();
        if (observaciones != null) {
            instancia.setObservaciones(observaciones);
        }

        eventoInstanciaRepository.save(instancia);
        log.info("Instancia marcada como completada: {}", instanciaId);
    }

    @Override
    public void desmarcarInstanciaCompletada(UUID instanciaId, UUID usuarioId) {
        EventoInstancia instancia;
        try {
            instancia = obtenerInstancia(instanciaId, usuarioId);
        } catch (IllegalAccessException e) {
            throw new RuntimeException("No tienes permiso para actualizar esta instancia", e);
        }

        instancia.desmarcarCompletada();
        eventoInstanciaRepository.save(instancia);
        log.info("Instancia desmarcada: {}", instanciaId);
    }

    @Override
    @Transactional(readOnly = true)
    public EventoInstancia obtenerInstancia(UUID instanciaId, UUID usuarioId) throws IllegalAccessException {
        EventoInstancia instancia = eventoInstanciaRepository.findById(instanciaId)
            .orElseThrow(() -> new IllegalArgumentException("Instancia no encontrada: " + instanciaId));

        if (!instancia.getUsuario().getUsuarioId().equals(usuarioId)) {
            throw new IllegalAccessException("No tienes permiso para acceder a esta instancia");
        }

        return instancia;
    }

    // ===== GENERACIÓN DE INSTANCIAS =====

    @Override
    public void generarInstancias(Evento evento) {
        log.info("Generando instancias para evento: {}", evento.getEventoId());

        if (!evento.getTipoRepeticion().tieneRepeticion()) {
            // Crear una única instancia
            crearInstancia(evento, 1, evento.getFechaInicio(), evento.getFechaFin());
            return;
        }

        // Generar instancias hasta la fecha fin de repetición
        LocalDateTime fechaActual = evento.getFechaInicio();
        LocalDateTime fechaFin = evento.getFechaFinRepeticion() != null
            ? evento.getFechaFinRepeticion()
            : evento.getFechaInicio().plusDays(MAX_INSTANCIAS_FUTURAS);

        int numeroRepeticion = 1;

        while (fechaActual.isBefore(fechaFin)) {
            LocalDateTime fechaFinInstancia = fechaActual.plus(
                java.time.Duration.between(evento.getFechaInicio(), evento.getFechaFin())
            );

            crearInstancia(evento, numeroRepeticion, fechaActual, fechaFinInstancia);

            // Calcular siguiente fecha
            fechaActual = fechaActual.plusDays(evento.getTipoRepeticion().getIntervaloDias());
            numeroRepeticion++;
        }

        log.info("Se generaron {} instancias para el evento: {}", numeroRepeticion - 1, evento.getEventoId());
    }

    @Override
    public void generarSiguienteInstancia(Evento evento) {
        log.debug("Generando siguiente instancia para evento: {}", evento.getEventoId());

        Integer proximoNumero = eventoInstanciaRepository.getProximoNumeroRepeticion(evento.getEventoId());
        Optional<EventoInstancia> ultimaInstancia = eventoInstanciaRepository.findUltimaInstancia(evento.getEventoId());

        if (ultimaInstancia.isEmpty()) {
            log.warn("No hay instancias previas para el evento: {}", evento.getEventoId());
            return;
        }

        EventoInstancia ultima = ultimaInstancia.get();
        LocalDateTime nuevaFecha = ultima.getFechaInicio().plusDays(evento.getTipoRepeticion().getIntervaloDias());

        // Verificar que no exceda la fecha fin de repetición
        if (evento.getFechaFinRepeticion() != null && nuevaFecha.isAfter(evento.getFechaFinRepeticion())) {
            log.info("Se alcanzó la fecha fin de repetición para evento: {}", evento.getEventoId());
            return;
        }

        LocalDateTime nuevaFechaFin = nuevaFecha.plus(
            java.time.Duration.between(ultima.getFechaInicio(), ultima.getFechaFin())
        );

        crearInstancia(evento, proximoNumero, nuevaFecha, nuevaFechaFin);
    }

    @Override
    public void regenerarInstancias(UUID eventoId, UUID usuarioId) {
        Evento evento;
        try {
            evento = obtenerEvento(eventoId, usuarioId);
        } catch (IllegalAccessException e) {
            throw new RuntimeException("No tienes permiso para regenerar instancias", e);
        }

        // Eliminar instancias existentes
        eventoInstanciaRepository.deleteAll(eventoInstanciaRepository.findByEventoBase(eventoId));

        // Generar nuevas instancias
        generarInstancias(evento);
        log.info("Instancias regeneradas para evento: {}", eventoId);
    }

    // ===== MANTENIMIENTO =====

    @Override
    public int limpiarInstanciasAntiguas(UUID usuarioId, int diasAntiguedad) {
        LocalDateTime fechaLimite = LocalDateTime.now().minusDays(diasAntiguedad);

        List<EventoInstancia> instanciasAntiguas = eventoInstanciaRepository
            .findInstanciasCompletadas(usuarioId, usuarioId) // Placeholder - necesita query adicional
            .stream()
            .filter(ei -> ei.getCompletadoEn() != null && ei.getCompletadoEn().isBefore(fechaLimite))
            .collect(Collectors.toList());

        eventoInstanciaRepository.deleteAll(instanciasAntiguas);
        log.info("Se eliminaron {} instancias antiguas", instanciasAntiguas.size());

        return instanciasAntiguas.size();
    }

    @Override
    public int limpiarEventosVencidos(UUID usuarioId) {
        List<Evento> eventosVencidos = eventoRepository.findEventosConRepeticionVencida(usuarioId, LocalDateTime.now());

        for (Evento evento : eventosVencidos) {
            try {
                eliminarEvento(evento.getEventoId(), usuarioId);
            } catch (Exception e) {
                log.error("Error eliminando evento vencido: {}", evento.getEventoId(), e);
            }
        }

        log.info("Se eliminaron {} eventos vencidos", eventosVencidos.size());
        return eventosVencidos.size();
    }

    @Override
    @Transactional(readOnly = true)
    public boolean tieneAcceso(UUID eventoId, UUID usuarioId) {
        Evento evento = eventoRepository.findById(eventoId).orElse(null);
        return evento != null && evento.getUsuario().getUsuarioId().equals(usuarioId);
    }

    @Override
    @Transactional(readOnly = true)
    public boolean tieneAccesoAInstancia(UUID instanciaId, UUID usuarioId) {
        EventoInstancia instancia = eventoInstanciaRepository.findById(instanciaId).orElse(null);
        return instancia != null && instancia.getUsuario().getUsuarioId().equals(usuarioId);
    }

    // ===== MÉTODOS PRIVADOS =====

    /**
     * Crea una instancia individual del evento.
     */
    private void crearInstancia(Evento evento, int numeroRepeticion, LocalDateTime fechaInicio, LocalDateTime fechaFin) {
        EventoInstancia instancia = EventoInstancia.builder()
            .eventoBase(evento)
            .puntoEca(evento.getPuntoEca())
            .usuario(evento.getUsuario())
            .fechaInicio(fechaInicio)
            .fechaFin(fechaFin)
            .numeroRepeticion(numeroRepeticion)
            .esCompletado(false)
            .build();

        eventoInstanciaRepository.save(instancia);
    }

    /**
     * Convierte una instancia a DTO para FullCalendar.
     */
    private EventoCalendarioDTO convertirADTO(EventoInstancia instancia) {
        Evento evento = instancia.getEventoBase();
        Material material = evento.getMaterial();

        return EventoCalendarioDTO.builder()
            .instanciaId(instancia.getInstanciaId())
            .titulo(evento.getTitulo())
            .fechaInicio(instancia.getFechaInicio().format(DATE_FORMATTER))
            .fechaFin(instancia.getFechaFin().format(DATE_FORMATTER))
            .backgroundColor(evento.getColor() != null ? evento.getColor() : COLOR_DEFAULT)
            .borderColor(evento.getColor() != null ? evento.getColor() : COLOR_DEFAULT)
            .textColor("#ffffff")
            .descripcion(evento.getDescripcion())
            .materialId(material.getMaterialId())
            .materialNombre(material.getDescripcion())
            .centroAcopioId(evento.getCentroAcopio() != null ? evento.getCentroAcopio().getCntAcpId() : null)
            .centroAcopioNombre(evento.getCentroAcopio() != null ? evento.getCentroAcopio().getNombreCntAcp() : null)
            .ventaInventarioId(evento.getVentaInventario().getVentaId())
            .eventoBaseId(evento.getEventoId())
            .tipoRepeticion(evento.getTipoRepeticion().getNombre())
            .numeroRepeticion(instancia.getNumeroRepeticion())
            .esCompletado(instancia.getEsCompletado())
            .completadoEn(instancia.getCompletadoEn() != null ? instancia.getCompletadoEn().format(DATE_FORMATTER) : null)
            .puntoEcaId(evento.getPuntoEca().getPuntoEcaID())
            .usuarioId(evento.getUsuario().getUsuarioId())
            .build();
    }
}

