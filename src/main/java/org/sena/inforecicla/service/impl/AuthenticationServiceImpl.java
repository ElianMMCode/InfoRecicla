package org.sena.inforecicla.service.impl;

import lombok.extern.slf4j.Slf4j;
import org.sena.inforecicla.model.Usuario;
import org.sena.inforecicla.repository.UsuarioRepository;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.security.core.userdetails.UserDetails;
import org.springframework.security.core.userdetails.UserDetailsService;
import org.springframework.security.core.userdetails.UsernameNotFoundException;
import org.springframework.stereotype.Service;

import java.util.Optional;

@Service
@Slf4j
public class AuthenticationServiceImpl implements UserDetailsService {

    @Autowired
    private UsuarioRepository usuarioRepository;

    @Override
    public UserDetails loadUserByUsername(String email) throws UsernameNotFoundException {
        log.info("🔍 Intentando cargar usuario con email: '{}'", email);
        log.info("   Email trimmed: '{}'", email != null ? email.trim() : "null");
        log.info("   Email lowercase: '{}'", email != null ? email.toLowerCase() : "null");

        Optional<Usuario> usuarioOpt = usuarioRepository.findByEmail(email);

        if (usuarioOpt.isEmpty()) {
            log.warn("❌ Usuario NO encontrado con email: '{}'", email);
            log.warn("   Esto significa que el email NO coincide exactamente en BD");
            log.warn("   Posibles causas:");
            log.warn("   1. Espacios en blanco (antes o después)");
            log.warn("   2. Diferente capitalización");
            log.warn("   3. El usuario no existe realmente");
            throw new UsernameNotFoundException("Usuario no encontrado con email: " + email);
        }

        Usuario usuario = usuarioOpt.get();
        log.info("✅ Usuario encontrado: '{}'", usuario.getEmail());
        log.info("   Nombres: {}", usuario.getNombres());
        log.info("   Tipo Usuario: {}", usuario.getTipoUsuario());
        log.info("   Activo: {}", usuario.getActivo());
        log.info("   Enabled: {}", usuario.isEnabled());
        log.info("   Password hash length: {}", usuario.getPassword() != null ? usuario.getPassword().length() : 0);

        if (!usuario.isEnabled()) {
            log.warn("❌ Usuario encontrado pero DESACTIVADO: {}", email);
            throw new UsernameNotFoundException("Usuario desactivado: " + email);
        }

        log.info("✅ Usuario LISTO para autenticación: {}", email);
        return usuario;
    }

    public UserDetails loadUserByCelular(String celular) throws UsernameNotFoundException {
        log.info("🔍 Intentando cargar usuario con celular: {}", celular);

        Optional<Usuario> usuarioOpt = usuarioRepository.findByCelular(celular);

        if (usuarioOpt.isEmpty()) {
            log.warn("❌ Usuario no encontrado con celular: {}", celular);
            throw new UsernameNotFoundException("Usuario no encontrado con celular: " + celular);
        }

        Usuario usuario = usuarioOpt.get();
        log.info("✅ Usuario encontrado: {}", usuario.getEmail());

        if (!usuario.isEnabled()) {
            log.warn("❌ Usuario desactivado: {}", celular);
            throw new UsernameNotFoundException("Usuario desactivado: " + celular);
        }

        return usuario;
    }
}


