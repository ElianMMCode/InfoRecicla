package org.sena.inforecicla.dto.publicacion;

import jakarta.validation.constraints.NotBlank;
import jakarta.validation.constraints.NotNull;
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
public class PublicacionRequestDTO {

    @NotBlank(message = "El título no puede estar vacío")
    @Size(min = 3, max = 200, message = "El título debe tener entre 3 y 200 caracteres")
    private String titulo;

    @NotBlank(message = "El contenido no puede estar vacío")
    private String contenido;

    @NotBlank(message = "El nombre no puede estar vacío")
    @Size(max = 30, message = "El nombre no puede exceder 30 caracteres")
    private String nombre;

    @NotBlank(message = "La descripción no puede estar vacía")
    @Size(max = 500, message = "La descripción no puede exceder 500 caracteres")
    private String descripcion;

    @NotNull(message = "El estado es obligatorio")
    private EstadoPublicacion estado;

    @NotNull(message = "El usuario es obligatorio")
    private UUID usuarioId;

    @NotNull(message = "La categoría es obligatoria")
    private UUID categoriaPublicacionId;
}