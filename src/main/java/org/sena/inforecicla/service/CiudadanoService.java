package org.sena.inforecicla.service;

import org.sena.inforecicla.dto.usuario.UsuarioResponseDTO;

import java.util.UUID;

public interface CiudadanoService {
    UsuarioResponseDTO getCiudadano(UUID id);

    UsuarioResponseDTO actualizarCiudadano(UUID ciudadanoId, UsuarioResponseDTO dto);
}
