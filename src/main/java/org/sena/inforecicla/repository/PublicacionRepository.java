package org.sena.inforecicla.repository;

import org.sena.inforecicla.model.Publicacion;
import org.sena.inforecicla.model.enums.EstadoPublicacion;
import org.springframework.data.domain.Page;
import org.springframework.data.domain.Pageable;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.data.jpa.repository.Query;
import org.springframework.data.repository.query.Param;
import org.springframework.stereotype.Repository;

import java.util.List;
import java.util.UUID;

@Repository
public interface PublicacionRepository extends JpaRepository<Publicacion, UUID> {

    // ✅ Métodos básicos
    List<Publicacion> findByUsuarioId(UUID usuarioId);
    List<Publicacion> findByCategoriaPublicacionId(UUID categoriaId);

    // ✅ Usar "estadoPublicacion" que es el nombre del campo en la entidad
    List<Publicacion> findByEstadoPublicacion(EstadoPublicacion estadoPublicacion);

    // ✅ Búsqueda por palabra clave
    @Query("SELECT p FROM Publicacion p WHERE " +
            "LOWER(p.titulo) LIKE LOWER(CONCAT('%', :keyword, '%')) OR " +
            "LOWER(p.contenido) LIKE LOWER(CONCAT('%', :keyword, '%'))")
    List<Publicacion> buscarPorPalabraClave(@Param("keyword") String keyword);

    // ✅ Métodos paginados
    Page<Publicacion> findByEstadoPublicacion(EstadoPublicacion estadoPublicacion, Pageable pageable);
    Page<Publicacion> findByCategoriaPublicacionId(UUID categoriaId, Pageable pageable);

    Page<Publicacion> findByEstadoPublicacionAndCategoriaPublicacionId(
            EstadoPublicacion estadoPublicacion, UUID categoriaId, Pageable pageable);

    @Query("SELECT p FROM Publicacion p WHERE " +
            "(LOWER(p.titulo) LIKE LOWER(CONCAT('%', :keyword, '%')) OR " +
            "LOWER(p.contenido) LIKE LOWER(CONCAT('%', :keyword, '%')))")
    Page<Publicacion> buscarPorPalabraClave(@Param("keyword") String keyword, Pageable pageable);

    // ✅ Métodos de conteo
    long countByEstadoPublicacion(EstadoPublicacion estadoPublicacion);
    long countByEstadoPublicacionNot(EstadoPublicacion estadoPublicacion);

    long countByUsuarioIdAndEstadoPublicacionNot(UUID usuarioId, EstadoPublicacion estadoPublicacion);
    long countByUsuarioIdAndEstadoPublicacion(UUID usuarioId, EstadoPublicacion estadoPublicacion);

    long countByCategoriaPublicacionIdAndEstadoPublicacionNot(UUID categoriaId, EstadoPublicacion estadoPublicacion);
    long countByCategoriaPublicacionIdAndEstadoPublicacion(UUID categoriaId, EstadoPublicacion estadoPublicacion);

    // ✅ Métodos adicionales
    List<Publicacion> findByEstadoPublicacionNot(EstadoPublicacion estadoPublicacion, Pageable pageable);
    List<Publicacion> findByUsuarioIdAndEstadoPublicacion(UUID usuarioId, EstadoPublicacion estadoPublicacion);
}