package org.sena.inforecicla.repository;

import org.sena.inforecicla.model.CentroAcopio;
import org.sena.inforecicla.model.enums.Estado;
import org.sena.inforecicla.model.enums.Visibilidad;
import org.springframework.data.jpa.repository.Query;

import java.util.List;
import java.util.Optional;
import java.util.UUID;

public interface CentroAcopioRepository extends BaseRepository<CentroAcopio, UUID> {

    List<CentroAcopio> findAllByPuntoEca_PuntoEcaIDAndEstadoAndVisibilidad(UUID puntoId, Estado estado, Visibilidad visibilidad);

    List<CentroAcopio> findAllByPuntoEca_PuntoEcaID(UUID puntoId);

    Optional<CentroAcopio> findByCntAcpIdAndPuntoEca_PuntoEcaID(UUID centroId, UUID puntoId);

    Optional<CentroAcopio> findAllByPuntoEca_PuntoEcaIDAndCntAcpId(UUID puntoId, UUID centroId);

    // Métodos con Fetch JOIN para cargar localidad eagerly
    @Query("SELECT DISTINCT c FROM CentroAcopio c LEFT JOIN FETCH c.localidad")
    List<CentroAcopio> findAllWithLocalidad();

    @Query("SELECT DISTINCT c FROM CentroAcopio c LEFT JOIN FETCH c.localidad WHERE c.puntoEca.puntoEcaID = :puntoId")
    List<CentroAcopio> findAllByPuntoEcaWithLocalidad(UUID puntoId);
}

