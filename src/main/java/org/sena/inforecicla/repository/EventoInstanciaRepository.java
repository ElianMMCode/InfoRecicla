package org.sena.inforecicla.repository;

import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.data.jpa.repository.Query;
import org.springframework.data.repository.query.Param;
import org.springframework.stereotype.Repository;
import org.sena.inforecicla.model.EventoInstancia;

import java.time.LocalDateTime;
import java.util.List;
import java.util.Optional;
import java.util.UUID;

/**
 * Repositorio para la entidad EventoInstancia.
 *
 * Las instancias de eventos representan cada ocurrencia específica de un evento
 * en el calendario. Este repositorio proporciona métodos para:
 * - Obtener instancias filtradas por usuario y punto ECA (seguridad)
 * - Obtener instancias en un rango de fechas (para el calendario)
 * - Obtener instancias completadas/pendientes
 * - Obtener instancias próximas
 */
@Repository
public interface EventoInstanciaRepository extends JpaRepository<EventoInstancia, UUID> {

    /**
     * Obtiene instancias de eventos para un usuario en un punto ECA en un rango de fechas.
     * Esta es la consulta principal para cargar eventos en el calendario.
     *
     * @param usuarioId ID del usuario
     * @param puntoEcaId ID del Punto ECA
     * @param inicio Fecha de inicio del rango
     * @param fin Fecha de fin del rango
     * @return Lista de instancias en el rango especificado
     */
    @Query("""
        SELECT ei FROM EventoInstancia ei 
        WHERE ei.usuario.usuarioId = :usuarioId 
        AND ei.puntoEca.puntoEcaID = :puntoEcaId
        AND ei.fechaInicio >= :inicio 
        AND ei.fechaFin <= :fin
        ORDER BY ei.fechaInicio ASC
        """)
    List<EventoInstancia> findInstanciasByUsuarioAndPuntoEcaAndDateRange(
        @Param("usuarioId") UUID usuarioId,
        @Param("puntoEcaId") UUID puntoEcaId,
        @Param("inicio") LocalDateTime inicio,
        @Param("fin") LocalDateTime fin
    );

    /**
     * Obtiene todas las instancias de un evento base.
     *
     * @param eventoBaseId ID del evento base
     * @return Lista de instancias del evento
     */
    @Query("SELECT ei FROM EventoInstancia ei WHERE ei.eventoBase.eventoId = :eventoBaseId ORDER BY ei.fechaInicio ASC")
    List<EventoInstancia> findByEventoBase(@Param("eventoBaseId") UUID eventoBaseId);

    /**
     * Obtiene instancias pendientes (no completadas) para un usuario.
     *
     * @param usuarioId ID del usuario
     * @param puntoEcaId ID del Punto ECA
     * @return Lista de instancias pendientes
     */
    @Query("""
        SELECT ei FROM EventoInstancia ei 
        WHERE ei.usuario.usuarioId = :usuarioId 
        AND ei.puntoEca.puntoEcaID = :puntoEcaId
        AND ei.esCompletado = false
        ORDER BY ei.fechaInicio ASC
        """)
    List<EventoInstancia> findInstanciasPendientes(
        @Param("usuarioId") UUID usuarioId,
        @Param("puntoEcaId") UUID puntoEcaId
    );

    /**
     * Obtiene instancias completadas para un usuario.
     *
     * @param usuarioId ID del usuario
     * @param puntoEcaId ID del Punto ECA
     * @return Lista de instancias completadas
     */
    @Query("""
        SELECT ei FROM EventoInstancia ei 
        WHERE ei.usuario.usuarioId = :usuarioId 
        AND ei.puntoEca.puntoEcaID = :puntoEcaId
        AND ei.esCompletado = true
        ORDER BY ei.completadoEn DESC
        """)
    List<EventoInstancia> findInstanciasCompletadas(
        @Param("usuarioId") UUID usuarioId,
        @Param("puntoEcaId") UUID puntoEcaId
    );

    /**
     * Obtiene instancias próximas sin completar.
     *
     * @param usuarioId ID del usuario
     * @param ahora Fecha actual
     * @param limitDays Número de días adelante a buscar
     * @return Lista de instancias próximas
     */
    @Query("""
        SELECT ei FROM EventoInstancia ei 
        WHERE ei.usuario.usuarioId = :usuarioId 
        AND ei.esCompletado = false
        AND ei.fechaInicio >= :ahora 
        AND ei.fechaInicio <= :ahora + :limitDays day 
        ORDER BY ei.fechaInicio ASC
        """)
    List<EventoInstancia> findInstanciasProximas(
        @Param("usuarioId") UUID usuarioId,
        @Param("ahora") LocalDateTime ahora,
        @Param("limitDays") int limitDays
    );

    /**
     * Obtiene instancias de hoy para un usuario.
     *
     * @param usuarioId ID del usuario
     * @param inicio Inicio del día
     * @param fin Fin del día
     * @return Lista de instancias de hoy
     */
    @Query("""
        SELECT ei FROM EventoInstancia ei 
        WHERE ei.usuario.usuarioId = :usuarioId 
        AND ei.fechaInicio >= :inicio 
        AND ei.fechaInicio <= :fin
        ORDER BY ei.fechaInicio ASC
        """)
    List<EventoInstancia> findInstanciasDeHoy(
        @Param("usuarioId") UUID usuarioId,
        @Param("inicio") LocalDateTime inicio,
        @Param("fin") LocalDateTime fin
    );

    /**
     * Obtiene el número de repetición de la próxima instancia a generar.
     *
     * @param eventoBaseId ID del evento base
     * @return Número de la próxima repetición
     */
    @Query("""
        SELECT COALESCE(MAX(ei.numeroRepeticion), 0) + 1 
        FROM EventoInstancia ei 
        WHERE ei.eventoBase.eventoId = :eventoBaseId
        """)
    Integer getProximoNumeroRepeticion(@Param("eventoBaseId") UUID eventoBaseId);

    /**
     * Obtiene la última instancia creada de un evento.
     *
     * @param eventoBaseId ID del evento base
     * @return Última instancia creada
     */
    @Query("""
        SELECT ei FROM EventoInstancia ei 
        WHERE ei.eventoBase.eventoId = :eventoBaseId
        ORDER BY ei.createdAt DESC
        LIMIT 1
        """)
    Optional<EventoInstancia> findUltimaInstancia(@Param("eventoBaseId") UUID eventoBaseId);

    /**
     * Obtiene instancias de un evento base para un usuario específico.
     *
     * @param eventoBaseId ID del evento base
     * @param usuarioId ID del usuario
     * @return Lista de instancias del usuario para ese evento
     */
    @Query("""
        SELECT ei FROM EventoInstancia ei 
        WHERE ei.eventoBase.eventoId = :eventoBaseId 
        AND ei.usuario.usuarioId = :usuarioId
        ORDER BY ei.numeroRepeticion ASC
        """)
    List<EventoInstancia> findInstanciasDelUsuario(
        @Param("eventoBaseId") UUID eventoBaseId,
        @Param("usuarioId") UUID usuarioId
    );
}

