package org.sena.inforecicla.service;

import org.sena.inforecicla.dto.puntoEca.gestor.GestorResponseDTO;
import org.sena.inforecicla.dto.puntoEca.gestor.GestorUpdateDTO;
import org.sena.inforecicla.dto.usuario.UsuarioGestorRequestDTO;
import org.sena.inforecicla.dto.usuario.UsuarioGestorResponseDTO;

import java.util.UUID;

public interface GestorEcaService {

    UsuarioGestorResponseDTO crearGestorConPuntoEca(UsuarioGestorRequestDTO dto);

    UsuarioGestorResponseDTO buscarGestorPuntoEca(UUID id);

    GestorResponseDTO actualizarGestor(UUID gestorId, GestorUpdateDTO gestorUpdate);
}
