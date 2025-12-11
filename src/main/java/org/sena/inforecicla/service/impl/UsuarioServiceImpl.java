package org.sena.inforecicla.service.impl;

import org.sena.inforecicla.dto.usuario.*;
import org.sena.inforecicla.model.Localidad;
import org.sena.inforecicla.model.PuntoECA;
import org.sena.inforecicla.model.Usuario;
import org.sena.inforecicla.model.enums.Estado;
import org.sena.inforecicla.model.enums.TipoUsuario;
import org.sena.inforecicla.repository.LocalidadRepository;
import org.sena.inforecicla.repository.PuntoEcaRepository;
import org.sena.inforecicla.repository.UsuarioRepository;
import org.sena.inforecicla.service.UsuarioService;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.security.crypto.password.PasswordEncoder;
import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;

import java.util.UUID;

@Service
public class UsuarioServiceImpl implements UsuarioService {

    @Autowired
    private UsuarioRepository usuarioRepository;

    @Autowired
    private LocalidadRepository localidadRepository;

    @Autowired
    private PuntoEcaRepository puntoEcaRepository;

    @Autowired
    private PasswordEncoder passwordEncoder;

    @Override
    public UsuarioGestorResponseDTO crearUsuarioGestorEca(UsuarioRequestDTO dto) {
        try {
            // Validaciones previas
            validarDatosUnicos(dto.email(), dto.celular(), null);

            // Buscar la localidad
            Localidad localidad = localidadRepository.findByNombreIgnoreCase(dto.localidad())
                    .orElseThrow(() -> new RuntimeException("Localidad '" + dto.localidad() + "' no encontrada"));

            // Crear el usuario (gestor del ECA)
            Usuario usuario = new Usuario();
            usuario.setNombres(dto.nombres());
            usuario.setApellidos(dto.apellidos());
            usuario.setEmail(dto.email());
            usuario.setCelular(dto.celular());
            usuario.setPassword(passwordEncoder.encode(dto.password()));
            usuario.setTipoUsuario(TipoUsuario.GestorECA);
            usuario.setTipoDocumento(dto.tipoDocumento());
            usuario.setNumeroDocumento(dto.numeroDocumento());
            usuario.setFechaNacimiento(dto.fechaNacimiento());
            usuario.setCiudad(dto.ciudad());
            usuario.setLocalidad(localidad);
            usuario.setFotoPerfil(dto.fotoPerfil());
            usuario.setEstado(Estado.Activo);
            usuario.setActivo(true);

            Usuario usuarioGuardado = usuarioRepository.save(usuario);

            return UsuarioGestorResponseDTO.derivado(usuarioGuardado, null);
        } catch (Exception e) {
            throw new RuntimeException("Error al crear gestor ECA: " + e.getMessage(), e);
        }
    }

    @Override
    public UsuarioGestorResponseDTO buscarPorId(UUID id) {
        try {
            Usuario usuario = usuarioRepository.findById(id)
                    .orElseThrow(() -> new RuntimeException("Usuario no encontrado con ID: " + id));

            // Si tiene punto ECA asociado, obtenerlo
            PuntoECA puntoEca = puntoEcaRepository.findByUsuario_UsuarioId(id).orElse(null);

            return UsuarioGestorResponseDTO.derivado(usuario, puntoEca);
        } catch (Exception e) {
            throw new RuntimeException("Error al buscar usuario: " + e.getMessage(), e);
        }
    }

    @Override
    @Transactional
    public UsuarioResponseDTO registrarCiudadano(RegistroCiudadanoDTO dto) {
        try {
            // Validaciones previas
            validarDatosUnicos(dto.email(), dto.celular(), dto.numeroDocumento());

            // Validar que las contraseñas coincidan
            if (!dto.password().equals(dto.passwordConfirm())) {
                throw new RuntimeException("Las contraseñas no coinciden");
            }

            // Buscar la localidad
            Localidad localidad = localidadRepository.findByNombreIgnoreCase(dto.localidad())
                    .orElseThrow(() -> new RuntimeException("Localidad '" + dto.localidad() + "' no encontrada"));

            // Crear el usuario
            Usuario usuario = new Usuario();
            usuario.setNombres(dto.nombres());
            usuario.setApellidos(dto.apellidos());
            usuario.setEmail(dto.email());
            usuario.setCelular(dto.celular());
            usuario.setPassword(passwordEncoder.encode(dto.password()));
            usuario.setTipoUsuario(TipoUsuario.Ciudadano);
            usuario.setTipoDocumento(dto.tipoDocumento());
            usuario.setNumeroDocumento(dto.numeroDocumento());
            usuario.setFechaNacimiento(dto.fechaNacimiento());
            usuario.setCiudad(dto.ciudad());
            usuario.setLocalidad(localidad);
            usuario.setEstado(Estado.Activo);
            usuario.setActivo(true);

            Usuario usuarioGuardado = usuarioRepository.save(usuario);

            return UsuarioResponseDTO.derivado(usuarioGuardado);
        } catch (Exception e) {
            throw new RuntimeException("Error al registrar ciudadano: " + e.getMessage(), e);
        }
    }

    @Override
    @Transactional
    public UsuarioResponseDTO registrarPuntoECA(RegistroPuntoEcaDTO dto) {
        try {
            // Validaciones previas
            validarDatosUnicos(dto.email(), dto.celular(), dto.numeroDocumento());

            // Validar que las contraseñas coincidan
            if (!dto.password().equals(dto.passwordConfirm())) {
                throw new RuntimeException("Las contraseñas no coinciden");
            }

            // Buscar la localidad
            Localidad localidad = localidadRepository.findByNombreIgnoreCase(dto.localidad())
                    .orElseThrow(() -> new RuntimeException("Localidad '" + dto.localidad() + "' no encontrada"));

            // Crear el usuario (gestor del ECA)
            Usuario usuario = new Usuario();
            usuario.setNombres(dto.nombres());
            usuario.setApellidos(dto.apellidos());
            usuario.setEmail(dto.email());
            usuario.setCelular(dto.celular());
            usuario.setPassword(passwordEncoder.encode(dto.password()));
            usuario.setTipoUsuario(TipoUsuario.GestorECA);
            usuario.setTipoDocumento(dto.tipoDocumento());
            usuario.setNumeroDocumento(dto.numeroDocumento());
            usuario.setCiudad(dto.ciudad());
            usuario.setLocalidad(localidad);
            usuario.setLatitud(dto.latitud());
            usuario.setLongitud(dto.longitud());
            usuario.setBiografia(dto.descripcion());
            usuario.setEstado(Estado.Activo);
            usuario.setActivo(true);

            Usuario usuarioGuardado = usuarioRepository.save(usuario);

            // Crear el Punto ECA asociado
            PuntoECA puntoEca = new PuntoECA();
            puntoEca.setNombrePunto(dto.nombres());
            puntoEca.setDescripcion(dto.descripcion());
            puntoEca.setUsuario(usuarioGuardado);

            // Campos heredados de EntidadContacto (obligatorios)
            puntoEca.setEmail(dto.email());
            puntoEca.setCelular(dto.celular());

            // Campos heredados de EntidadLocalizacion
            puntoEca.setLocalidad(localidad);
            puntoEca.setCiudad(dto.ciudad());
            puntoEca.setLatitud(dto.latitud());
            puntoEca.setLongitud(dto.longitud());

            // Campos específicos del PuntoECA
            puntoEca.setDireccion(dto.direccion());
            puntoEca.setEstado(Estado.Activo);

            puntoEcaRepository.save(puntoEca);

            return UsuarioResponseDTO.derivado(usuarioGuardado);
        } catch (Exception e) {
            throw new RuntimeException("Error al registrar Punto ECA: " + e.getMessage(), e);
        }
    }

    private void validarDatosUnicos(String email, String celular, String numeroDocumento) {
        // Validar que el email no exista
        if (usuarioRepository.findByEmail(email).isPresent()) {
            throw new RuntimeException("El email ya está registrado");
        }

        // Validar que el celular no exista
        if (usuarioRepository.findByCelular(celular).isPresent()) {
            throw new RuntimeException("El celular ya está registrado");
        }

        // Validar que el número de documento no exista (si se proporciona)
        if (numeroDocumento != null && !numeroDocumento.trim().isEmpty()) {
            if (usuarioRepository.findByNumeroDocumento(numeroDocumento).isPresent()) {
                throw new RuntimeException("El número de documento ya está registrado");
            }
        }
    }
}

