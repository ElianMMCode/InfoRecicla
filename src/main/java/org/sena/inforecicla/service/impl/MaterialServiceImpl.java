package org.sena.inforecicla.service.impl;

import lombok.RequiredArgsConstructor;
import org.sena.inforecicla.exception.MaterialNotFoundException;
import org.sena.inforecicla.model.Material;
import org.sena.inforecicla.repository.MaterialRepository;
import org.sena.inforecicla.service.MaterialService;
import org.springframework.stereotype.Service;

import java.util.Optional;
import java.util.UUID;

import static java.util.Comparator.comparing;

@Service
@RequiredArgsConstructor
public class MaterialServiceImpl implements MaterialService {

    private final MaterialRepository materialRepository;

    private String normalizarParametro(String valor) {
        return valor != null ? valor : "";
    }

    @Override
    public Optional<Material> encontrarMaterial(UUID material) throws MaterialNotFoundException {
        return materialRepository.findByMaterialIdAndEstadoActivo(material);
    }
}

