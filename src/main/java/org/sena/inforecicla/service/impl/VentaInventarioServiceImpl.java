package org.sena.inforecicla.service.impl;

import lombok.AllArgsConstructor;
import org.sena.inforecicla.dto.puntoEca.inventario.movimientos.VentaInventarioResponseDTO;
import org.sena.inforecicla.repository.VentasInventarioRepository;
import org.sena.inforecicla.service.VentaInventarioService;
import org.springframework.data.domain.Page;
import org.springframework.data.domain.PageRequest;
import org.springframework.data.domain.Pageable;
import org.springframework.data.domain.Sort;
import org.springframework.stereotype.Service;

import java.util.UUID;

@Service
@AllArgsConstructor
public class VentaInventarioServiceImpl implements VentaInventarioService {

    private final VentasInventarioRepository ventasInventarioRepository;

    @Override
    public Page<VentaInventarioResponseDTO> ventasDelPunto(UUID puntoEcaId, int page, int size) {
        Pageable pageable = PageRequest.of(page, size, Sort.by("fechaVenta").descending());
        return ventasInventarioRepository.findAllByInventario_PuntoEca_PuntoEcaID(puntoEcaId,pageable)
                .map(VentaInventarioResponseDTO::derivado);
    }
}
