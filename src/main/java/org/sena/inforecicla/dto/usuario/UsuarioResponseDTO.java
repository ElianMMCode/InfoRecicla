package org.sena.inforecicla.dto.usuario;

import java.util.UUID;

public record UsuarioResponseDTO(
        UUID usuarioId,
        String nombres,
        String apellidos,
        String email,
        String celular,
        String tipoUsuario,
        String mensaje
) {
}

