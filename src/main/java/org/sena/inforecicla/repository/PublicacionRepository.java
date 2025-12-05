package org.sena.inforecicla.repository;

import org.sena.inforecicla.model.Publicacion;
import org.sena.inforecicla.model.enums.Estado;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.data.jpa.repository.Query;
import org.springframework.data.repository.query.Param;
import org.springframework.stereotype.Repository;

import java.util.List;
import java.util.UUID;

@Repository
public interface PublicacionRepository extends JpaRepository<Publicacion, UUID> {

    List<Publicacion> findByUsuarioId(UUID usuarioId);

    @Query("SELECT p FROM Publicacion p WHERE p.categoriaPublicacion.categoriaPublicacionId = :categoriaId")
    List<Publicacion> findByCategoriaPublicacionId(@Param("categoriaId") UUID categoriaId);

    @Query("SELECT p FROM Publicacion p WHERE p.estado = :estado")
    List<Publicacion> findByEstado(@Param("estado") Estado estado);

    @Query("SELECT p FROM Publicacion p WHERE LOWER(p.titulo) LIKE LOWER(CONCAT('%', :keyword, '%')) " +
            "OR LOWER(p.contenido) LIKE LOWER(CONCAT('%', :keyword, '%'))")
    List<Publicacion> buscarPorPalabraClave(@Param("keyword") String keyword);

    @Query("SELECT p FROM Publicacion p WHERE p.usuario.usuarioId = :usuarioId AND p.estado = :estado")
    List<Publicacion> findByUsuarioIdAndEstado(@Param("usuarioId") UUID usuarioId,
                                               @Param("estado") Estado estado);

    long countByUsuarioId(UUID usuarioId);
}
