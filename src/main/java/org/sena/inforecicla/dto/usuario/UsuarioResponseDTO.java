package org.sena.inforecicla.dto.usuario;

import org.sena.inforecicla.model.Usuario;
import org.sena.inforecicla.model.enums.TipoDocumento;
import org.sena.inforecicla.model.enums.TipoUsuario;

import java.util.UUID;

public record UsuarioResponseDTO(UUID usuarioId,
                                 String nombres,
                                 String apellidos,
                                 TipoUsuario tipoUsuario,
                                 TipoDocumento tipoDocumento,
                                 String numeroDocumento,
                                 String fechaNacimiento,
                                 String celular,
                                 String email,
                                 String ciudad,
                                 UUID localidadId,
                                 String localidad
                                 ) {
    public static UsuarioResponseDTO derivado(Usuario u){
        return new UsuarioResponseDTO(
                u.getUsuarioId(),
                u.getNombres(),
                u.getApellidos(),
                u.getTipoUsuario(),
                u.getTipoDocumento(),
                u.getNumeroDocumento(),
                u.getFechaNacimiento(),
                u.getCelular(),
                u.getEmail(),
                u.getCiudad(),
                u.getLocalidad() != null ? u.getLocalidad().getLocalidadId() : null,
                u.getLocalidad() != null ? u.getLocalidad().getNombre() : ""
        );
    }

}
