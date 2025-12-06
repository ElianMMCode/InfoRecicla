package org.sena.inforecicla.repository;

import org.sena.inforecicla.model.CategoriaPublicacion;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.data.jpa.repository.Query;
import org.springframework.stereotype.Repository;

import java.util.List;
import java.util.Optional;
import java.util.UUID;

@Repository
public interface CategoriaPublicacionRepository extends JpaRepository<CategoriaPublicacion, UUID> {

    List<CategoriaPublicacion> findByEstado(String estado);
    List<CategoriaPublicacion> findByNombreContainingIgnoreCase(String keyword);
    Optional<CategoriaPublicacion> findByNombre(String nombre);
    long countByEstado(String estado);
}