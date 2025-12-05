package org.sena.inforecicla.dto.publicacion;

import lombok.AllArgsConstructor;
import lombok.Builder;
import lombok.Data;
import lombok.NoArgsConstructor;
import org.sena.inforecicla.model.enums.EstadoPublicacion;

import java.time.LocalDateTime;
import java.util.UUID;

@Data
@Builder
@NoArgsConstructor
@AllArgsConstructor
public class PublicacionResponseDTO {

    private UUID publicacionId;
    private String titulo;
    private String contenido;
    private String nombre;
    private String descripcion;
    private EstadoPublicacion estado;
    private UUID usuarioId;
    private String nombreUsuario;
    private UUID categoriaPublicacionId;
    private String nombreCategoria;
    private LocalDateTime creado;
    private LocalDateTime actualizado;
}