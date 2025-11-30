package org.sena.inforecicla.repository;

import org.sena.inforecicla.model.TipoMaterial;
import org.springframework.data.jpa.repository.JpaRepository;

import java.util.UUID;

public interface TipoMaterialRepository extends JpaRepository<TipoMaterial,UUID>{
}
