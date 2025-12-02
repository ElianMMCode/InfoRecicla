package org.sena.inforecicla.repository;

import org.sena.inforecicla.model.CompraInventario;
import org.sena.inforecicla.model.VentaInventario;
import org.springframework.data.domain.Page;
import org.springframework.data.domain.Pageable;

import java.util.Optional;
import java.util.UUID;

public interface ComprasInventarioRepository extends BaseRepository<CompraInventario, UUID>{

    Page<CompraInventario> findAllByInventario_PuntoEca_PuntoEcaID(UUID puntoId, Pageable pageable);

}
