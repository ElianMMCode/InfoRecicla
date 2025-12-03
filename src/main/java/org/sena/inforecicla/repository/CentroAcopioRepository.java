package org.sena.inforecicla.repository;

import org.sena.inforecicla.model.CentroAcopio;
import org.sena.inforecicla.model.enums.Estado;
import org.sena.inforecicla.model.enums.Visibilidad;

import java.util.List;
import java.util.UUID;
import java.util.stream.Stream;

public interface CentroAcopioRepository extends BaseRepository<CentroAcopio, UUID> {

    List<CentroAcopio> findAllByPuntoEca_PuntoEcaIDAndEstadoAndVisibilidad(UUID puntoId, Estado estado, Visibilidad visibilidad);

    default List<CentroAcopio> findAllByPuntoEca_PuntoEcaIDAndVisibilidad_Eca(UUID puntoId) {
        return findAllByPuntoEca_PuntoEcaIDAndEstadoAndVisibilidad(puntoId, Estado.Activo ,Visibilidad.ECA);
    }

    default List<CentroAcopio> findAllByPuntoEca_PuntoEcaIDAndVisibilidad_Global(UUID puntoId) {
        return findAllByPuntoEca_PuntoEcaIDAndEstadoAndVisibilidad(puntoId, Estado.Activo, Visibilidad.GLOBAL);
    }

    default List<CentroAcopio> findAllByPuntoEca(UUID puntoId){
        List<CentroAcopio> centroAcopioEca = findAllByPuntoEca_PuntoEcaIDAndVisibilidad_Eca(puntoId);
        List<CentroAcopio> centroAcopioGlobal = findAllByPuntoEca_PuntoEcaIDAndVisibilidad_Global(puntoId);
        return Stream.concat(centroAcopioEca.stream(),centroAcopioGlobal.stream()).toList();
    }
}

