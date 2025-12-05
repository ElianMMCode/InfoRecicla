package org.sena.inforecicla.repository;

import org.sena.inforecicla.model.Inventario;
import org.sena.inforecicla.model.enums.Estado;
import java.util.List;
import java.util.Optional;
import java.util.UUID;

public interface InventarioRepository extends BaseRepository<Inventario, UUID> {

    List<Inventario> findAllByEstado(Estado estado);
    //Búsqueda por Punto Eca
    List<Inventario> findAllByPuntoEca_PuntoEcaID(UUID puntoEcaId);

    //Confirmar existencia de inventario
    Optional<Inventario> findByInventarioIdAndPuntoEca_PuntoEcaIDAndMaterial_MaterialId(UUID inventarioId, UUID puntoId, UUID materialId);
}
