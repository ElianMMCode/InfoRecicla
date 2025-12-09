package org.sena.inforecicla.dto.puntoEca.inventario.movimientos;

import jakarta.validation.constraints.*;
import java.math.BigDecimal;
import java.time.LocalDateTime;
import java.util.UUID;

public record VentaInventarioUpdateDTO(

        @NotNull(message = "El ID del punto ECA es obligatorio")
        UUID puntoId,

        @NotNull(message = "El ID de la venta es obligatorio")
        UUID ventaId,

        @NotNull(message = "El ID del material es obligatorio")
        UUID materialId,

        @NotNull(message = "El ID del inventario es obligatorio")
        UUID inventarioId,

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

        @NotNull(message = "El centro de acopio es obligatorio")
        UUID centroAcopioId,

        @Size(max = 500, message = "Las observaciones no pueden exceder 500 caracteres")
        String observaciones

) {
}

