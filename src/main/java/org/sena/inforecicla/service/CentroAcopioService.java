package org.sena.inforecicla.service;

import org.sena.inforecicla.model.CentroAcopio;

import java.util.List;
import java.util.Optional;
import java.util.UUID;

/**
 * Servicio para gestionar operaciones relacionadas con Centros de Acopio
 */
public interface CentroAcopioService {

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

    /**
     * Obtiene un centro de acopio por su ID
     *
     * @param centroAcopioId ID del Centro de Acopio
     * @return Centro de acopio si existe
     */
    CentroAcopio obtenerPorId(UUID centroAcopioId);

    CentroAcopio obtenerCentroValidoPunto(UUID centroId, UUID puntoId);
}

