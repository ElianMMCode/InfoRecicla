package org.sena.inforecicla.dto.puntoEca.materiales;

import org.sena.inforecicla.model.TipoMaterial;

public record TipoMaterialesInvResponseDTO (
    //Categoria Material
    String nmbCategoria,
    String dscCategoria

){
    public static TipoMaterialesInvResponseDTO derivado(TipoMaterial tip){
        return new TipoMaterialesInvResponseDTO(
                tip.getNombre(),
                tip.getDescripcion()
        );
    }
}
