package org.sena.inforecicla.dto.puntoEca.inventario.movimientos;

import jakarta.validation.constraints.NotNull;
import java.util.UUID;

/**
 * DTO para eliminar una venta de inventario
 */
public record VentaInventarioDeleteDTO(

        @NotNull(message = "El venta ID es obligatorio")
        UUID ventaId,

        @NotNull(message = "El inventario ID es obligatorio")
        UUID inventarioId,

        @NotNull(message = "El punto ECA ID es obligatorio")
        UUID puntoId,

        @NotNull(message = "El material ID es obligatorio")
        UUID materialId

) {
}

