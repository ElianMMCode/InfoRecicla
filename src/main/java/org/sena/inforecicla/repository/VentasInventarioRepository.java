package org.sena.inforecicla.repository;

import org.sena.inforecicla.model.VentaInventario;
import org.springframework.data.domain.Page;
import org.springframework.data.domain.Pageable;

import java.util.UUID;

public interface VentasInventarioRepository extends BaseRepository<VentaInventario, UUID>{
    Page<VentaInventario> findAllByInventario_PuntoEca_PuntoEcaID(UUID puntoId, Pageable pageable);
}
