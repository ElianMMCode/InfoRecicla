package org.sena.inforecicla.service;

import org.sena.inforecicla.dto.usuario.UsuarioGestorResponseDTO;
import org.sena.inforecicla.dto.usuario.UsuarioRequestDTO;

import java.util.UUID;

public interface UsuarioService {

    UsuarioGestorResponseDTO crearUsuarioGestorEca(UsuarioRequestDTO dto);

    UsuarioGestorResponseDTO buscarPorId(UUID id);
}
