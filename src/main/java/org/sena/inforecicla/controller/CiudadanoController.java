package org.sena.inforecicla.controller;

import org.sena.inforecicla.model.Usuario;
import org.sena.inforecicla.repository.UsuarioRepository;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.security.crypto.password.PasswordEncoder;
import org.springframework.stereotype.Controller;
import org.springframework.transaction.annotation.Transactional;
import org.springframework.ui.Model;
import org.springframework.web.bind.annotation.*;
import org.springframework.web.servlet.mvc.support.RedirectAttributes;
import lombok.extern.slf4j.Slf4j;

import java.util.UUID;

@Controller
@RequestMapping("/ciudadano")
@Slf4j
public class CiudadanoController {

    @Autowired
    private UsuarioRepository usuarioRepository;

    @Autowired
    private PasswordEncoder passwordEncoder;

    /**
     * GET /ciudadano/{id} - Mostrar perfil del ciudadano
     */
    @GetMapping("/{id}")
    @Transactional(readOnly = true)
    public String perfilCiudadano(@PathVariable UUID id, Model model) {
        try {
            log.info("📋 Cargando perfil del ciudadano: {}", id);

            Usuario usuario = usuarioRepository.findById(id)
                    .orElseThrow(() -> new RuntimeException("Usuario no encontrado"));

            // Forzar carga de la localidad para evitar LazyInitializationException
            if (usuario.getLocalidad() != null) {
                usuario.getLocalidad().getNombre();
            }

            model.addAttribute("ciudadano", usuario);
            log.info("✅ Perfil cargado correctamente para: {}", usuario.getEmail());
            return "views/Ciudadano/ciudadano";
        } catch (Exception e) {
            log.error("❌ Error al cargar perfil: {}", e.getMessage());
            throw new RuntimeException("Error al cargar el perfil del ciudadano: " + e.getMessage(), e);
        }
    }

    /**
     * POST /ciudadano/{id} - Actualizar información del ciudadano
     */
    @PostMapping("/{id}")
    @Transactional
    public String actualizarCiudadano(
            @PathVariable UUID id,
            @RequestParam(required = false) String nombres,
            @RequestParam(required = false) String apellidos,
            @RequestParam(required = false) String email,
            @RequestParam(required = false) String ciudad,
            @RequestParam(required = false) String celular,
            @RequestParam(required = false) String fechaNacimiento,
            @RequestParam(required = false) String contrasenaActual,
            @RequestParam(required = false) String contrasenaNueva,
            @RequestParam(required = false) String confirmarContrasena,
            RedirectAttributes redirectAttributes) {

        try {
            log.info("🔄 Iniciando actualización de ciudadano: {}", id);

            Usuario usuario = usuarioRepository.findById(id)
                    .orElseThrow(() -> new RuntimeException("Usuario no encontrado"));

            // Validar que al menos email sea válido
            if (email != null && !email.trim().isEmpty()) {
                if (!email.matches("^[A-Za-z0-9+_.-]+@(.+)$")) {
                    redirectAttributes.addFlashAttribute("errorMessage", "El email no es válido");
                    log.warn("❌ Email inválido: {}", email);
                    return "redirect:/ciudadano/" + id;
                }
                usuario.setEmail(email);
            }

            // Actualizar información personal
            if (nombres != null && !nombres.trim().isEmpty()) {
                usuario.setNombres(nombres);
                log.debug("✏️ Nombres actualizados a: {}", nombres);
            }
            if (apellidos != null && !apellidos.trim().isEmpty()) {
                usuario.setApellidos(apellidos);
                log.debug("✏️ Apellidos actualizados a: {}", apellidos);
            }
            if (ciudad != null && !ciudad.trim().isEmpty()) {
                usuario.setCiudad(ciudad);
                log.debug("✏️ Ciudad actualizada a: {}", ciudad);
            }
            if (celular != null && !celular.trim().isEmpty()) {
                // Validar que sea un número válido
                if (!celular.matches("^[0-9]{10}$")) {
                    redirectAttributes.addFlashAttribute("errorMessage", "El celular debe tener 10 dígitos");
                    log.warn("❌ Celular inválido: {}", celular);
                    return "redirect:/ciudadano/" + id;
                }
                usuario.setCelular(celular);
                log.debug("✏️ Celular actualizado");
            }
            if (fechaNacimiento != null && !fechaNacimiento.trim().isEmpty()) {
                usuario.setFechaNacimiento(fechaNacimiento);
                log.debug("✏️ Fecha de nacimiento actualizada");
            }

            // Cambiar contraseña si se proporciona
            if (contrasenaActual != null && !contrasenaActual.trim().isEmpty() &&
                contrasenaNueva != null && !contrasenaNueva.trim().isEmpty()) {

                log.info("🔐 Intentando cambiar contraseña");

                // Validar contraseña actual
                if (!passwordEncoder.matches(contrasenaActual, usuario.getPassword())) {
                    redirectAttributes.addFlashAttribute("errorMessage", "La contraseña actual es incorrecta");
                    log.warn("❌ Contraseña actual incorrecta para usuario: {}", usuario.getEmail());
                    return "redirect:/ciudadano/" + id;
                }

                // Validar que las contraseñas nuevas coincidan
                if (!contrasenaNueva.equals(confirmarContrasena)) {
                    redirectAttributes.addFlashAttribute("errorMessage", "Las contraseñas nuevas no coinciden");
                    log.warn("❌ Las contraseñas nuevas no coinciden");
                    return "redirect:/ciudadano/" + id;
                }

                // Validar requisitos de contraseña
                if (!contrasenaNueva.matches("^(?=.*[a-z])(?=.*[A-Z])(?=.*\\d)(?=.*[@$!%*?&])[A-Za-z\\d@$!%*?&]{8,}$")) {
                    redirectAttributes.addFlashAttribute("errorMessage", "La contraseña no cumple los requisitos: mín 8 caracteres, mayúscula, minúscula, número y carácter especial");
                    log.warn("❌ Contraseña no cumple requisitos");
                    return "redirect:/ciudadano/" + id;
                }

                // Actualizar contraseña
                usuario.setPassword(passwordEncoder.encode(contrasenaNueva));
                log.info("✅ Contraseña actualizada");
            }

            // Guardar cambios
            usuarioRepository.save(usuario);

            redirectAttributes.addFlashAttribute("successMessage", "✅ Cambios guardados exitosamente");
            log.info("✅ Perfil actualizado correctamente para: {}", usuario.getEmail());
            return "redirect:/ciudadano/" + id;

        } catch (Exception e) {
            log.error("❌ Error al guardar los cambios: {}", e.getMessage(), e);
            redirectAttributes.addFlashAttribute("errorMessage", "Error al guardar los cambios: " + e.getMessage());
            return "redirect:/ciudadano/" + id;
        }
    }
}

