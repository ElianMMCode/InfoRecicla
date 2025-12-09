package org.sena.inforecicla.dto.puntoEca.inventario.movimientos;

import jakarta.validation.constraints.*;
import java.math.BigDecimal;
import java.time.LocalDateTime;
import java.util.UUID;

/**
 * DTO para actualizar una compra de inventario existente
 */
public record CompraInventarioUpdateDTO(

        @NotNull(message = "El ID del punto ECA es obligatorio")
        UUID puntoId,

        @NotNull(message = "El ID del material es obligatorio")
        UUID materialId,

        @NotNull(message = "El ID de la compra es obligatorio")
        UUID compraId,

        @NotNull(message = "El ID del inventario es obligatorio")
        UUID inventarioId,

        @NotNull(message = "La fecha de compra es obligatoria")
        LocalDateTime fechaCompra,

        @NotNull(message = "El precio de compra es obligatorio")
        @DecimalMin(value = "0.01", message = "El precio de compra debe ser mayor a 0.01")
        @Digits(integer = 12, fraction = 2, message = "Formato inválido para precio de compra")
        BigDecimal precioCompra,

        @NotNull
        @DecimalMin(value = "0.1", message = "La cantidad no puede ser negativo")
        @Digits(integer = 10, fraction = 2, message = "Formato inválido para cantidad actual")
        BigDecimal cantidad,

        @Size(max = 500, message = "Las observaciones no pueden exceder 500 caracteres")
        String observaciones

) {
}

