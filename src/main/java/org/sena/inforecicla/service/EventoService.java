package org.sena.inforecicla.service;

import org.sena.inforecicla.dto.EventoCalendarioDTO;
import org.sena.inforecicla.dto.EventoVentaCreateDTO;
import org.sena.inforecicla.model.*;
import org.sena.inforecicla.model.enums.TipoRepeticion;

import java.time.LocalDateTime;
import java.util.List;
import java.util.UUID;

/**
 * Interfaz del servicio para gestionar eventos del calendario.
 *
 * Los eventos son creados automáticamente a partir de ventas de material
 * y pueden tener repetición semanal, quincenal o mensual.
 *
 * Todos los eventos están asociados a un usuario y un Punto ECA para
 * garantizar seguridad en el acceso.
 */
public interface EventoService {

    // ===== OPERACIONES DE EVENTO BASE =====

    /**
     * Crea un evento base desde una venta de material.
     * Automáticamente genera instancias según el tipo de repetición.
     *
     * @param dto DTO con información del evento
     * @return Evento creado
     */
    Evento crearEventoDesdeVenta(EventoVentaCreateDTO dto);

    /**
     * Obtiene un evento por su ID (con validación de seguridad).
     *
     * @param eventoId ID del evento
     * @param usuarioId ID del usuario que solicita
     * @return Evento encontrado
     * @throws IllegalAccessException Si el usuario no tiene acceso
     */
    Evento obtenerEvento(UUID eventoId, UUID usuarioId) throws IllegalAccessException;

    /**
     * Actualiza un evento existente.
     *
     * @param eventoId ID del evento
     * @param eventoActualizado Datos actualizados
     * @param usuarioId ID del usuario
     * @return Evento actualizado
     */
    Evento actualizarEvento(UUID eventoId, Evento eventoActualizado, UUID usuarioId);

    /**
     * Elimina un evento y todas sus instancias.
     *
     * @param eventoId ID del evento
     * @param usuarioId ID del usuario
     */
    void eliminarEvento(UUID eventoId, UUID usuarioId);

    /**
     * Obtiene todos los eventos de un usuario en un punto ECA.
     *
     * @param usuarioId ID del usuario
     * @param puntoEcaId ID del Punto ECA
     * @return Lista de eventos
     */
    List<Evento> obtenerEventosDelUsuario(UUID usuarioId, UUID puntoEcaId);

    /**
     * Obtiene eventos con repetición activa.
     *
     * @param usuarioId ID del usuario
     * @param tipoRepeticion Tipo de repetición a buscar
     * @return Lista de eventos con repetición
     */
    List<Evento> obtenerEventosConRepeticion(UUID usuarioId, TipoRepeticion tipoRepeticion);

    // ===== OPERACIONES DE INSTANCIAS =====

    /**
     * Obtiene las instancias de eventos para mostrar en el calendario.
     * Esta es la consulta principal para cargar el calendario.
     *
     * @param usuarioId ID del usuario
     * @param puntoEcaId ID del Punto ECA
     * @param inicio Fecha de inicio del rango
     * @param fin Fecha de fin del rango
     * @return DTOs de eventos listos para FullCalendar
     */
    List<EventoCalendarioDTO> obtenerInstanciasCalendario(
        UUID usuarioId,
        UUID puntoEcaId,
        LocalDateTime inicio,
        LocalDateTime fin
    );

    /**
     * Obtiene instancias pendientes para un usuario.
     *
     * @param usuarioId ID del usuario
     * @param puntoEcaId ID del Punto ECA
     * @return Lista de instancias pendientes
     */
    List<EventoInstancia> obtenerInstanciasPendientes(UUID usuarioId, UUID puntoEcaId);

    /**
     * Obtiene instancias completadas para un usuario.
     *
     * @param usuarioId ID del usuario
     * @param puntoEcaId ID del Punto ECA
     * @return Lista de instancias completadas
     */
    List<EventoInstancia> obtenerInstanciasCompletadas(UUID usuarioId, UUID puntoEcaId);

    /**
     * Obtiene instancias próximas sin completar (próximos N días).
     *
     * @param usuarioId ID del usuario
     * @param dias Número de días a anticipar
     * @return Lista de instancias próximas
     */
    List<EventoInstancia> obtenerInstanciasProximas(UUID usuarioId, int dias);

    /**
     * Marca una instancia como completada.
     *
     * @param instanciaId ID de la instancia
     * @param usuarioId ID del usuario
     * @param observaciones Observaciones opcionales
     */
    void marcarInstanciaCompletada(UUID instanciaId, UUID usuarioId, String observaciones);

    /**
     * Marca una instancia como no completada.
     *
     * @param instanciaId ID de la instancia
     * @param usuarioId ID del usuario
     */
    void desmarcarInstanciaCompletada(UUID instanciaId, UUID usuarioId);

    /**
     * Obtiene una instancia específica.
     *
     * @param instanciaId ID de la instancia
     * @param usuarioId ID del usuario
     * @return Instancia encontrada
     */
    EventoInstancia obtenerInstancia(UUID instanciaId, UUID usuarioId) throws IllegalAccessException;

    // ===== GENERACIÓN DE INSTANCIAS =====

    /**
     * Genera las instancias automáticamente para un evento con repetición.
     * Se ejecuta automáticamente al crear un evento con repetición.
     *
     * @param evento Evento base
     */
    void generarInstancias(Evento evento);

    /**
     * Genera la siguiente instancia para un evento con repetición.
     * Útil para mantener un "buffer" de instancias futuras.
     *
     * @param evento Evento base
     */
    void generarSiguienteInstancia(Evento evento);

    /**
     * Regenera todas las instancias de un evento.
     * Útil si se cambian los parámetros de repetición.
     *
     * @param eventoId ID del evento
     * @param usuarioId ID del usuario
     */
    void regenerarInstancias(UUID eventoId, UUID usuarioId);

    // ===== MANTENIMIENTO =====

    /**
     * Limpia instancias completadas hace más de N días.
     *
     * @param usuarioId ID del usuario (si es null, limpia para todos)
     * @param diasAntiguedad Número de días para considerar antigua
     * @return Número de instancias eliminadas
     */
    int limpiarInstanciasAntiguas(UUID usuarioId, int diasAntiguedad);

    /**
     * Verifica y limpia eventos cuya repetición ha vencido.
     *
     * @param usuarioId ID del usuario
     * @return Número de eventos limpiados
     */
    int limpiarEventosVencidos(UUID usuarioId);

    /**
     * Valida que un usuario tenga acceso a un evento.
     *
     * @param eventoId ID del evento
     * @param usuarioId ID del usuario
     * @return true si tiene acceso
     */
    boolean tieneAcceso(UUID eventoId, UUID usuarioId);

    /**
     * Valida que un usuario tenga acceso a una instancia.
     *
     * @param instanciaId ID de la instancia
     * @param usuarioId ID del usuario
     * @return true si tiene acceso
     */
    boolean tieneAccesoAInstancia(UUID instanciaId, UUID usuarioId);
}

