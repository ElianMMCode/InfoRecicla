package org.sena.inforecicla.dto.usuario;

import org.sena.inforecicla.model.enums.LocalidadBogota;
import org.sena.inforecicla.model.enums.TipoDocumento;
import org.sena.inforecicla.model.enums.TipoUsuario;

import java.util.UUID;

public record UsuarioGestorResponseDTO(

        UUID usuarioId,
        String nombres,
        String apellidos,
        TipoUsuario tipoUsuario,
        TipoDocumento tipoDocumento,
        String numeroDocumento,
        String fechaNacimiento,
        String celular,
        String email,
        String ciudad,
        LocalidadBogota localidad,
        String fotoPerfil,
        String biografia,

        // Campos adicionales del Punto ECA
        UUID puntoEcaId,
        String nombrePunto,
        String descripcionPunto,
        String fotoPunto,
        String logoPunto,
        String telefonoPunto,
        String direccionPunto,
        LocalidadBogota localidadBogota,
        String coordenadasPunto,
        String logoUrlPunto) {
}
