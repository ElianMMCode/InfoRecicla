package org.sena.inforecicla.service;

import org.sena.inforecicla.dto.puntoEca.inventario.movimientos.CompraInventarioRequestDTO;
import org.sena.inforecicla.dto.puntoEca.inventario.movimientos.CompraInventarioResponseDTO;
import org.sena.inforecicla.exception.InventarioNotFoundException;
import org.springframework.data.domain.Page;

import java.util.UUID;

public interface CompraInventarioService {

    CompraInventarioResponseDTO registrarCompra(CompraInventarioRequestDTO dto) throws InventarioNotFoundException;

    Page<CompraInventarioResponseDTO> comprasDelPunto(UUID puntoId, int page, int size);
}
