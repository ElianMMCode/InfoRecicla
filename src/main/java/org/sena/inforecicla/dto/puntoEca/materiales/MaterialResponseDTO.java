package org.sena.inforecicla.dto.puntoEca.materiales;

import org.sena.inforecicla.model.Material;

import java.util.UUID;

public record MaterialResponseDTO(
    //Material
    UUID materialId,
    String nmbMaterial,
    String dscMaterial,
    String imagenUrl,
    //Categoria Material
    String nmbCategoria,
    String dscCategoria,
    //Tipo Material
    String nmbTipo,
    String dscTipo

) {

    public static MaterialResponseDTO derivado(Material m) {
        return new MaterialResponseDTO(
            m.getMaterialId(),
            m.getNombre(),
            m.getDescripcion(),
            m.getImagenUrl(),
            m.getCtgMaterial().getNombre(),
            m.getCtgMaterial().getDescripcion(),
            m.getTipoMaterial().getNombre(),
            m.getTipoMaterial().getDescripcion()
        );
    }
}
