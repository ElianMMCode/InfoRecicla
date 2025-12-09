package org.sena.inforecicla.service.impl;

import lombok.RequiredArgsConstructor;
import org.sena.inforecicla.dto.puntoEca.materiales.TipoMaterialesInvResponseDTO;
import org.sena.inforecicla.repository.TipoMaterialRepository;
import org.sena.inforecicla.service.TipoMaterialService;
import org.springframework.stereotype.Service;

import java.util.List;

import static java.util.Comparator.comparing;

@Service
@RequiredArgsConstructor
public class TipoMaterialServiceImpl implements TipoMaterialService {

    private final TipoMaterialRepository tipoMaterialRepository;

    @Override
    public List<TipoMaterialesInvResponseDTO> listarTiposMateriales() {
        return tipoMaterialRepository.findAllActivos().stream()
                .map(TipoMaterialesInvResponseDTO::derivado)
                .sorted(comparing(TipoMaterialesInvResponseDTO::nmbCategoria))
                .toList();
    }
}

