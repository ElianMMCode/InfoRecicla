package org.sena.inforecicla.repository;

import org.sena.inforecicla.model.Inventario;
import org.sena.inforecicla.model.enums.Estado;
import java.util.List;
import java.util.UUID;

public interface InventarioRepository extends BaseRepository<Inventario, UUID> {

    List<Inventario> findAllByEstado(Estado estado);
    //Búsqueda por Punto Eca
    List<Inventario> findAllByPuntoEca_PuntoEcaID(UUID puntoEcaId);


}
