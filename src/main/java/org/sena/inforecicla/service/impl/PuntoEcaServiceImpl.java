package org.sena.inforecicla.service.impl;

import lombok.RequiredArgsConstructor;
import org.sena.inforecicla.model.PuntoECA;
import org.sena.inforecicla.repository.PuntoEcaRepository;
import org.sena.inforecicla.service.PuntoEcaService;
import org.springframework.stereotype.Service;

import java.util.Optional;
import java.util.UUID;

@Service
@RequiredArgsConstructor
public class PuntoEcaServiceImpl implements PuntoEcaService {

    private final PuntoEcaRepository puntoEcaRepository;

    @Override
    public Optional<PuntoECA> buscarPuntoEca(UUID puntoId) {
        return puntoEcaRepository.findById(puntoId);
    }

    @Override
    public Optional<PuntoECA> mostrarPuntoEca(UUID puntoId) {
        return buscarPuntoEca(puntoId);
    }
}

