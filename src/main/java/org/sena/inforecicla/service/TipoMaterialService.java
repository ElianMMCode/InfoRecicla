package org.sena.inforecicla.service;

import org.sena.inforecicla.dto.puntoEca.materiales.TipoMaterialesInvResponseDTO;

import java.util.List;

public interface TipoMaterialService {

    List<TipoMaterialesInvResponseDTO> listarTiposMateriales();
}

