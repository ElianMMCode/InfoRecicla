package org.sena.inforecicla.dto.puntoEca.inventario.movimientos;

import jakarta.validation.constraints.*;
import java.math.BigDecimal;
import java.time.LocalDateTime;
import java.util.UUID;

/**
 * DTO para guardar una nueva venta de inventario (salida de material)
 */
public record VentaInventarioRequestDTO(

        @NotNull(message = "El inventario ID es obligatorio")
        UUID inventarioId,

        @NotNull(message = "El puntoEca ID es obligatorio")
        UUID puntoEcaId,

        @NotNull(message = "El material ID es obligatorio")
        UUID materialId,

        @NotNull(message = "La fecha de venta es obligatoria")
        LocalDateTime fechaVenta,

        @NotNull(message = "El precio de venta es obligatorio")
        @DecimalMin(value = "0.01", message = "El precio de venta debe ser mayor a 0.01")
        @Digits(integer = 12, fraction = 2, message = "Formato inválido para precio de venta")
        BigDecimal precioVenta,

        @NotNull
        @DecimalMin(value = "0.1", message = "La cantidad no puede ser negativo")
        @Digits(integer = 10, fraction = 2, message = "Formato inválido para cantidad actual")
        BigDecimal cantidad,

        UUID centroAcopioId,

        @Size(max = 500, message = "Las observaciones no pueden exceder 500 caracteres")
        String observaciones

) {
}

