package org.sena.inforecicla.service;

import org.sena.inforecicla.model.PuntoECA;
import org.sena.inforecicla.model.enums.Estado;

import java.util.Optional;
import java.util.UUID;

public interface PuntoEcaService {

    Optional<PuntoECA> buscarPuntoEca(UUID puntoId);

    Optional<PuntoECA> buscarPuntoEcaEstado(UUID puntoId, Estado estado);
}
