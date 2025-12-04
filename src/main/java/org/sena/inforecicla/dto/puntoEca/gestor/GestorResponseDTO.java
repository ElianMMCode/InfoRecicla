package org.sena.inforecicla.dto.puntoEca.gestor;

import org.sena.inforecicla.model.Usuario;
import org.sena.inforecicla.model.enums.TipoDocumento;

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
        UUID localidadId,
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
                u.getLocalidad() != null ? u.getLocalidad().getLocalidadId() : null,
                u.getFotoPerfil(),
                u.getBiografia()
        );
    }

        // Campos adicionales del Punto ECA

}
