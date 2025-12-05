package org.sena.inforecicla.repository;

import org.sena.inforecicla.model.PuntoECA;
import org.sena.inforecicla.model.enums.Estado;

import java.util.Optional;
import java.util.UUID;

public interface PuntoEcaRepository extends BaseRepository<PuntoECA, UUID> {
    Optional<PuntoECA> findByPuntoEcaIDAndGestorId(UUID puntoEcaId, UUID usuarioId);

    Optional<PuntoECA> findByPuntoEcaIDAndEstado(UUID puntoEcaID, Estado estado);
}
