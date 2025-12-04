package org.sena.inforecicla.repository;

import org.sena.inforecicla.model.Localidad;
import org.springframework.stereotype.Repository;

import java.util.Optional;
import java.util.UUID;

@Repository
public interface LocalidadRepository extends BaseRepository<Localidad, UUID> {
    Optional<Localidad> findByNombreIgnoreCase(String nombre);
}

