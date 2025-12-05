package org.sena.inforecicla.service.impl;

import lombok.AllArgsConstructor;
import org.sena.inforecicla.dto.puntoEca.inventario.movimientos.CompraInventarioRequestDTO;
import org.sena.inforecicla.dto.puntoEca.inventario.movimientos.CompraInventarioResponseDTO;
import org.sena.inforecicla.exception.InventarioNotFoundException;
import org.sena.inforecicla.model.CompraInventario;
import org.sena.inforecicla.model.Inventario;
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
public class CompraInventarioServiceImpl implements CompraInventarioService , MovimientoInventario<CompraInventarioRequestDTO>{

    private final ComprasInventarioRepository comprasInventarioRepository;
    private final InventarioServiceImpl inventarioService;

    @Override
    public CompraInventarioResponseDTO registrarCompra(CompraInventarioRequestDTO dto) throws InventarioNotFoundException{

        UUID inventarioId = dto.inventarioId();
        UUID puntoId = dto.puntoEcaId();
        UUID materialId = dto.materialId();

        Inventario inv = inventarioService.obtenerInventarioValidoParaCompra(inventarioId, puntoId, materialId);

        CompraInventario compra = CompraInventario.builder()
                .cantidad(dto.cantidad())
                .fechaCompra(dto.fechaCompra())
                .inventario(inv)
                .observaciones(dto.observaciones())
                .precioCompra(dto.precioCompra())
                .build();

        actualizarStockInventario(dto, inv);

        comprasInventarioRepository.save(compra);

        return CompraInventarioResponseDTO.derivado(compra);

    }

    @Override
    public Page<CompraInventarioResponseDTO> comprasDelPunto(UUID puntoId, int page, int size) {
        Pageable pageable = PageRequest.of(page, size, Sort.by("fechaCompra").descending());
        return comprasInventarioRepository.findAllByInventario_PuntoEca_PuntoEcaID(puntoId,pageable)
                .map(CompraInventarioResponseDTO::derivado);
    }

    @Override
    public void actualizarStockInventario(CompraInventarioRequestDTO dto, Inventario inv) throws InventarioNotFoundException{

        if (inv.getCapacidadMaxima().compareTo(dto.cantidad()) < 0) {
            throw new InventarioNotFoundException(
                    "La cantidad a registrar (" + dto.cantidad() + " kg) supera la capacidad máxima del inventario (" +
                            inv.getCapacidadMaxima() + " kg). Por favor, ingresa una cantidad menor."
            );
        }
        inv.setStockActual(inv.getStockActual().add(dto.cantidad()));

        inventarioService.actualizarStock(inv);
    }
}
