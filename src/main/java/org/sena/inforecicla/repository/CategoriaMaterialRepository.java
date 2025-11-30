package org.sena.inforecicla.repository;

import org.sena.inforecicla.model.CategoriaMaterial;
import org.springframework.data.jpa.repository.JpaRepository;

import java.util.UUID;

public interface CategoriaMaterialRepository extends JpaRepository<CategoriaMaterial, UUID> {
}
