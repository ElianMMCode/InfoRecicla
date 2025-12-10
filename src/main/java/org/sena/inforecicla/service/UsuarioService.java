package org.sena.inforecicla.service;

import org.sena.inforecicla.dto.usuario.RegistroCiudadanoDTO;
import org.sena.inforecicla.dto.usuario.RegistroPuntoEcaDTO;
import org.sena.inforecicla.dto.usuario.UsuarioResponseDTO;
import org.sena.inforecicla.dto.usuario.UsuarioGestorResponseDTO;
import org.sena.inforecicla.dto.usuario.UsuarioRequestDTO;

import java.util.UUID;

public interface UsuarioService {

    // Métodos de registro
    UsuarioResponseDTO registrarCiudadano(RegistroCiudadanoDTO dto);

    UsuarioResponseDTO registrarPuntoECA(RegistroPuntoEcaDTO dto);

    // Métodos de gestión de usuarios
    UsuarioGestorResponseDTO crearUsuarioGestorEca(UsuarioRequestDTO dto);

    UsuarioGestorResponseDTO buscarPorId(UUID id);
}
