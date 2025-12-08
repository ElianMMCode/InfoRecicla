package org.sena.inforecicla.repository;

import org.sena.inforecicla.model.VentaInventario;
import org.sena.inforecicla.model.enums.Estado;
import org.springframework.data.domain.Page;
import org.springframework.data.domain.Pageable;

import java.util.List;
import java.util.Optional;
import java.util.UUID;

public interface VentasInventarioRepository extends BaseRepository<VentaInventario, UUID>{

    Page<VentaInventario> findAllByEstadoAndInventario_PuntoEca_PuntoEcaID(Estado estado,UUID puntoId, Pageable pageable);

    Optional<VentaInventario> findByVentaIdAndInventario_InventarioIdAndInventario_PuntoEca_PuntoEcaID(UUID ventaId, UUID inventarioId, UUID puntoId);

    List<VentaInventario> findAllByEstadoAndInventario_InventarioIdAndInventario_PuntoEca_PuntoEcaID(Estado estado, UUID puntoId, UUID inventarioId);

    List<VentaInventario> findAllByInventario_InventarioIdAndInventario_PuntoEca_PuntoEcaID(UUID inventarioId, UUID puntoId);
}
