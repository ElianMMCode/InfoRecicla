package org.sena.inforecicla.service;

import lombok.RequiredArgsConstructor;
import org.sena.inforecicla.dto.usuario.UsuarioGestorRequestDTO;
import org.sena.inforecicla.dto.usuario.UsuarioGestorResponseDTO;
import org.sena.inforecicla.model.PuntoECA;
import org.sena.inforecicla.model.Usuario;
import org.sena.inforecicla.model.enums.Estado;
import org.sena.inforecicla.model.enums.LocalidadBogota;
import org.sena.inforecicla.model.enums.TipoUsuario;
import org.sena.inforecicla.repository.UsuarioRepository;
import org.springframework.security.crypto.password.PasswordEncoder;
import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;
import org.sena.inforecicla.repository.PuntoECARepository;

import java.util.UUID;

@Service
@RequiredArgsConstructor
public class GestorEcaService {

    private final UsuarioRepository usuarioRepository;
    private final PuntoECARepository puntoECARepository;
    private final PasswordEncoder passwordEncoder;

    @Transactional
    public UsuarioGestorResponseDTO crearGestorConPuntoEca(UsuarioGestorRequestDTO dto) {

        // Crear el usuario gestor
        Usuario usuario = new Usuario();
        usuario.setNombres(dto.nombres());
        usuario.setApellidos(dto.apellidos());
        usuario.setEmail(dto.email());
        usuario.setCelular(dto.celular());
        usuario.setCiudad(dto.ciudad());
        usuario.setLocalidad(LocalidadBogota.valueOf(dto.localidad()));
        usuario.setTipoUsuario(TipoUsuario.GestorECA);
        usuario.setTipoDocumento(dto.tipoDocumento());
        usuario.setNumeroDocumento(dto.numeroDocumento());
        usuario.setFechaNacimiento(dto.fechaNacimiento());
        usuario.setBiografia(dto.biografia());
        usuario.setFotoPerfil(dto.fotoPerfil());
        usuario.setPassword(passwordEncoder.encode(dto.password()));

        Usuario usuarioGuardado = usuarioRepository.save(usuario);

        // Crear el punto ECA asociado
        PuntoECA puntoECA = new PuntoECA();
        puntoECA.setNombrePunto(dto.nombrePunto());
        puntoECA.setDescripcion(dto.descripcionPunto());
        puntoECA.setTelefonoPunto(dto.telefonoPunto());
        puntoECA.setDireccion(dto.direccionPunto());
        puntoECA.setCoordenadas(dto.coordenadasPunto());
        puntoECA.setLogoUrlPunto(dto.logoUrlPunto());
        puntoECA.setFotoUrlPunto(dto.fotoUrlPunto());
        puntoECA.setLocalidad(LocalidadBogota.valueOf(dto.localidadPunto()));
        puntoECA.setCelular(dto.celularPunto());
        puntoECA.setEmail(dto.emailPunto());
        puntoECA.setSitioWeb(dto.sitioWebPunto());
        puntoECA.setHorarioAtencion(dto.horarioAtencionPunto());
        puntoECA.setEstado(Estado.Activo);
        puntoECA.setUsuario(usuarioGuardado);
        puntoECA.setCiudad(dto.ciudadPunto());

        PuntoECA puntoGuardado = puntoECARepository.save(puntoECA);

        // Actualizar la referencia del punto ECA en el usuario
        usuarioGuardado.setPuntoECA(puntoGuardado);
        usuarioGuardado = usuarioRepository.save(usuarioGuardado);

        return construirUsuarioGestorResponseDTO(usuarioGuardado, puntoGuardado);
    }

    private UsuarioGestorResponseDTO construirUsuarioGestorResponseDTO(Usuario usuario, PuntoECA puntoECA) {
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
                // Campos del PuntoECA
                puntoECA.getPuntoEcaID(),
                puntoECA.getNombrePunto(),
                puntoECA.getDescripcion(),
                puntoECA.getFotoUrlPunto(),
                puntoECA.getLogoUrlPunto(),
                puntoECA.getTelefonoPunto(),
                puntoECA.getDireccion(),
                puntoECA.getLocalidad(),
                puntoECA.getCoordenadas(),
                puntoECA.getLogoUrlPunto());
    }

    public UsuarioGestorResponseDTO buscarGestorPuntoEca(UUID id) {
        Usuario usuario = usuarioRepository.findById(id)
                .orElseThrow(() -> new RuntimeException("Usuario no encontrado con ID: " + id));

        if (usuario.getTipoUsuario() != TipoUsuario.GestorECA) {
            throw new RuntimeException("El usuario no es un gestor ECA. Tipo actual: " + usuario.getTipoUsuario());
        }

        PuntoECA puntoECA = usuario.getPuntoECA();
        if (puntoECA == null) {
            throw new RuntimeException("El gestor no tiene un punto ECA asociado");
        }

        return construirUsuarioGestorResponseDTO(usuario, puntoECA);
    }

}
