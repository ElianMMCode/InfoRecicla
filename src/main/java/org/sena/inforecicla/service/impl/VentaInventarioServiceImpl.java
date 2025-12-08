package org.sena.inforecicla.service.impl;

import lombok.AllArgsConstructor;
import org.sena.inforecicla.dto.puntoEca.inventario.movimientos.*;
import org.sena.inforecicla.exception.InventarioNotFoundException;
import org.sena.inforecicla.model.CentroAcopio;
import org.sena.inforecicla.model.Inventario;
import org.sena.inforecicla.model.VentaInventario;
import org.sena.inforecicla.model.enums.Estado;
import org.sena.inforecicla.repository.VentasInventarioRepository;
import org.sena.inforecicla.service.VentaInventarioService;
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
public class VentaInventarioServiceImpl implements VentaInventarioService, MovimientoInventario<VentaInventarioRequestDTO> {

    private final VentasInventarioRepository ventasInventarioRepository;
    private final InventarioServiceImpl inventarioService;
    private final CentroAcopioServiceImpl centroAcopioService;

    @Override
    public Page<VentaInventarioResponseDTO> ventasDelPunto(UUID puntoEcaId, int page, int size) {
        Pageable pageable = PageRequest.of(page, size, Sort.by("fechaVenta").descending());
        return ventasInventarioRepository.findAllByEstadoAndInventario_PuntoEca_PuntoEcaID(Estado.Activo,puntoEcaId,pageable)
                .map(VentaInventarioResponseDTO::derivado);
    }

    @Override
    public List<VentaInventario> ventasInventarioPunto(UUID puntoId, UUID inventarioId) {
        return ventasInventarioRepository.findAllByEstadoAndInventario_InventarioIdAndInventario_PuntoEca_PuntoEcaID(Estado.Activo, puntoId, inventarioId);
    }


    @Override
    public VentaInventarioResponseDTO registrarVenta(VentaInventarioRequestDTO dto) throws InventarioNotFoundException {

        UUID inventarioId = dto.inventarioId();
        UUID puntoId = dto.puntoEcaId();
        UUID materialId = dto.materialId();
        UUID centroId = dto.centroAcopioId();

        Inventario inv = inventarioService.obtenerInventarioValido(inventarioId, puntoId, materialId);

        // Centro Acopio es opcional
        CentroAcopio centroPunto = null;
        if (centroId != null) {
            centroPunto = centroAcopioService.obtenerCentroValidoPunto(centroId, puntoId);
        }

        VentaInventario venta = VentaInventario.builder()
                .cantidad(dto.cantidad())
                .fechaVenta(dto.fechaVenta())
                .inventario(inv)
                .precioVenta(dto.precioVenta())
                .observaciones(dto.observaciones())
                .ctrAcopio(centroPunto).build();

        ventasInventarioRepository.save(venta);
        actualizarStockInventario(dto, inv);

        return VentaInventarioResponseDTO.derivado(venta);
    }


    @Override
    public VentaInventarioResponseDTO actualizarVenta(VentaInventarioUpdateDTO dto)throws InventarioNotFoundException{

        VentaInventario valida = obtenerVentaValidaInventario(dto);

        BigDecimal diferencia;

        Inventario inv = inventarioService.obtenerInventarioValido(dto.inventarioId(), dto.puntoId(), dto.materialId());

        if (dto.cantidad().compareTo(valida.getCantidad()) > 0) {
            diferencia = dto.cantidad().subtract(valida.getCantidad());
            inv.setStockActual(inv.getStockActual().subtract(diferencia));
        } else if (dto.cantidad().compareTo(valida.getCantidad()) < 0) {
            diferencia = valida.getCantidad().subtract(dto.cantidad());
            inv.setStockActual(inv.getStockActual().add(diferencia));
        }

        CentroAcopio cnt = null;
        if (dto.centroAcopioId() != null) {
            cnt = centroAcopioService.obtenerCentroValidoPunto(dto.centroAcopioId(), dto.puntoId());
        }

        valida.setFechaVenta(dto.fechaVenta());
        valida.setPrecioVenta(dto.precioVenta());
        valida.setCantidad(dto.cantidad());
        valida.setCtrAcopio(cnt);
        valida.setObservaciones(dto.observaciones());

        ventasInventarioRepository.save(valida);
        inventarioService.actualizarStock(inv);

        return VentaInventarioResponseDTO.derivado(valida);
    }

    @Override
    public VentaInventario obtenerVentaValidaInventario(VentaInventarioUpdateDTO dto) throws InventarioNotFoundException {
        UUID ventaId = dto.ventaId();
        UUID inventarioId = dto.inventarioId();
        UUID puntoId = dto.puntoId();

        return ventasInventarioRepository
                .findByVentaIdAndInventario_InventarioIdAndInventario_PuntoEca_PuntoEcaID(ventaId,inventarioId,puntoId)
                .orElseThrow(() -> new InventarioNotFoundException("Venta " + ventaId + " asociada al punto " + puntoId + " no registrada"));
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

    @Override
    public void eliminarVenta(VentaInventarioDeleteDTO dto) throws InventarioNotFoundException {
        VentaInventario venta = obtenerVentaValidaInventario(dto.ventaId(), dto.inventarioId(), dto.puntoId());
        Inventario inv = inventarioService.obtenerInventarioValido(dto.inventarioId(), dto.puntoId(), dto.materialId());
        venta.setEstado(Estado.Inactivo);
        ventasInventarioRepository.save(venta);

        inv.setStockActual(inv.getStockActual().add(venta.getCantidad()));

        inventarioService.actualizarStock(inv);
    }

    @Override
    public VentaInventario obtenerVentaValidaInventario(UUID ventaId, UUID inventarioId, UUID puntoId) throws InventarioNotFoundException {
        return ventasInventarioRepository
                .findByVentaIdAndInventario_InventarioIdAndInventario_PuntoEca_PuntoEcaID(ventaId, inventarioId, puntoId)
                .orElseThrow(() -> new InventarioNotFoundException("Venta " + ventaId + " asociada al punto " + puntoId + " no registrada"));
    }

    @Override
    public void eliminarVentasAsociadasInventario(UUID puntoId, UUID inventarioId) {
        // Buscar TODAS las ventas del inventario (sin filtrar por estado)
        List<VentaInventario> ventasInventario = ventasInventarioRepository
                .findAllByInventario_InventarioIdAndInventario_PuntoEca_PuntoEcaID(inventarioId, puntoId);
        ventasInventario.forEach(v -> v.setEstado(Estado.Inactivo));
        ventasInventarioRepository.saveAll(ventasInventario);
    }
}
