package org.sena.inforecicla.service;

import org.sena.inforecicla.dto.puntoEca.inventario.movimientos.VentaInventarioResponseDTO;
import org.springframework.data.domain.Page;

import java.util.UUID;

public interface VentaInventarioService {
    Page<VentaInventarioResponseDTO> ventasDelPunto(UUID puntoEcaId, int page, int size);
}
