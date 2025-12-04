package org.sena.inforecicla.repository;

import org.sena.inforecicla.model.CategoriaPublicacion;
import org.sena.inforecicla.model.Mensajes;

import java.util.List;
import java.util.UUID;

public interface MensajesRepository extends BaseRepository <Mensajes, UUID>{


    List<Mensajes> findByConversacion_ConversacionIdOrderByFechaCreacionAsc(UUID conversacionId); // Asumiendo que heredas fechaCreacion


    List<Mensajes> findByConversacion_ConversacionId(UUID conversacionId);
}
