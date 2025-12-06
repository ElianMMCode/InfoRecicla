package org.sena.inforecicla.dto.publicacion;

import lombok.Builder;
import org.sena.inforecicla.model.Publicacion;

import java.time.LocalDateTime;
import java.util.UUID;

@Builder
public record PublicacionResponseDTO(
        UUID publicacionId,
        String titulo,
        String contenido,
        String estado,
        String usuarioNombre,
        String usuarioEmail,
        String categoriaNombre,
        LocalDateTime fechaCreacion,
        LocalDateTime fechaActualizacion
) {

    public static PublicacionResponseDTO derivado(Publicacion p) {
        return new PublicacionResponseDTO(
                p.getPublicacionId(),
                p.getTitulo(),
                p.getContenido(),
                p.getEstado().name(),
                p.getUsuario() != null ? p.getUsuario().getNombres() + " " + p.getUsuario().getApellidos() : null,
                p.getUsuario() != null ? p.getUsuario().getEmail() : null,
                p.getCategoriaPublicacion() != null ? p.getCategoriaPublicacion().getNombre() : null,
                p.getFechaCreacion(),
                p.getFechaActualizacion()
        );
    }
}