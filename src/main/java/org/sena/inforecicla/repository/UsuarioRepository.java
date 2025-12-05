package org.sena.inforecicla.repository;

import org.sena.inforecicla.model.Usuario;

import java.util.List;
import java.util.UUID;

public interface UsuarioRepository extends BaseRepository<Usuario, UUID> {
    @Override
    default List<Usuario> findAllActivos() {
        return BaseRepository.super.findAllActivos();
    }
}
