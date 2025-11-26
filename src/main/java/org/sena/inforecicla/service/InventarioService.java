package org.sena.inforecicla.service;

import lombok.RequiredArgsConstructor;
import org.sena.inforecicla.repository.MaterialRepository;
import org.sena.inforecicla.repository.PuntoEcaRepository;
import org.springframework.stereotype.Service;

@Service
@RequiredArgsConstructor
public class InventarioService {

    private final PuntoEcaRepository puntoEcaRepository;
    private final MaterialRepository materialRepository;
}
