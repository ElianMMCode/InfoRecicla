package org.sena.inforecicla.dto.puntoEca.gestor;

import org.sena.inforecicla.model.Usuario;
import org.sena.inforecicla.model.enums.LocalidadBogota;
import org.sena.inforecicla.model.enums.TipoDocumento;
import org.sena.inforecicla.model.enums.TipoUsuario;

import java.util.UUID;

public record GestorResponseDTO(

        UUID usuarioId,
        String nombres,
        String apellidos,
        TipoDocumento tipoDocumento,
        String numeroDocumento,
        String fechaNacimiento,
        String celular,
        String email,
        LocalidadBogota localidad,
        String fotoPerfil,
        String biografia
){
    public static GestorResponseDTO derivado(Usuario u){
        return new GestorResponseDTO(
                u.getUsuarioId(),
                u.getNombres(),
                u.getApellidos(),
                u.getTipoDocumento(),
                u.getNumeroDocumento(),
                u.getFechaNacimiento(),
                u.getCelular(),
                u.getEmail(),
                u.getLocalidad(),
                u.getFotoPerfil(),
                u.getBiografia()
        );
    }

        // Campos adicionales del Punto ECA

}
