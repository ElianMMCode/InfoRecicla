package org.sena.inforecicla.service;

import org.sena.inforecicla.model.CentroAcopio;

import java.util.List;
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
    List<CentroAcopio> obtenerPorPuntoECA(UUID puntoEcaId);

    /**
     * Obtiene un centro de acopio por su ID
     *
     * @param centroAcopioId ID del Centro de Acopio
     * @return Centro de acopio si existe
     */
    CentroAcopio obtenerPorId(UUID centroAcopioId);
}

