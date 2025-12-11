package org.sena.inforecicla.service.impl;

import lombok.AllArgsConstructor;
import org.sena.inforecicla.dto.puntoEca.inventario.movimientos.CompraInventarioDeleteDTO;
import org.sena.inforecicla.dto.puntoEca.inventario.movimientos.CompraInventarioRequestDTO;
import org.sena.inforecicla.dto.puntoEca.inventario.movimientos.CompraInventarioResponseDTO;
import org.sena.inforecicla.dto.puntoEca.inventario.movimientos.CompraInventarioUpdateDTO;
import org.sena.inforecicla.exception.InventarioNotFoundException;
import org.sena.inforecicla.model.CompraInventario;
import org.sena.inforecicla.model.Inventario;
import org.sena.inforecicla.model.enums.Estado;
import org.sena.inforecicla.repository.ComprasInventarioRepository;
import org.sena.inforecicla.service.CompraInventarioService;
import org.sena.inforecicla.service.InventarioService;
import org.springframework.data.domain.Page;
import org.springframework.data.domain.PageRequest;
import org.springframework.data.domain.Pageable;
import org.springframework.data.domain.Sort;
import org.springframework.stereotype.Service;

import java.math.BigDecimal;
import java.util.List;
import java.util.UUID;

@Service
@AllArgsConstructor
public class CompraInventarioServiceImpl implements CompraInventarioService, MovimientoInventario<CompraInventarioRequestDTO> {

    private final ComprasInventarioRepository comprasInventarioRepository;
    private final InventarioService inventarioService;

    @Override
    public CompraInventarioResponseDTO registrarCompra(CompraInventarioRequestDTO dto) throws InventarioNotFoundException {

        UUID inventarioId = dto.inventarioId();
        UUID puntoId = dto.puntoEcaId();
        UUID materialId = dto.materialId();

        Inventario inv = inventarioService.obtenerInventarioValido(inventarioId, puntoId, materialId);

        // ========== VALIDACIONES ==========
        // 1. Validar que la cantidad no supere la capacidad máxima
        if (inv.getCapacidadMaxima().compareTo(dto.cantidad()) < 0) {
            throw new InventarioNotFoundException(
                    "La cantidad a registrar (" + dto.cantidad() + " kg) supera la capacidad máxima del inventario (" +
                            inv.getCapacidadMaxima() + " kg). Por favor, ingresa una cantidad menor."
            );
        }

        // 2. Validar que la cantidad no supere el stock actual disponible
        BigDecimal stockDisponible = inv.getCapacidadMaxima().subtract(inv.getStockActual());
        if (stockDisponible.compareTo(dto.cantidad()) < 0) {
            throw new InventarioNotFoundException(
                    "La cantidad a registrar (" + dto.cantidad() + " kg) supera el espacio disponible en el inventario. " +
                            "Stock actual: " + inv.getStockActual() + " kg, " +
                            "Capacidad disponible: " + stockDisponible + " kg. " +
                            "Por favor, ingresa una cantidad menor."
            );
        }

        CompraInventario compra = CompraInventario.builder()
                .cantidad(dto.cantidad())
                .fechaCompra(dto.fechaCompra())
                .inventario(inv)
                .observaciones(dto.observaciones())
                .precioCompra(dto.precioCompra())
                .build();


        comprasInventarioRepository.save(compra);
        actualizarStockInventario(dto, inv);

        return CompraInventarioResponseDTO.derivado(compra);

    }

    @Override
    public Page<CompraInventarioResponseDTO> comprasDelPunto(UUID puntoId, int page, int size) {
        Pageable pageable = PageRequest.of(page, size, Sort.by("fechaCompra").descending());
        return comprasInventarioRepository.findAllByEstadoAndInventario_PuntoEca_PuntoEcaID(Estado.Activo, puntoId, pageable)
                .map(CompraInventarioResponseDTO::derivado);
    }


    @Override
    public CompraInventarioResponseDTO actualizarCompra(CompraInventarioUpdateDTO dto) throws InventarioNotFoundException {

        CompraInventario valida = obtenerCompraValidaInventario(dto.compraId(), dto.inventarioId(), dto.puntoId());

        BigDecimal diferencia;

        Inventario inv = inventarioService.obtenerInventarioValido(dto.inventarioId(), dto.puntoId(), dto.materialId());

        if (dto.cantidad().compareTo(valida.getCantidad()) > 0) {
            diferencia = dto.cantidad().subtract(valida.getCantidad());
            inv.setStockActual(inv.getStockActual().add(diferencia));
        } else if (dto.cantidad().compareTo(valida.getCantidad()) < 0) {
            diferencia = valida.getCantidad().subtract(dto.cantidad());
            inv.setStockActual(inv.getStockActual().subtract(diferencia));
        }

        valida.setFechaCompra(dto.fechaCompra());
        valida.setCantidad(dto.cantidad());
        valida.setPrecioCompra(dto.precioCompra());
        valida.setCantidad(dto.cantidad());
        valida.setObservaciones(dto.observaciones());

        inventarioService.actualizarStock(inv);
        comprasInventarioRepository.save(valida);

        return CompraInventarioResponseDTO.derivado(valida);
    }

    @Override
    public void eliminarCompra(CompraInventarioDeleteDTO dto) throws InventarioNotFoundException {
        CompraInventario compra = obtenerCompraValidaInventario(dto.compraId(), dto.inventarioId(), dto.puntoId());
        Inventario inv = inventarioService.obtenerInventarioValido(dto.inventarioId(), dto.puntoId(), dto.materialId());
        compra.setEstado(Estado.Inactivo);
        comprasInventarioRepository.save(compra);

        inv.setStockActual(inv.getStockActual().subtract(compra.getCantidad()));
        inventarioService.actualizarStock(inv);
    }

    @Override
    public CompraInventario obtenerCompraValidaInventario(UUID compraId, UUID inventarioId, UUID puntoId) throws InventarioNotFoundException {
        return comprasInventarioRepository.
                findByCompraIdAndInventario_InventarioIdAndInventario_PuntoEca_PuntoEcaID(compraId, inventarioId, puntoId)
                .orElseThrow(() -> new InventarioNotFoundException("Compra " + compraId + " asociada al punto " + puntoId + " no registrada"));
    }

    @Override
    public List<CompraInventario> comprasInventarioPunto(UUID puntoId, UUID inventarioId) {
        return comprasInventarioRepository.findAllByEstadoAndInventario_InventarioIdAndInventario_PuntoEca_PuntoEcaID(Estado.Activo, puntoId, inventarioId);
    }

    @Override
    public void eliminarComprasAsociadasInventario(UUID puntoId, UUID inventarioId) {
        // Buscar TODAS las compras del inventario (sin filtrar por estado)
        List<CompraInventario> comprasInventario = comprasInventarioRepository
                .findAllByInventario_InventarioIdAndInventario_PuntoEca_PuntoEcaID(inventarioId, puntoId);
        comprasInventario.forEach(c -> c.setEstado(Estado.Inactivo));
        comprasInventarioRepository.saveAll(comprasInventario);
    }

    @Override
    public void actualizarStockInventario(CompraInventarioRequestDTO dto, Inventario inv) {
        // Las validaciones ya se hacen en registrarCompra()
        // Solo actualizar el stock
        inv.setStockActual(inv.getStockActual().add(dto.cantidad()));

        inventarioService.actualizarStock(inv);
    }
}
