package org.sena.inforecicla.repository;

import org.sena.inforecicla.model.PuntoECA;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.stereotype.Repository;

import java.util.UUID;

@Repository
public interface PuntoECARepository extends JpaRepository<PuntoECA, UUID> {
}
