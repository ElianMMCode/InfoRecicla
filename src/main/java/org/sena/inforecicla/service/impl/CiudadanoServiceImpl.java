package org.sena.inforecicla.service.impl;

import lombok.RequiredArgsConstructor;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;
import org.sena.inforecicla.dto.usuario.UsuarioResponseDTO;
import org.sena.inforecicla.model.Usuario;
import org.sena.inforecicla.repository.UsuarioRepository;
import org.sena.inforecicla.service.CiudadanoService;
import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;

import java.util.UUID;

@Service
@RequiredArgsConstructor
public class CiudadanoServiceImpl implements CiudadanoService {

    private static final Logger logger = LoggerFactory.getLogger(CiudadanoServiceImpl.class);

    private final UsuarioRepository usuarioRepository;

    @Override
    @Transactional(readOnly = true)
    public UsuarioResponseDTO getCiudadano(UUID id) {
        logger.info("Buscando ciudadano con ID: {}", id);

        Usuario usuario = usuarioRepository.findById(id)
                .orElseThrow(() -> {
                    logger.error("Ciudadano no encontrado con ID: {}", id);
                    return new RuntimeException("Ciudadano no encontrado con ID: " + id);
                });

        logger.info("Ciudadano encontrado: {} {}", usuario.getNombres(), usuario.getApellidos());
        return UsuarioResponseDTO.derivado(usuario);
    }

    @Override
    @Transactional
    public UsuarioResponseDTO actualizarCiudadano(UUID ciudadanoId, UsuarioResponseDTO dto) {
        logger.info("Actualizando información del ciudadano con ID: {}", ciudadanoId);

        Usuario usuario = usuarioRepository.findById(ciudadanoId)
                .orElseThrow(() -> {
                    logger.error("Ciudadano no encontrado con ID: {}", ciudadanoId);
                    return new RuntimeException("Ciudadano no encontrado con ID: " + ciudadanoId);
                });

        // Actualizar campos del usuario con los datos del DTO
        usuario.setNombres(dto.nombres());
        usuario.setApellidos(dto.apellidos());
        usuario.setEmail(dto.email());
        usuario.setCelular(dto.celular());
        usuario.setCiudad(dto.ciudad());
        usuario.setTipoDocumento(dto.tipoDocumento());
        usuario.setNumeroDocumento(dto.numeroDocumento());
        usuario.setFechaNacimiento(dto.fechaNacimiento());

        Usuario usuarioActualizado = usuarioRepository.save(usuario);
        logger.info("Ciudadano actualizado correctamente con ID: {}", ciudadanoId);

        return UsuarioResponseDTO.derivado(usuarioActualizado);
    }
}

