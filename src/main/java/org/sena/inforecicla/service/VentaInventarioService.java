package org.sena.inforecicla.service;

import org.sena.inforecicla.dto.puntoEca.inventario.movimientos.*;
import org.sena.inforecicla.exception.InventarioNotFoundException;
import org.sena.inforecicla.model.VentaInventario;
import org.springframework.data.domain.Page;

import java.util.List;
import java.util.UUID;

public interface VentaInventarioService {
    Page<VentaInventarioResponseDTO> ventasDelPunto(UUID puntoEcaId, int page, int size);

    List<VentaInventario> ventasInventarioPunto(UUID puntoId, UUID inventarioId);

    VentaInventarioResponseDTO registrarVenta(VentaInventarioRequestDTO ventaDTO) throws InventarioNotFoundException;

    VentaInventarioResponseDTO actualizarVenta(VentaInventarioUpdateDTO dto)throws InventarioNotFoundException;

    VentaInventario obtenerVentaValidaInventario(VentaInventarioUpdateDTO dto) throws InventarioNotFoundException;

    void eliminarVenta(VentaInventarioDeleteDTO dto) throws InventarioNotFoundException;

    VentaInventario obtenerVentaValidaInventario(UUID ventaId, UUID inventarioId, UUID puntoId) throws InventarioNotFoundException;

    void eliminarVentasAsociadasInventario(UUID puntoId, UUID inventarioId);
}
