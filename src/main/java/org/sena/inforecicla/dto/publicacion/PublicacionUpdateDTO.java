package org.sena.inforecicla.dto.publicacion;

import jakarta.validation.constraints.Size;
import lombok.AllArgsConstructor;
import lombok.Builder;
import lombok.Data;
import lombok.NoArgsConstructor;
import org.sena.inforecicla.model.enums.EstadoPublicacion;

import java.util.UUID;

@Data
@Builder
@NoArgsConstructor
@AllArgsConstructor
public class PublicacionUpdateDTO {

    @Size(min = 3, max = 200, message = "El título debe tener entre 3 y 200 caracteres")
    private String titulo;

    private String contenido;

    @Size(max = 30, message = "El nombre no puede exceder 30 caracteres")
    private String nombre;

    @Size(max = 500, message = "La descripción no puede exceder 500 caracteres")
    private String descripcion;

    private EstadoPublicacion estado;

    private UUID categoriaPublicacionId;
}