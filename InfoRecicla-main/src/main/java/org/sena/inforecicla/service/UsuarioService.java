package org.sena.inforecicla.service;

import lombok.RequiredArgsConstructor;
import org.sena.inforecicla.dto.usuario.UsuarioGestorResponseDTO;
import org.sena.inforecicla.dto.usuario.UsuarioRequestDTO;
import org.sena.inforecicla.model.Usuario;
import org.sena.inforecicla.model.enums.LocalidadBogota;
import org.sena.inforecicla.repository.UsuarioRepository;
import org.springframework.security.crypto.password.PasswordEncoder;
import org.springframework.stereotype.Service;

import java.util.UUID;

@Service
@RequiredArgsConstructor
public class UsuarioService {

    private final UsuarioRepository usuarioRepository;
    private final PasswordEncoder passwordEncoder;

    public UsuarioGestorResponseDTO crearUsuarioGestorEca(UsuarioRequestDTO dto) {

        Usuario usuario = new Usuario();
        usuario.setNombres(dto.nombres());
        usuario.setApellidos(dto.apellidos());
        usuario.setEmail(dto.email());
        usuario.setCelular(dto.celular());
        usuario.setCiudad(dto.ciudad());
        usuario.setLocalidad(LocalidadBogota.valueOf(dto.localidad()));
        usuario.setTipoUsuario(dto.tipoUsuario());
        usuario.setTipoDocumento(dto.tipoDocumento());
        usuario.setNumeroDocumento(dto.numeroDocumento());
        usuario.setFechaNacimiento(dto.fechaNacimiento());
        usuario.setBiografia(dto.biografia());
        usuario.setFotoPerfil(dto.fotoPerfil());


        usuario.setPassword(passwordEncoder.encode(dto.password()));
        Usuario guardado = usuarioRepository.save(usuario);


        // Construir el ResponseDTO
        return new UsuarioGestorResponseDTO(
                guardado.getUsuarioId(),
                guardado.getNombres(),
                guardado.getApellidos(),
                guardado.getTipoUsuario(),
                guardado.getTipoDocumento(),
                guardado.getNumeroDocumento(),
                guardado.getFechaNacimiento(),
                guardado.getCelular(),
                guardado.getEmail(),
                guardado.getCiudad(),
                guardado.getLocalidad(),
                guardado.getFotoPerfil(),
                guardado.getBiografia(),
                guardado.getPuntoECA() != null ? guardado.getPuntoECA().getPuntoEcaID() : null,
                // Campos del PuntoECA
                guardado.getPuntoECA() != null ? guardado.getPuntoECA().getNombrePunto() : null,
                guardado.getPuntoECA() != null ? guardado.getPuntoECA().getDescripcion() : null,
                guardado.getPuntoECA() != null ? guardado.getPuntoECA().getFotoUrlPunto() : null,
                guardado.getPuntoECA() != null ? guardado.getPuntoECA().getLogoUrlPunto() : null,
                guardado.getPuntoECA() != null ? guardado.getPuntoECA().getTelefonoPunto() : null,
                guardado.getPuntoECA() != null ? guardado.getPuntoECA().getDireccion() : null,
                guardado.getPuntoECA() != null ? guardado.getPuntoECA().getLocalidad() : null,
                guardado.getPuntoECA() != null ? guardado.getPuntoECA().getCoordenadas() : null,
                guardado.getPuntoECA() != null ? guardado.getPuntoECA().getLogoUrlPunto() : null);

    }

    public UsuarioGestorResponseDTO buscarPorId(UUID id) {
        Usuario usuario = usuarioRepository.findById(id)
                .orElseThrow(() -> new RuntimeException("Usuario no encontrado"));

        // Obtener datos del PuntoECA si existe
        String nombrePunto = null;
        String direccion = null;
        String coordenadas = null;

        if (usuario.getPuntoECA() != null) {
            nombrePunto = usuario.getPuntoECA().getNombrePunto();
            direccion = usuario.getPuntoECA().getDireccion();
            coordenadas = usuario.getPuntoECA().getCoordenadas();
        }

        return new UsuarioGestorResponseDTO(
                usuario.getUsuarioId(),
                usuario.getNombres(),
                usuario.getApellidos(),
                usuario.getTipoUsuario(),
                usuario.getTipoDocumento(),
                usuario.getNumeroDocumento(),
                usuario.getFechaNacimiento(),
                usuario.getCelular(),
                usuario.getEmail(),
                usuario.getCiudad(),
                usuario.getLocalidad(),
                usuario.getFotoPerfil(),
                usuario.getBiografia(),
                usuario.getPuntoECA() != null ? usuario.getPuntoECA().getPuntoEcaID() : null,
                nombrePunto,
                usuario.getPuntoECA() != null ? usuario.getPuntoECA().getDescripcion() : null,
                usuario.getPuntoECA() != null ? usuario.getPuntoECA().getFotoUrlPunto() : null,
                usuario.getPuntoECA() != null ? usuario.getPuntoECA().getLogoUrlPunto() : null,
                usuario.getPuntoECA() != null ? usuario.getPuntoECA().getTelefonoPunto() : null,
                direccion,
                usuario.getPuntoECA() != null ? usuario.getPuntoECA().getLocalidad() : null,
                coordenadas,
                usuario.getPuntoECA() != null ? usuario.getPuntoECA().getLogoUrlPunto() : null
        );
    }

}
