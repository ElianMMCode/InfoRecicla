package org.sena.inforecicla.repository;

import org.sena.inforecicla.model.enums.Estado;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.data.repository.NoRepositoryBean;

import java.util.List;

@NoRepositoryBean
public interface BaseRepository<T, ID> extends JpaRepository<T, ID> {

    List<T> findAllByEstado(Estado estado);

    default List<T> findAllActivos() {
        return findAllByEstado(Estado.Activo);
    }
}