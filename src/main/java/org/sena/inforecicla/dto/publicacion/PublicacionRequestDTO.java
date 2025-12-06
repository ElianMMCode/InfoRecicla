package org.sena.inforecicla.dto.publicacion;

import jakarta.validation.constraints.NotBlank;
import jakarta.validation.constraints.NotNull;
import jakarta.validation.constraints.Size;
import lombok.AllArgsConstructor;
import lombok.Builder;
import lombok.Data;
import lombok.NoArgsConstructor;

import org.sena.inforecicla.model.Publicacion;
import org.sena.inforecicla.model.enums.EstadoPublicacion;

import java.util.List;
import java.util.UUID;

@Data
@Builder
@NoArgsConstructor
@AllArgsConstructor
public class PublicacionRequestDTO {

    @NotBlank(message = "El título es obligatorio")
    @Size(min = 3, max = 200, message = "El título debe tener entre 3 y 200 caracteres")
    private String titulo;

    @NotBlank(message = "El contenido es obligatorio")
    private String contenido;

    @NotNull(message = "El usuario es obligatorio")
    private UUID usuarioId;

    @NotNull(message = "La categoría es obligatoria")
    private UUID categoriaPublicacionId;

    private List<UUID> etiquetasIds;

    private String estado;

    // Convierte a entidad (solo campos directos; relaciones deben ser resueltas en el service)
    public Publicacion toEntity() {
        Publicacion p = new Publicacion();
        p.setTitulo(this.titulo);
        p.setContenido(this.contenido);
        if (this.estado != null) {
            p.setEstadoPublicacion(EstadoPublicacion.valueOf(this.estado.toUpperCase()));
        } else {
            p.setEstadoPublicacion(EstadoPublicacion.BORRADOR);
        }
        return p;
    }
}