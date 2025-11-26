package org.sena.inforecicla.dto.puntoEca;

import org.sena.inforecicla.model.enums.UnidadMedida;

import java.math.BigDecimal;
import java.util.UUID;

public record InventarioResponseDTO(

        //Datos del inventario
        BigDecimal capacidadMaxima,
        UnidadMedida unidadMedida,
        BigDecimal stockActual,
        Short umbralAlerta,
        Short umbralCritico,
        BigDecimal precioCompra,
        BigDecimal precioVenta,
        //Datos material
        UUID materialId,
        String nombreMaterial,
        //Datos puntoEca
        UUID puntoEcaId,
        String nombrePuntoEca

) {
}
