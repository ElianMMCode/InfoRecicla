package org.sena.inforecicla.service;

import org.sena.inforecicla.model.PuntoECA;

import java.util.Optional;
import java.util.UUID;

public interface PuntoEcaService {

    Optional<PuntoECA> buscarPuntoEca(UUID puntoId);

}
