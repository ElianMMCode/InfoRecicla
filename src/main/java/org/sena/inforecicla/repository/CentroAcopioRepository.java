package org.sena.inforecicla.repository;

import org.sena.inforecicla.model.CentroAcopio;
import org.sena.inforecicla.model.enums.Estado;
import org.sena.inforecicla.model.enums.Visibilidad;

import java.util.List;
import java.util.Optional;
import java.util.UUID;

public interface CentroAcopioRepository extends BaseRepository<CentroAcopio, UUID> {

    List<CentroAcopio> findAllByPuntoEca_PuntoEcaIDAndEstadoAndVisibilidad(UUID puntoId, Estado estado, Visibilidad visibilidad);

    List<CentroAcopio> findAllByPuntoEca_PuntoEcaID(UUID puntoId);

    Optional<CentroAcopio> findByCntAcpIdAndPuntoEca_PuntoEcaID(UUID centroId, UUID puntoId);

    Optional<CentroAcopio> findAllByPuntoEca_PuntoEcaIDAndCntAcpId(UUID puntoId, UUID centroId);
}

