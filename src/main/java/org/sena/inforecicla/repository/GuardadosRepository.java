package org.sena.inforecicla.repository;

import org.sena.inforecicla.model.CategoriaPublicacion;
import org.sena.inforecicla.model.Guardados;
import org.sena.inforecicla.model.enums.TipoPublicacion;

import java.util.List;
import java.util.UUID;

public interface GuardadosRepository extends BaseRepository <Guardados, UUID>{


    List<Guardados> findByUsuario_UsuarioId(UUID usuarioId);


    List<Guardados> findByUsuario_UsuarioIdAndTipo(UUID usuarioId, TipoPublicacion tipo);
}
