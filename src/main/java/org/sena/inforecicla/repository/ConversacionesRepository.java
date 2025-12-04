package org.sena.inforecicla.repository;

import org.sena.inforecicla.model.CategoriaPublicacion;
import org.sena.inforecicla.model.Conversaciones;

import java.util.List;
import java.util.UUID;

public interface ConversacionesRepository extends BaseRepository <Conversaciones, UUID>{
    List<Conversaciones> findByCreadores_UsuarioId(UUID usuarioId);


    List<Conversaciones> findByTituloContainingIgnoreCase(String titulo);

}
