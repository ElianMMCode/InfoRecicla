package org.sena.inforecicla.repository;

import org.sena.inforecicla.model.Material;
import org.sena.inforecicla.model.enums.Estado;

import java.util.Optional;
import java.util.UUID;

public interface MaterialRepository extends BaseRepository<Material, UUID> {

//    List<Material> findAllByInventario
    Optional<Material> findByMaterialIdAndEstado(UUID material, Estado estado);

    default Optional<Material> findByMaterialIdAndEstadoActivo(UUID material){
        Estado estado = Estado.Activo;
        return findByMaterialIdAndEstado(material, estado);
    }

}
