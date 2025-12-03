package org.sena.inforecicla.repository;

import org.sena.inforecicla.model.CategoriaPublicacion;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.stereotype.Repository;

import java.util.UUID;

@Repository
public interface CategoriaPublicacionRepository extends JpaRepository<CategoriaPublicacion, UUID> {
    boolean existsByNombre(String nombre);
}