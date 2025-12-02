package org.sena.inforecicla.dto.puntoEca.inventario.movimientos;

import org.sena.inforecicla.model.CompraInventario;
import java.math.BigDecimal;
import java.time.LocalDateTime;
import java.util.UUID;

/**
 * DTO para mostrar una compra de inventario con todos sus detalles
 */
public record CompraInventarioResponseDTO(

        UUID compraId,
        UUID inventarioId,
        String nombreMaterial,
        LocalDateTime fechaCompra,
        BigDecimal precioCompra,
        String observaciones,
        LocalDateTime fechaCreacion,
        LocalDateTime fechaActualizacion

) {

    /**
     * Convierte una entidad CompraInventario a su DTO de respuesta
     */
    public static CompraInventarioResponseDTO derivado(CompraInventario compra) {
        return new CompraInventarioResponseDTO(
                compra.getCompraId(),
                compra.getInventario().getInventarioId(),
                compra.getInventario().getMaterial().getNombre(),
                compra.getFechaCompra(),
                compra.getPrecioCompra(),
                compra.getObservaciones(),
                compra.getFechaCreacion(),
                compra.getFechaActualizacion()
        );
    }

}

