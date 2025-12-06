package org.sena.inforecicla.dto.categoriaPublicacion;

import lombok.Builder;
import org.sena.inforecicla.model.CategoriaPublicacion;

@Builder
public record CategoriaPublicacionResponseDTO(
        String nombre,
        String descripcion
) {
    public static CategoriaPublicacionResponseDTO derivado(CategoriaPublicacion catPublicacion){
        return new CategoriaPublicacionResponseDTO(
                catPublicacion.getNombre(),
                catPublicacion.getDescripcion()
        );
    }
}