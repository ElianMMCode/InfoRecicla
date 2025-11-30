package org.sena.inforecicla.dto.puntoEca.inventario;

import org.sena.inforecicla.model.Inventario;
import org.sena.inforecicla.model.enums.Alerta;
import org.sena.inforecicla.model.enums.UnidadMedida;

import java.math.BigDecimal;
import java.time.LocalDateTime;
import java.util.UUID;

public record InventarioResponseDTO(

        //Datos del inventario
        UUID inventarioId,
        BigDecimal capacidadMaxima,
        UnidadMedida unidadMedida,
        BigDecimal stockActual,
        Short umbralAlerta,
        Short umbralCritico,
        Alerta alerta,
        BigDecimal precioCompra,
        BigDecimal precioVenta,
        //Datos material
        UUID materialId,
        String nombreMaterial,
        //Datos puntoEca
        UUID puntoEcaId,
        String nombrePuntoEca,
        LocalDateTime fechaCreacion,
        LocalDateTime fechaActualizacion
) {

    public static InventarioResponseDTO derivado(Inventario i){

        return new InventarioResponseDTO(
                i.getInventarioId(),
                i.getCapacidadMaxima(),
                i.getUnidadMedida(),
                i.getStockActual(),
                i.getUmbralAlerta(),
                i.getUmbralCritico(),
                i.getAlerta(),
                i.getPrecioCompra(),
                i.getPrecioVenta(),
                i.getMaterial().getMaterialId(),
                i.getMaterial().getNombre(),
                i.getPuntoEca().getPuntoEcaID(),
                i.getPuntoEca().getNombrePunto(),
                i.getFechaCreacion(),
                i.getFechaActualizacion()
        );

    }
}
