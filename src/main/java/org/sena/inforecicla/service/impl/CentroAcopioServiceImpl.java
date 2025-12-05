package org.sena.inforecicla.service.impl;

import lombok.AllArgsConstructor;
import org.sena.inforecicla.model.CentroAcopio;
import org.sena.inforecicla.repository.CentroAcopioRepository;
import org.sena.inforecicla.service.CentroAcopioService;
import org.springframework.stereotype.Service;

import java.util.List;
import java.util.UUID;

/**
 * Implementación del servicio de Centro de Acopio
 */
@Service
@AllArgsConstructor
public class CentroAcopioServiceImpl implements CentroAcopioService {


    private final CentroAcopioRepository centroAcopioRepository;

    @Override
    public List<CentroAcopio> listaCentrosPorPuntoEca(UUID puntoEcaId) {
        return centroAcopioRepository.findAllByPuntoEca_PuntoEcaID(puntoEcaId).stream()
                .filter(c -> c.getPuntoEca().getPuntoEcaID().equals(puntoEcaId))
                .toList();
    }

    @Override
    public CentroAcopio obtenerPorId(UUID centroAcopioId) {
        return centroAcopioRepository.findById(centroAcopioId)
                .orElseThrow(() -> {
                    return new IllegalArgumentException("Centro de acopio no encontrado: " + centroAcopioId);
                });
    }

   @Override
    public CentroAcopio obtenerCentroValidoPunto(UUID centroId, UUID puntoId) {
        return centroAcopioRepository
                .findAllByPuntoEca_PuntoEcaIDAndCntAcpId(puntoId, centroId)
                .or(() -> centroAcopioRepository.findById(centroId))
                .orElse(null);
    }

}

