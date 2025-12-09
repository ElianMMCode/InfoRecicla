package org.sena.inforecicla.dto.puntoEca.inventario.movimientos;

import java.math.BigDecimal;
import java.time.LocalDateTime;
import java.util.UUID;

/**
 * DTO para mostrar un historial general de movimientos (compras y ventas)
 * Utilizado en listados combinados
 */
public record MovimientoHistorialDTO(

        UUID movimientoId,
        String tipoMovimiento,  // "ENTRADA" o "SALIDA"
        String nombreMaterial,
        BigDecimal cantidad,
        BigDecimal precio,
        String detalleAdicional,  // Centro de acopio para salidas, proveedor para entradas
        LocalDateTime fecha,
        String observaciones

) {

    /**
     * Factory para crear un historial desde una compra
     */
    public static MovimientoHistorialDTO desdeCompra(CompraInventarioResponseDTO compra) {
        return new MovimientoHistorialDTO(
                compra.compraId(),
                "ENTRADA",
                compra.nombreMaterial(),
                null,  // La cantidad se maneja a nivel de inventario
                compra.precioCompra(),
                "Compra de material",
                compra.fechaCreacion(),
                compra.observaciones()
        );
    }

    /**
     * Factory para crear un historial desde una venta
     */
    public static MovimientoHistorialDTO desdeVenta(VentaInventarioResponseDTO venta) {
        return new MovimientoHistorialDTO(
                venta.ventaId(),
                "SALIDA",
                venta.nombreMaterial(),
                null,  // La cantidad se maneja a nivel de inventario
                venta.precioVenta(),
                venta.nombreCentroAcopio(),
                venta.fechaCreacion(),
                venta.observaciones()
        );
    }

}

