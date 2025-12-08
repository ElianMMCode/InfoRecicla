package org.sena.inforecicla.dto.puntoEca.inventario.movimientos;

import org.sena.inforecicla.model.VentaInventario;
import java.math.BigDecimal;
import java.time.LocalDateTime;
import java.util.UUID;

/**
 * DTO para mostrar una venta de inventario con todos sus detalles
 */
public record VentaInventarioResponseDTO(

        UUID ventaId,
        UUID inventarioId,
        UUID materialId,
        String nombreMaterial,
        LocalDateTime fechaVenta,
        BigDecimal precioVenta,
        BigDecimal cantidad,
        UUID centroAcopioId,
        String nombreCentroAcopio,
        String observaciones,
        LocalDateTime fechaCreacion,
        LocalDateTime fechaActualizacion

) {

    /**
     * Convierte una entidad VentaInventario a su DTO de respuesta
     */
    public static VentaInventarioResponseDTO derivado(VentaInventario venta) {
        return new VentaInventarioResponseDTO(
                venta.getVentaId(),
                venta.getInventario().getInventarioId(),
                venta.getInventario().getMaterial().getMaterialId(),
                venta.getInventario().getMaterial().getNombre(),
                venta.getFechaVenta(),
                venta.getPrecioVenta(),
                venta.getCantidad(),
                venta.getCtrAcopio() != null ? venta.getCtrAcopio().getCntAcpId() : null,
                venta.getCtrAcopio() != null ? venta.getCtrAcopio().getNombreCntAcp() : null,
                venta.getObservaciones(),
                venta.getFechaCreacion(),
                venta.getFechaActualizacion()
        );
    }

}

