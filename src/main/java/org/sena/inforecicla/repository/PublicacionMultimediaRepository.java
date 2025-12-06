package org.sena.inforecicla.repository;

import org.sena.inforecicla.model.PublicacionesMultimedia;
import org.sena.inforecicla.model.enums.TipoMultimedia;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.stereotype.Repository;

import java.util.List;
import java.util.UUID;

@Repository
public interface PublicacionMultimediaRepository extends JpaRepository<PublicacionesMultimedia, UUID> {

    List<PublicacionesMultimedia> findByPublicacionPublicacionId(UUID publicacionId);

    List<PublicacionesMultimedia> findByTipoMultimedia(TipoMultimedia tipoMultimedia);
}


