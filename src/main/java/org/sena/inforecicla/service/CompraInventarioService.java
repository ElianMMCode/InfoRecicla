package org.sena.inforecicla.service;

import org.sena.inforecicla.dto.puntoEca.inventario.movimientos.CompraInventarioResponseDTO;
import org.sena.inforecicla.model.CompraInventario;
import org.springframework.data.domain.Page;

import java.util.Optional;
import java.util.UUID;

public interface CompraInventarioService {

    Page<CompraInventarioResponseDTO> comprasDelPunto(UUID puntoId, int page, int size);
}
