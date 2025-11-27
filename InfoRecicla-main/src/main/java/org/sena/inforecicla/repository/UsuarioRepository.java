package org.sena.inforecicla.repository;

import org.sena.inforecicla.model.Usuario;
import org.sena.inforecicla.model.enums.TipoUsuario;
import org.springframework.data.jpa.repository.JpaRepository;

import java.util.List;
import java.util.UUID;

public interface UsuarioRepository extends JpaRepository<Usuario, UUID> {
    List<Usuario> findByTipoUsuario(TipoUsuario tipoUsuario);
}
