/**
 * ARCHIVO EJEMPLO: Repositorio para Eventos
 * Ubicación: src/main/java/org/sena/inforecicla/repository/EventoRepository.java
 *
 * Copia este código en la ubicación indicada
 */

package org.sena.inforecicla.repository;

import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.data.jpa.repository.Query;
import org.springframework.data.repository.query.Param;
import org.springframework.stereotype.Repository;
import org.sena.inforecicla.model.Evento;
import org.sena.inforecicla.model.enums.TipoRepeticion;

import java.time.LocalDateTime;
import java.util.List;
import java.util.Optional;
import java.util.UUID;

/**
 * Repositorio para la entidad Evento.
 *
 * Los eventos son configuraciones de repetición automática basadas en ventas de material.
 * Este repositorio proporciona métodos para obtener eventos filtrados por:
 * - Usuario y Punto ECA (para seguridad)
 * - Rango de fechas
 * - Tipo de repetición
 */
@Repository
public interface EventoRepository extends JpaRepository<Evento, UUID> {

    /**
     * Obtiene eventos de un usuario específico en un Punto ECA específico.
     *
     * @param usuarioId ID del usuario
     * @param puntoEcaId ID del Punto ECA
     * @return Lista de eventos del usuario en el punto ECA
     */
    @Query("SELECT e FROM Evento e WHERE e.usuario.usuarioId = :usuarioId AND e.puntoEca.id = :puntoEcaId")
    List<Evento> findByUsuarioAndPuntoEca(
        @Param("usuarioId") UUID usuarioId,
        @Param("puntoEcaId") UUID puntoEcaId
    );

    /**
     * Obtiene eventos de un usuario que tienen una venta específica.
     *
     * @param usuarioId ID del usuario
     * @param ventaId ID de la venta
     * @return Evento opcional encontrado
     */
    @Query("SELECT e FROM Evento e WHERE e.usuario.usuarioId = :usuarioId AND e.ventaInventario.ventaId = :ventaId")
    Optional<Evento> findByUsuarioAndVenta(
        @Param("usuarioId") UUID usuarioId,
        @Param("ventaId") UUID ventaId
    );

    /**
     * Obtiene eventos de un usuario en un rango de fechas (para el calendario).
     *
     * @param usuarioId ID del usuario
     * @param puntoEcaId ID del Punto ECA
     * @param inicio Fecha de inicio del rango
     * @param fin Fecha de fin del rango
     * @return Lista de eventos en el rango
     */
    @Query("""
        SELECT e FROM Evento e 
        WHERE e.usuario.usuarioId = :usuarioId 
        AND e.puntoEca.id = :puntoEcaId
        AND e.fechaInicio >= :inicio 
        AND e.fechaFin <= :fin
        ORDER BY e.fechaInicio ASC
        """)
    List<Evento> findEventosByUsuarioAndPuntoEcaAndDateRange(
        @Param("usuarioId") UUID usuarioId,
        @Param("puntoEcaId") UUID puntoEcaId,
        @Param("inicio") LocalDateTime inicio,
        @Param("fin") LocalDateTime fin
    );

    /**
     * Obtiene eventos con repetición de un usuario.
     *
     * @param usuarioId ID del usuario
     * @param tipoRepeticion Tipo de repetición buscado
     * @return Lista de eventos con repetición
     */
    @Query("""
        SELECT e FROM Evento e 
        WHERE e.usuario.usuarioId = :usuarioId 
        AND e.tipoRepeticion = :tipoRepeticion
        """)
    List<Evento> findEventosConRepeticion(
        @Param("usuarioId") UUID usuarioId,
        @Param("tipoRepeticion") TipoRepeticion tipoRepeticion
    );

    /**
     * Obtiene eventos cuya repetición haya vencido (fecha fin de repetición pasada).
     * Útil para limpiar o archivar eventos antiguos.
     *
     * @param usuario Usuario propietario
     * @param ahora Fecha actual
     * @return Lista de eventos con repetición vencida
     */
    @Query("""
        SELECT e FROM Evento e 
        WHERE e.usuario.usuarioId = :usuarioId 
        AND e.tipoRepeticion IS NOT NULL 
        AND e.fechaFinRepeticion IS NOT NULL
        AND e.fechaFinRepeticion < :ahora
        """)
    List<Evento> findEventosConRepeticionVencida(
        @Param("usuarioId") UUID usuarioId,
        @Param("ahora") LocalDateTime ahora
    );

    /**
     * Obtiene todos los eventos de un Punto ECA (para administración).
     *
     * @param puntoEcaId ID del Punto ECA
     * @return Lista de eventos del punto ECA
     */
    @Query("SELECT e FROM Evento e WHERE e.puntoEca.id = :puntoEcaId ORDER BY e.fechaInicio DESC")
    List<Evento> findByPuntoEca(@Param("puntoEcaId") UUID puntoEcaId);

    /**
     * Obtiene eventos asociados a un material específico.
     *
     * @param materialId ID del material
     * @return Lista de eventos del material
     */
    @Query("SELECT e FROM Evento e WHERE e.material.materialId = :materialId ORDER BY e.fechaInicio DESC")
    List<Evento> findByMaterial(@Param("materialId") UUID materialId);

    /**
     * Obtiene eventos asociados a un centro de acopio especfico.

    /**
     * Obtiene todos los eventos de un Punto ECA específico.
     *
     * @param puntoEcaId ID del Punto ECA
     * @return Lista de eventos del punto
     */
    @Query("SELECT e FROM Evento e WHERE e.puntoEca.puntoEcaID = :puntoEcaId ORDER BY e.fechaInicio DESC")
    List<Evento> findByPuntoEca_PuntoEcaID(@Param("puntoEcaId") UUID puntoEcaId);

    /**
     * Obtiene todos los eventos de un Punto ECA con eager loading de instancias.
     *
     * @param puntoEcaId ID del Punto ECA
     * @return Lista de eventos con instancias precargadas
     */
    @Query("""
        SELECT DISTINCT e FROM Evento e 
        LEFT JOIN FETCH e.instancias
        WHERE e.puntoEca.puntoEcaID = :puntoEcaId 
        ORDER BY e.fechaInicio DESC
        """)
    List<Evento> findByPuntoEcaWithInstancias(@Param("puntoEcaId") UUID puntoEcaId);
     /*
     * @param centroAcopioId ID del centro de acopio
     * @return Lista de eventos del centro de acopio
     */
    @Query("SELECT e FROM Evento e WHERE e.centroAcopio.cntAcpId = :centroAcopioId ORDER BY e.fechaInicio DESC")
    List<Evento> findByCentroAcopio(@Param("centroAcopioId") UUID centroAcopioId);
}

