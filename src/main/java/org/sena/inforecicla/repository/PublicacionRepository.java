package org.sena.inforecicla.repository;



import org.sena.inforecicla.model.Publicacion;
import org.sena.inforecicla.model.enums.Estado;

import java.util.List;
import java.util.UUID;

public interface PublicacionRepository  extends BaseRepository <Publicacion, UUID>{

    // Buscar publicaciones por su estado (ej: PUBLICADO, BORRADOR)
    List<Publicacion> findByEstado(Estado estado);

    // Buscar todas las publicaciones de un usuario específico
    // Asumiendo que tu entidad Usuario tiene un campo 'id' o 'usuarioId'
    List<Publicacion> findByUsuario_UsuarioId(UUID usuarioId);

    // Buscar publicaciones que contengan cierto texto en el título (Búsqueda simple)
    List<Publicacion> findByTituloContainingIgnoreCase(String titulo);
}
