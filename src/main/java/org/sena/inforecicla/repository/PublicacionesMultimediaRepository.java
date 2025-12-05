package org.sena.inforecicla.repository;

import org.sena.inforecicla.model.CategoriaPublicacion;
import org.sena.inforecicla.model.PublicacionesMultimedia;
import org.sena.inforecicla.model.enums.TipoMultimedia;

import java.util.List;
import java.util.UUID;

public interface PublicacionesMultimediaRepository extends BaseRepository <PublicacionesMultimedia, UUID> {

    // Obtener todas las fotos/videos de una publicación específica
    List<PublicacionesMultimedia> findByPublicacion_PublicacionId(UUID publicacionId);

    // Filtrar por tipo
    List<PublicacionesMultimedia> findByTipoMultimedia(TipoMultimedia tipoMultimedia);

}


