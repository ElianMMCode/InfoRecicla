package org.sena.inforecicla.dto.puntoEca.materiales;

import org.sena.inforecicla.model.Inventario;
import org.sena.inforecicla.model.Material;
import org.sena.inforecicla.model.enums.UnidadMedida;

import java.math.BigDecimal;
import java.util.UUID;

public record MaterialInvResponseDTO(
    //Material
    UUID materialId,
    String nmbMaterial,
    String dscMaterial,
    String imagenUrl,
    //Inventario
    UUID inventarioId,
    UnidadMedida unidadMedida,
    BigDecimal stockActual,
    BigDecimal capacidadMaxima,
    BigDecimal precioCompra,
    BigDecimal precioVenta,
    //Categoria Material
    String nmbCategoria,
    //Tipo Material
    String nmbTipo

) {

    public static MaterialInvResponseDTO derivado(Material m, Inventario i) {
        return new MaterialInvResponseDTO(
            m.getMaterialId(),
            m.getNombre(),
            m.getDescripcion(),
            m.getImagenUrl(),
            i.getInventarioId(),
            i.getUnidadMedida(),
            i.getStockActual(),
            i.getCapacidadMaxima(),
            i.getPrecioCompra(),
            i.getPrecioVenta(),
            m.getCtgMaterial().getNombre(),
            m.getTipoMaterial().getNombre()
        );
    }
}
