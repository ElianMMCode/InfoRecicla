package org.sena.inforecicla.service;

import lombok.RequiredArgsConstructor;
import org.sena.inforecicla.model.PuntoECA;
import org.sena.inforecicla.repository.PuntoEcaRepository;
import org.springframework.stereotype.Service;

import java.util.Optional;
import java.util.UUID;

@Service
@RequiredArgsConstructor
public class PuntoEcaService {

    private final PuntoEcaRepository puntoEcaRepository;

    public Optional<PuntoECA> buscarPuntoEca(UUID puntoId) {
        return puntoEcaRepository.findById(puntoId);
    }

    public Optional<PuntoECA> mostrarPuntoEca(UUID puntoId) {
        return buscarPuntoEca(puntoId);
    }


}
