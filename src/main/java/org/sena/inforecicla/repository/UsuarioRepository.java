package org.sena.inforecicla.repository;

import org.sena.inforecicla.model.Usuario;
import org.springframework.data.jpa.repository.Query;
import org.springframework.data.repository.query.Param;

import java.util.List;
import java.util.Optional;
import java.util.UUID;

public interface UsuarioRepository extends BaseRepository<Usuario, UUID> {

    @Query("SELECT u FROM Usuario u WHERE TRIM(LOWER(u.email)) = TRIM(LOWER(:email))")
    Optional<Usuario> findByEmail(@Param("email") String email);

    @Query("SELECT u FROM Usuario u WHERE u.celular = :celular")
    Optional<Usuario> findByCelular(@Param("celular") String celular);

    @Query("SELECT u FROM Usuario u WHERE u.numeroDocumento = :numeroDocumento")
    Optional<Usuario> findByNumeroDocumento(@Param("numeroDocumento") String numeroDocumento);

    @Override
    default List<Usuario> findAllActivos() {
        return BaseRepository.super.findAllActivos();
    }
}
