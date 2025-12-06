package org.sena.inforecicla.dto.usuario;

import lombok.Builder;

import java.time.LocalDateTime;
import java.util.UUID;

import org.sena.inforecicla.model.Usuario;

@Builder
public record UsuarioResponseDTO(
        UUID usuarioId,
        String nombre,
        String email,
        String rol,
        String estado,
        LocalDateTime fechaCreacion,
        LocalDateTime fechaUltimoAcceso
) {
    public static UsuarioResponseDTO derivado(Usuario usuario) {
        if (usuario == null) return null;
        return new UsuarioResponseDTO(
                usuario.getUsuarioId(),
                usuario.getNombres() + " " + usuario.getApellidos(),
                usuario.getEmail(),
                usuario.getTipoUsuario() != null ? usuario.getTipoUsuario().name() : null,
                usuario.getEstado() != null ? usuario.getEstado().name() : null,
                usuario.getFechaCreacion(),
                usuario.getFechaUltimoAcceso() instanceof LocalDateTime ? (LocalDateTime) usuario.getFechaUltimoAcceso() : null
        );
    }
}
