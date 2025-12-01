package org.sena.inforecicla.dto.usuario;

import org.sena.inforecicla.model.PuntoECA;
import org.sena.inforecicla.model.Usuario;
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
        UUID localidadId,
        String localidad,
        String fotoPerfil,
        String biografia,

        // Campos adicionales del Punto ECA
        UUID puntoEcaId,
        String nombrePunto,
        String descripcionPunto,
        String fotoPunto,
        String logoPunto,
        String telefonoPunto,
        String celularPunto,
        String emailPunto,
        String direccionPunto,
        UUID localidadPuntoId,
        double latitud,
        double longitud,
        String logoUrlPunto,
        String sitioWebPunto,
        String horarioAtencionPunto) {

    public static UsuarioGestorResponseDTO derivado(Usuario u, PuntoECA p) {
        return new UsuarioGestorResponseDTO(
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
                u.getLocalidad() != null ? u.getLocalidad().getNombre() : "",
                u.getFotoPerfil(),
                u.getBiografia(),
                p.getPuntoEcaID(),
                p.getNombrePunto(),
                p.getDescripcion(),
                p.getFotoUrlPunto(),
                p.getLogoUrlPunto(),
                p.getTelefonoPunto(),
                p.getCelular(),
                p.getEmail(),
                p.getDireccion(),
                p.getLocalidad() != null ? p.getLocalidad().getLocalidadId() : null,
                p.getLatitud() != null ? p.getLatitud() : 0.0,
                p.getLongitud() != null ? p.getLongitud() : 0.0,
                p.getLogoUrlPunto(),
                p.getSitioWeb(),
                p.getHorarioAtencion()
        );
    }
}
