package org.sena.inforecicla.dto.categoriaPublicacion;

import jakarta.persistence.Column;
import jakarta.validation.constraints.NotBlank;
import jakarta.validation.constraints.Size;
import lombok.AllArgsConstructor;
import lombok.Builder;
import lombok.Data;
import lombok.NoArgsConstructor;

import org.sena.inforecicla.model.CategoriaPublicacion;

@Data
@Builder
@NoArgsConstructor
@AllArgsConstructor
public class CategoriaPublicacionRequestDTO {

    @NotBlank(message = "El nombre de la categoría es obligatorio")
    @Size(min = 2, max = 100, message = "El nombre debe tener entre 2 y 100 caracteres")
    private String nombre;

    @Size(max = 500, message = "La descripción no puede exceder los 500 caracteres")
    private String descripcion;

    private String estado;

    public CategoriaPublicacion toEntity() {
        CategoriaPublicacion c = new CategoriaPublicacion();
        c.setNombre(this.nombre);
        c.setDescripcion(this.descripcion);
        return c;
    }
}