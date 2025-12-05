package org.sena.inforecicla.service.impl;

import lombok.RequiredArgsConstructor;
import org.sena.inforecicla.dto.puntoEca.materiales.CategoriaMaterialesInvResponseDTO;
import org.sena.inforecicla.model.Material;
import org.sena.inforecicla.repository.CategoriaMaterialRepository;
import org.sena.inforecicla.service.CategoriaMaterialService;
import org.springframework.stereotype.Service;

import java.util.List;
import java.util.UUID;

import static java.util.Comparator.comparing;

@Service
@RequiredArgsConstructor
public class CategoriaMaterialServiceImpl implements CategoriaMaterialService {

    private final CategoriaMaterialRepository categoriaMaterialRepository;
    private final MaterialServiceImpl materialService;

    @Override
    public List<CategoriaMaterialesInvResponseDTO> listarCategoriasMateriales() {
        return categoriaMaterialRepository.findAllActivos().stream()
                .map(CategoriaMaterialesInvResponseDTO::derivado)
                .sorted(comparing(CategoriaMaterialesInvResponseDTO::nmbCategoria))
                .toList();
    }

}

