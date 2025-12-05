package org.sena.inforecicla.service.impl;

import lombok.RequiredArgsConstructor;
import org.sena.inforecicla.dto.puntoEca.gestor.GestorResponseDTO;
import org.sena.inforecicla.dto.puntoEca.gestor.GestorUpdateDTO;
import org.sena.inforecicla.dto.puntoEca.gestor.PuntoEcaResponseDTO;
import org.sena.inforecicla.dto.puntoEca.gestor.PuntoEcaUpdateDTO;
import org.sena.inforecicla.dto.usuario.UsuarioGestorRequestDTO;
import org.sena.inforecicla.dto.usuario.UsuarioGestorResponseDTO;
import org.sena.inforecicla.model.PuntoECA;
import org.sena.inforecicla.model.Usuario;
import org.sena.inforecicla.model.enums.Estado;
import org.sena.inforecicla.model.enums.TipoUsuario;
import org.sena.inforecicla.repository.PuntoEcaRepository;
import org.sena.inforecicla.repository.UsuarioRepository;
import org.sena.inforecicla.service.GestorEcaService;
import org.sena.inforecicla.service.LocalidadService;
import org.springframework.security.crypto.password.PasswordEncoder;
import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;

import java.util.UUID;

@Service
@RequiredArgsConstructor
public class GestorEcaServiceImpl implements GestorEcaService {

    private final UsuarioRepository usuarioRepository;
    private final PuntoEcaRepository puntoECARepository;
    private final LocalidadService localidadService;
    private final PasswordEncoder passwordEncoder;

    @Override
    @Transactional
    public UsuarioGestorResponseDTO crearGestorConPuntoEca(UsuarioGestorRequestDTO dto) {
        Usuario usuario = new Usuario();
        usuario.setNombres(dto.nombres());
        usuario.setApellidos(dto.apellidos());
        usuario.setEmail(dto.email());
        usuario.setCelular(dto.celular());
        usuario.setCiudad(dto.ciudad());
        usuario.setLocalidad(localidadService.buscarPorNombre(dto.localidad())
                .orElseThrow(() -> new RuntimeException("Localidad no encontrada: " + dto.localidad())));
        usuario.setTipoUsuario(TipoUsuario.GestorECA);
        usuario.setTipoDocumento(dto.tipoDocumento());
        usuario.setNumeroDocumento(dto.numeroDocumento());
        usuario.setFechaNacimiento(dto.fechaNacimiento());
        usuario.setBiografia(dto.biografia());
        usuario.setFotoPerfil(dto.fotoPerfil());
        usuario.setPassword(passwordEncoder.encode(dto.password()));

        Usuario usuarioGuardado = usuarioRepository.save(usuario);

        PuntoECA puntoECA = new PuntoECA();
        puntoECA.setNombrePunto(dto.nombrePunto());
        puntoECA.setDescripcion(dto.descripcionPunto());
        puntoECA.setTelefonoPunto(dto.telefonoPunto());
        puntoECA.setDireccion(dto.direccionPunto());
        puntoECA.setLogoUrlPunto(dto.logoUrlPunto());
        puntoECA.setFotoUrlPunto(dto.fotoUrlPunto());
        puntoECA.setLocalidad(localidadService.buscarPorNombre(dto.localidadPunto())
                .orElseThrow(() -> new RuntimeException("Localidad no encontrada: " + dto.localidadPunto())));
        puntoECA.setCelular(dto.celularPunto());
        puntoECA.setEmail(dto.emailPunto());
        puntoECA.setSitioWeb(dto.sitioWebPunto());
        puntoECA.setHorarioAtencion(dto.horarioAtencionPunto());
        puntoECA.setEstado(Estado.Activo);
        puntoECA.setUsuario(usuarioGuardado);
        puntoECA.setCiudad(dto.ciudadPunto());

        PuntoECA puntoGuardado = puntoECARepository.save(puntoECA);

        usuarioGuardado.setPuntoECA(puntoGuardado);
        usuarioGuardado = usuarioRepository.save(usuarioGuardado);

        return construirUsuarioGestorResponseDTO(usuarioGuardado, puntoGuardado);
    }

    @Override
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

    @Override
    @Transactional
    public GestorResponseDTO actualizarGestor(UUID gestorId, GestorUpdateDTO gestorUpdate){
       Usuario gestor = usuarioRepository.findById(gestorId)
               .orElseThrow(() -> new RuntimeException("Usuario no encontrado con ID: " + gestorId));

       gestor.setNombres(gestorUpdate.nombres());
       gestor.setApellidos(gestorUpdate.apellidos());
       gestor.setTipoDocumento(gestorUpdate.tipoDocumento());
       if (gestorUpdate.numeroDocumento() != null && !gestorUpdate.numeroDocumento().trim().isEmpty()) {
           gestor.setNumeroDocumento(gestorUpdate.numeroDocumento());
       }
       if (gestorUpdate.fechaNacimiento() != null && !gestorUpdate.fechaNacimiento().trim().isEmpty()) {
           gestor.setFechaNacimiento(gestorUpdate.fechaNacimiento());
       }
       gestor.setCelular(gestorUpdate.celular());
       gestor.setEmail(gestorUpdate.email());
       if (gestorUpdate.biografia() != null && !gestorUpdate.biografia().trim().isEmpty()) {
           gestor.setBiografia(gestorUpdate.biografia());
       }

       Usuario actualizado = usuarioRepository.save(gestor);

       return GestorResponseDTO.derivado(actualizado);
    }

    @Override
    @Transactional
    public PuntoEcaResponseDTO actualizarPunto(UUID usuarioId, UUID puntoEcaId, PuntoEcaUpdateDTO puntoUpdate) {
        PuntoECA punto = puntoECARepository.findByPuntoEcaIDAndGestorId(puntoEcaId, usuarioId)
                .orElseThrow(()-> new RuntimeException("Punto eca no encontrado con ID: "+ puntoEcaId));

        punto.setNombrePunto(puntoUpdate.nombrePunto());

        if (puntoUpdate.descripcionPunto() != null && !puntoUpdate.descripcionPunto().trim().isEmpty()) {
            punto.setDescripcion(puntoUpdate.descripcionPunto());
        }

        punto.setCelular(puntoUpdate.celularPunto());
        punto.setEmail(puntoUpdate.emailPunto());
        punto.setDireccion(puntoUpdate.direccionPunto());

        punto.setLocalidad(localidadService.buscarPorId(puntoUpdate.localidadPuntoId())
                .orElseThrow(() -> new RuntimeException("Localidad no encontrada")));

        if (puntoUpdate.latitud() != null && puntoUpdate.latitud() != 0) {
            punto.setLatitud(puntoUpdate.latitud());
        }
        if (puntoUpdate.longitud() != null && puntoUpdate.longitud() != 0) {
            punto.setLongitud(puntoUpdate.longitud());
        }

        if (puntoUpdate.sitioWebPunto() != null && !puntoUpdate.sitioWebPunto().trim().isEmpty()) {
            punto.setSitioWeb(puntoUpdate.sitioWebPunto());
        }

        if (puntoUpdate.horarioAtencionPunto() != null && !puntoUpdate.horarioAtencionPunto().trim().isEmpty()) {
            punto.setHorarioAtencion(puntoUpdate.horarioAtencionPunto());
        }

        // Asegurar que el estado nunca sea null
        if (punto.getEstado() == null) {
            punto.setEstado(Estado.Activo);
        }

        PuntoECA actualizado = puntoECARepository.save(punto);
        return PuntoEcaResponseDTO.derivado(actualizado);
    }

    private UsuarioGestorResponseDTO construirUsuarioGestorResponseDTO(Usuario usuario, PuntoECA puntoECA) {
        return UsuarioGestorResponseDTO.derivado(usuario, puntoECA);
    }
}
