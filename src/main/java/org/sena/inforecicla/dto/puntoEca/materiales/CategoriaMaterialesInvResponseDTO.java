package org.sena.inforecicla.dto.puntoEca.materiales;

import org.sena.inforecicla.model.CategoriaMaterial;

public record CategoriaMaterialesInvResponseDTO (
    //Categoria Material
    String nmbCategoria,
    String dscCategoria
){

    public static CategoriaMaterialesInvResponseDTO derivado(CategoriaMaterial cat){
        return new CategoriaMaterialesInvResponseDTO(
                cat.getNombre(),
                cat.getDescripcion()
        );
    }
}
