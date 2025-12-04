package org.sena.inforecicla.repository;

import org.sena.inforecicla.model.CategoriaPublicacion;
import org.sena.inforecicla.model.Comentarios;

import java.util.List;
import java.util.UUID;

public interface ComentariosRepository extends BaseRepository <Comentarios, UUID>{

    List<Comentarios> findByUsuario_UsuarioId(UUID usuarioId);

}
