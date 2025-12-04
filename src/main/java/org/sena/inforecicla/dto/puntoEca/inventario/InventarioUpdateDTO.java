package org.sena.inforecicla.dto.puntoEca.inventario;

import jakarta.validation.constraints.*;
import org.sena.inforecicla.model.enums.UnidadMedida;

import java.math.BigDecimal;

public record InventarioUpdateDTO(

        @NotNull
        @DecimalMin(value = "0.0", message = "El stock actual no puede ser negativo")
        @Digits(integer = 11, fraction = 3, message = "Formato inválido para stock actual")
        BigDecimal stockActual,

        @NotNull
        @DecimalMin(value = "0.001", message = "La capacidad máxima debe ser mayor a 0")
        @Digits(integer = 11, fraction = 3, message = "Formato inválido para capacidad máxima")
        BigDecimal capacidadMaxima,

        @NotNull
        UnidadMedida unidadMedida,

        @NotNull
        @DecimalMin(value = "0.001", message = "El precio de compra debe ser mayor a 0")
        @Digits(integer = 11, fraction = 2, message = "Formato inválido para precio de compra")
        BigDecimal precioCompra,

        @NotNull
        @DecimalMin(value = "0.001", message = "El precio de venta debe ser mayor a 0")
        @Digits(integer = 11, fraction = 2, message = "Formato inválido para precio de venta")
        BigDecimal precioVenta,

        @NotNull
        @Min(value = 1, message = "El umbral de alerta debe ser mínimo 1")
        @Max(value = 999, message = "El umbral de alerta debe ser máximo 999")
        Short umbralAlerta,

        @NotNull
        @Min(value = 1, message = "El umbral crítico debe ser mínimo 1")
        @Max(value = 999, message = "El umbral crítico debe ser máximo 999")
        Short umbralCritico
) {
}
