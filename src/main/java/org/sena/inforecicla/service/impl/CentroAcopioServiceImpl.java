package org.sena.inforecicla.service.impl;

import lombok.AllArgsConstructor;
import org.sena.inforecicla.model.CentroAcopio;
import org.sena.inforecicla.repository.CentroAcopioRepository;
import org.sena.inforecicla.service.CentroAcopioService;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;
import org.springframework.stereotype.Service;

import java.util.List;
import java.util.UUID;

/**
 * Implementación del servicio de Centro de Acopio
 */
@Service
@AllArgsConstructor
public class CentroAcopioServiceImpl implements CentroAcopioService {

    private static final Logger logger = LoggerFactory.getLogger(CentroAcopioServiceImpl.class);

    private final CentroAcopioRepository centroAcopioRepository;

    @Override
    public List<CentroAcopio> obtenerPorPuntoECA(UUID puntoEcaId) {
        logger.debug("Obteniendo centros de acopio para Punto ECA: {}", puntoEcaId);
        return centroAcopioRepository.findAllByPuntoEca(puntoEcaId);
    }

    @Override
    public CentroAcopio obtenerPorId(UUID centroAcopioId) {
        logger.debug("Obteniendo centro de acopio por ID: {}", centroAcopioId);
        return centroAcopioRepository.findById(centroAcopioId)
                .orElseThrow(() -> {
                    logger.error("Centro de acopio no encontrado: {}", centroAcopioId);
                    return new IllegalArgumentException("Centro de acopio no encontrado: " + centroAcopioId);
                });
    }
}

