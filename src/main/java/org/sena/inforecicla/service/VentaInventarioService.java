package org.sena.inforecicla.service;

import org.sena.inforecicla.dto.puntoEca.inventario.movimientos.VentaInventarioRequestDTO;
import org.sena.inforecicla.dto.puntoEca.inventario.movimientos.VentaInventarioResponseDTO;
import org.sena.inforecicla.exception.InventarioNotFoundException;
import org.springframework.data.domain.Page;

import java.util.UUID;

public interface VentaInventarioService {
    Page<VentaInventarioResponseDTO> ventasDelPunto(UUID puntoEcaId, int page, int size);

    VentaInventarioResponseDTO registrarVenta(VentaInventarioRequestDTO ventaDTO) throws InventarioNotFoundException;
}
