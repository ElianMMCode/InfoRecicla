package org.sena.inforecicla.repository;

import org.sena.inforecicla.model.Material;
import org.springframework.data.jpa.repository.JpaRepository;

import java.util.UUID;

public interface MaterialRepository extends JpaRepository<Material, UUID> {

    Material findByNombreContainingIgnoreCase(String nombre);


}
