package org.sena.inforecicla.repository;

import org.sena.inforecicla.model.CategoriaPublicacion;
import org.sena.inforecicla.model.Votos;

import java.util.List;
import java.util.Optional;
import java.util.UUID;

public interface VotosRepository extends BaseRepository <Votos, UUID> {
    List<Votos> findByPublicacion_PublicacionId(UUID publicacionId);

    // Verificar si un usuario ya votó en una publicación (para evitar duplicados)
    Optional<Votos> findByUsuario_UsuarioIdAndPublicacion_PublicacionId(UUID usuarioId, UUID publicacionId);

    // Contar votos de una publicación
    long countByPublicacion_PublicacionId(UUID publicacionId);
}
