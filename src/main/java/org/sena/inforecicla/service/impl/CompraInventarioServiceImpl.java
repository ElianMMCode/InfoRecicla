package org.sena.inforecicla.service.impl;

import lombok.AllArgsConstructor;
import org.sena.inforecicla.dto.puntoEca.inventario.movimientos.CompraInventarioResponseDTO;
import org.sena.inforecicla.repository.ComprasInventarioRepository;
import org.sena.inforecicla.service.CompraInventarioService;
import org.springframework.data.domain.Page;
import org.springframework.data.domain.PageRequest;
import org.springframework.data.domain.Pageable;
import org.springframework.data.domain.Sort;
import org.springframework.stereotype.Service;

import java.util.UUID;

@Service
@AllArgsConstructor
public class CompraInventarioServiceImpl implements CompraInventarioService {

    private final ComprasInventarioRepository comprasInventarioRepository;

    @Override
    public Page<CompraInventarioResponseDTO> comprasDelPunto(UUID puntoId, int page, int size) {
        Pageable pageable = PageRequest.of(page, size, Sort.by("fechaCompra").descending());
        return comprasInventarioRepository.findAllByInventario_PuntoEca_PuntoEcaID(puntoId,pageable)
                .map(CompraInventarioResponseDTO::derivado);
    }
}
