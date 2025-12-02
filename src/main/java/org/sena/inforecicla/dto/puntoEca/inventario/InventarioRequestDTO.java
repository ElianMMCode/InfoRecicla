package org.sena.inforecicla.dto.puntoEca.inventario;

import jakarta.validation.constraints.*;
import org.sena.inforecicla.model.enums.UnidadMedida;

import java.math.BigDecimal;
import java.util.UUID;

public record InventarioRequestDTO(

        @NotNull
        @DecimalMin(value = "0.01", message = "La capacidad máxima debe ser mayor a 0.01")
        @Digits(integer = 10, fraction = 2, message = "Formato inválido para capacidad máxima")
        BigDecimal capacidadMaxima,

        @NotNull
        UnidadMedida unidadMedida,

        @NotNull
        @DecimalMin(value = "0.0", message = "El stock actual no puede ser negativo")
        @Digits(integer = 10, fraction = 2, message = "Formato inválido para stock actual")
        BigDecimal stockActual,

        @NotNull
        @Min(value = 1, message = "El umbral de alerta debe ser mínimo 1")
        @Max(value = 100, message = "El umbral de alerta debe ser máximo 100")
        Short umbralAlerta,

        @NotNull
        @Min(value = 1, message = "El umbral crítico debe ser mínimo 1")
        @Max(value = 100, message = "El umbral crítico debe ser máximo 100")
        Short umbralCritico,

        @NotNull
        @DecimalMin(value = "0.01", message = "El precio de venta debe ser mayor a 0.01")
        @Digits(integer = 10, fraction = 2, message = "Formato inválido para precio de venta")
        BigDecimal precioVenta,

        @NotNull
        @DecimalMin(value = "0.01", message = "El precio de compra debe ser mayor a 0.01")
        @Digits(integer = 10, fraction = 2, message = "Formato inválido para precio de compra")
        BigDecimal precioCompra,

        @NotNull
        UUID materialId,

        @NotNull
        UUID puntoEcaId
) {

}

