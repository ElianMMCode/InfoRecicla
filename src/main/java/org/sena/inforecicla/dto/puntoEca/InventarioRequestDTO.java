package org.sena.inforecicla.dto.puntoEca;

import jakarta.validation.constraints.*;
import org.sena.inforecicla.model.Material;
import org.sena.inforecicla.model.PuntoECA;
import org.sena.inforecicla.model.enums.UnidadMedida;

import java.math.BigDecimal;
import java.util.UUID;

public record InventarioRequestDTO(
        @NotNull
        UUID inventarioId,

        @NotNull
        @DecimalMin(value = "0.001", message = "La capacidad máxima debe ser mayor a 0")
        @Digits(integer = 11, fraction = 3, message = "Formato inválido para capacidad máxima")
        BigDecimal capacidadMaxima,

        @NotNull
        UnidadMedida unidadMedida,

        @NotNull
        @DecimalMin(value = "0.0", message = "El stock actual no puede ser negativo")
        @Digits(integer = 11, fraction = 3, message = "Formato inválido para stock actual")
        BigDecimal stockActual,

        @NotNull
        @Min(value = 1, message = "El umbral de alerta debe ser mínimo 1")
        @Max(value = 999, message = "El umbral de alerta debe ser máximo 999")
        Short umbralAlerta,

        @NotNull
        @Min(value = 1, message = "El umbral crítico debe ser mínimo 1")
        @Max(value = 999, message = "El umbral crítico debe ser máximo 999")
        Short umbralCritico,

        @NotNull
        @DecimalMin(value = "0.001", message = "El precio de venta debe ser mayor a 0")
        @Digits(integer = 11, fraction = 2, message = "Formato inválido para precio de venta")
        BigDecimal precioVenta,

        @NotNull
        Material material,

        @NotNull
        PuntoECA puntoECA
) {
}

