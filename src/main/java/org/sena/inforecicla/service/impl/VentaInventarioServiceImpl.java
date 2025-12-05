package org.sena.inforecicla.service.impl;

import lombok.AllArgsConstructor;
import org.sena.inforecicla.dto.puntoEca.inventario.movimientos.VentaInventarioRequestDTO;
import org.sena.inforecicla.dto.puntoEca.inventario.movimientos.VentaInventarioResponseDTO;
import org.sena.inforecicla.exception.InventarioNotFoundException;
import org.sena.inforecicla.model.CentroAcopio;
import org.sena.inforecicla.model.Inventario;
import org.sena.inforecicla.model.VentaInventario;
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
public class VentaInventarioServiceImpl implements VentaInventarioService, MovimientoInventario<VentaInventarioRequestDTO> {

    private final VentasInventarioRepository ventasInventarioRepository;
    private final InventarioServiceImpl inventarioService;
    private final CentroAcopioServiceImpl centroAcopioServiceImpl;

    @Override
    public Page<VentaInventarioResponseDTO> ventasDelPunto(UUID puntoEcaId, int page, int size) {
        Pageable pageable = PageRequest.of(page, size, Sort.by("fechaVenta").descending());
        return ventasInventarioRepository.findAllByInventario_PuntoEca_PuntoEcaID(puntoEcaId,pageable)
                .map(VentaInventarioResponseDTO::derivado);
    }

    @Override
    public VentaInventarioResponseDTO registrarVenta(VentaInventarioRequestDTO dto) throws InventarioNotFoundException {

        UUID inventarioId = dto.inventarioId();
        UUID puntoId = dto.puntoEcaId();
        UUID materialId = dto.materialId();
        UUID centroId = dto.centroAcopioId();

        Inventario inv = inventarioService.obtenerInventarioValidoParaCompra(inventarioId, puntoId, materialId);

        CentroAcopio centroPunto = centroAcopioServiceImpl.obtenerCentroValidoPunto(centroId, puntoId);

        VentaInventario venta = VentaInventario.builder()
                .cantidad(dto.cantidad())
                .fechaVenta(dto.fechaVenta())
                .inventario(inv)
                .precioVenta(dto.precioVenta())
                .observaciones(dto.observaciones())
                .ctrAcopio(centroPunto).build();

        actualizarStockInventario(dto, inv);

        ventasInventarioRepository.save(venta);

        return VentaInventarioResponseDTO.derivado(venta);
    }

    @Override
    public void actualizarStockInventario(VentaInventarioRequestDTO dto, Inventario inv) throws InventarioNotFoundException {


        if (inv.getStockActual().compareTo(dto.cantidad()) < 0) {
            throw new InventarioNotFoundException(
                    "La cantidad a registrar (" + dto.cantidad() + ") supera el stock actual del inventario (" +
                            inv.getCapacidadMaxima() + " ). Por favor, ingresa una cantidad menor."
            );
        }
        inv.setStockActual(inv.getStockActual().subtract(dto.cantidad()));

        inventarioService.actualizarStock(inv);

    }
}
