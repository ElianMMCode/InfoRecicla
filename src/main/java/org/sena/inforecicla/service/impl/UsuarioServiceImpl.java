package org.sena.inforecicla.service.impl;

import lombok.RequiredArgsConstructor;
import org.sena.inforecicla.dto.usuario.UsuarioResponseDTO;
import org.sena.inforecicla.exception.UsuarioNotFoundException;
import org.sena.inforecicla.repository.UsuarioRepository;
import org.sena.inforecicla.service.UsuarioService;
import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;

import java.util.UUID;

@Service
@RequiredArgsConstructor
@Transactional(readOnly = true)
public abstract class UsuarioServiceImpl implements UsuarioService {

    private final UsuarioRepository usuarioRepository;

    @Override
    public UsuarioResponseDTO buscarUsuarioPorId(UUID usuarioId) {
        return usuarioRepository.findById(usuarioId)
                .map(org.sena.inforecicla.dto.usuario.UsuarioResponseDTO::derivado)
                .orElseThrow(() -> new UsuarioNotFoundException("Usuario no encontrado con ID: " + usuarioId));
    }

    @Override
    public void validarUsuarioExistente(UUID usuarioId) {
        if (!usuarioRepository.existsById(usuarioId)) {
            throw new UsuarioNotFoundException("Usuario no encontrado con ID: " + usuarioId);
        }
    }
}