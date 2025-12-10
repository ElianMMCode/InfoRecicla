package org.sena.inforecicla.service;

import org.sena.inforecicla.dto.CentroAcopioCreateDTO;
import org.sena.inforecicla.model.CentroAcopio;

import java.util.List;
import java.util.UUID;

/**
 * Servicio para gestionar operaciones relacionadas con Centros de Acopio
 */
public interface CentroAcopioService {

    /**
     * Crea un nuevo centro de acopio asociado a un Punto ECA
     *
     * @param puntoEcaId ID del Punto ECA
     * @param dto DTO con los datos del centro a crear
     * @return Centro de acopio creado
     */
    CentroAcopio crear(UUID puntoEcaId, CentroAcopioCreateDTO dto);

    /**
     * Obtiene todos los centros de acopio asociados a un Punto ECA específico
     *
     * @param puntoEcaId ID del Punto ECA
     * @return Lista de centros de acopio activos (ECA y GLOBAL)
     */
    List<CentroAcopio> listaCentrosPorPuntoEca(UUID puntoEcaId);

    /**
     * Obtiene todos los centros de acopio globales (sin punto asignado)
     *
     * @return Lista de centros de acopio globales
     */
    List<CentroAcopio> obtenerCentrosGlobales();

    /**
     * Obtiene centros de acopio de un punto + centros globales
     *
     * @param puntoEcaId ID del Punto ECA
     * @return Lista combinada de centros del punto y globales
     */
    List<CentroAcopio> obtenerCentrosPuntoYGlobales(UUID puntoEcaId);

    CentroAcopio obtenerPorId(UUID centroAcopioId);

    /**
     * Obtiene un centro de acopio válido para un punto específico
     */
    CentroAcopio obtenerCentroValidoPunto(UUID centroId, UUID puntoId);

    /**
     * Actualiza un centro de acopio
     *
     * @param centroId ID del Centro de Acopio
     * @param centroActualizado Datos actualizados del centro
     * @return Centro actualizado
     */
    CentroAcopio actualizar(UUID centroId, CentroAcopio centroActualizado);

    /**
     * Elimina un centro de acopio
     *
     * @param centroId ID del Centro de Acopio
     */
    void eliminar(UUID centroId);
}

