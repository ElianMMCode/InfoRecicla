package org.sena.inforecicla.service;

import lombok.RequiredArgsConstructor;
import org.sena.inforecicla.dto.puntoEca.inventario.InventarioResponseDTO;
import org.sena.inforecicla.dto.puntoEca.inventario.InventarioUpdateDTO;
import org.sena.inforecicla.exception.InventarioNotFoundException;
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

    public List<InventarioResponseDTO> mostrarInventarioPuntoEca(UUID puntoId) {

        List<Inventario> inv = inventarioRepository.findAllByPuntoEca_PuntoEcaID(puntoId);

        return inv.stream().map(i -> InventarioResponseDTO.builder()
                        .inventarioId(i.getInventarioId())
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

    public InventarioResponseDTO actualizarInventario(UUID inventarioId, InventarioUpdateDTO invUpdate) throws InventarioNotFoundException {
        Inventario inventario = inventarioRepository.findById(inventarioId).orElseThrow(
                () -> new InventarioNotFoundException("Registro en el inventario no encontrado"));

        inventario.setStockActual(invUpdate.stockActual());
        inventario.setCapacidadMaxima(invUpdate.capacidadMaxima());
        inventario.setUnidadMedida(invUpdate.unidadMedida());
        inventario.setPrecioCompra(invUpdate.precioCompra());
        inventario.setPrecioVenta(invUpdate.precioVenta());
        inventario.setUmbralAlerta(invUpdate.umbralAlerta());
        inventario.setUmbralCritico(invUpdate.umbralCritico());

        Inventario guardado = inventarioRepository.save(inventario);

        return InventarioResponseDTO.builder()
                .materialId(guardado.getMaterial().getMaterialId())
                .capacidadMaxima(guardado.getCapacidadMaxima())
                .fechaActualizacion(guardado.getFechaActualizacion())
                .fechaCreacion(guardado.getFechaActualizacion())
                .nombreMaterial(guardado.getMaterial().getNombre())
                .nombrePuntoEca(guardado.getPuntoEca().getNombrePunto())
                .precioCompra(guardado.getPrecioCompra())
                .precioVenta(guardado.getPrecioVenta())
                .puntoEcaId(guardado.getPuntoEca().getPuntoEcaID())
                .stockActual(guardado.getStockActual())
                .umbralAlerta(guardado.getUmbralAlerta())
                .umbralCritico(guardado.getUmbralCritico())
                .unidadMedida(guardado.getUnidadMedida()).build();
    }
}
