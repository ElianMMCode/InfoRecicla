package org.sena.inforecicla.service;

import org.sena.inforecicla.dto.puntoEca.inventario.movimientos.*;
import org.sena.inforecicla.exception.InventarioNotFoundException;
import org.sena.inforecicla.model.CompraInventario;
import org.springframework.data.domain.Page;

import java.util.List;
import java.util.UUID;

public interface CompraInventarioService {

    CompraInventarioResponseDTO registrarCompra(CompraInventarioRequestDTO dto) throws InventarioNotFoundException;

    Page<CompraInventarioResponseDTO> comprasDelPunto(UUID puntoId, int page, int size);

    CompraInventarioResponseDTO actualizarCompra(CompraInventarioUpdateDTO dto) throws InventarioNotFoundException;

    void eliminarCompra(CompraInventarioDeleteDTO dto) throws InventarioNotFoundException;

    CompraInventario obtenerCompraValidaInventario(UUID compraId, UUID inventarioId, UUID puntoId) throws InventarioNotFoundException;

    List<CompraInventario> comprasInventarioPunto(UUID puntoId, UUID inventarioId);

    void eliminarComprasAsociadasInventario(UUID puntoId, UUID inventarioId);
}
