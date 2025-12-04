package org.sena.inforecicla.service;

import lombok.RequiredArgsConstructor;
import org.sena.inforecicla.dto.puntoEca.InventarioResponseDTO;
import org.sena.inforecicla.model.Inventario;
import org.sena.inforecicla.repository.InventarioRepository;
import org.sena.inforecicla.repository.MaterialRepository;
import org.sena.inforecicla.repository.PuntoEcaRepository;
import org.springframework.stereotype.Service;

import java.util.List;
import java.util.UUID;

import static java.util.Comparator.comparing;

@Service
@RequiredArgsConstructor
public class InventarioService {

    private final PuntoEcaRepository puntoEcaRepository;
    private final MaterialRepository materialRepository;
    private final InventarioRepository inventarioRepository;

    public List<InventarioResponseDTO> mostrarInventariosPuntoEca(UUID puntoId) {

        List<Inventario> inv = inventarioRepository.findAllByPuntoEca_PuntoEcaID(puntoId);

        return inv.stream().map(i -> InventarioResponseDTO.builder()
                        .capacidadMaxima(i.getCapacidadMaxima())
                        .unidadMedida(i.getUnidadMedida())
                        .stockActual(i.getStockActual())
                        .umbralAlerta(i.getUmbralAlerta())
                        .umbralCritico(i.getUmbralCritico())
                        .precioCompra(i.getPrecioCompra())
                        .precioVenta(i.getPrecioVenta())
                        .materialId(i.getMaterial().getMaterialId())
                        .nombreMaterial(i.getMaterial().getNombre())
                        .puntoEcaId(i.getPuntoEca().getPuntoEcaID())
                        .nombrePuntoEca(i.getPuntoEca().getNombrePunto())
                        .fechaCreacion(i.getFechaCreacion())
                        .fechaActualizacion(i.getFechaActualizacion())
                        .build())
                .sorted(comparing(InventarioResponseDTO::fechaActualizacion).reversed())
                .toList();
    }
}
