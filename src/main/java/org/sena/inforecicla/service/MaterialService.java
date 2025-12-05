package org.sena.inforecicla.service;

import org.sena.inforecicla.exception.MaterialNotFoundException;
import org.sena.inforecicla.model.Material;

import java.util.Optional;
import java.util.UUID;

public interface MaterialService {

    Optional<Material> encontrarMaterial(UUID material) throws MaterialNotFoundException;
}


