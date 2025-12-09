package org.sena.inforecicla.repository;

import org.sena.inforecicla.model.CompraInventario;
import org.sena.inforecicla.model.enums.Estado;
import org.springframework.data.domain.Page;
import org.springframework.data.domain.Pageable;

import java.util.List;
import java.util.Optional;
import java.util.UUID;

public interface ComprasInventarioRepository extends BaseRepository<CompraInventario, UUID>{

    Page<CompraInventario> findAllByEstadoAndInventario_PuntoEca_PuntoEcaID(Estado estado, UUID puntoId, Pageable pageable);
    List<CompraInventario> findAllByEstadoAndInventario_InventarioIdAndInventario_PuntoEca_PuntoEcaID(Estado estado,UUID inventarioId,UUID puntoId);

    CompraInventario findByCompraIdAndInventario_InventarioId(UUID compraId, UUID inventarioInventarioId);

    Optional<CompraInventario> findByCompraIdAndInventario_InventarioIdAndInventario_PuntoEca_PuntoEcaID(UUID compraId, UUID compraId1, UUID compraId2);

    List<CompraInventario> findAllByInventario_InventarioIdAndInventario_PuntoEca_PuntoEcaID(UUID inventarioId, UUID puntoId);
}
