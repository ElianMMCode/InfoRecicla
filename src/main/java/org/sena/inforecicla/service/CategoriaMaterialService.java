package org.sena.inforecicla.service;

import org.sena.inforecicla.dto.puntoEca.materiales.CategoriaMaterialesInvResponseDTO;

import java.util.List;
import java.util.UUID;

public interface CategoriaMaterialService {
    List<CategoriaMaterialesInvResponseDTO> listarCategoriasMateriales();
}

