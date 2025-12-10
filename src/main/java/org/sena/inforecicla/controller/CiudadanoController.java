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

import java.util.UUID;

@Controller
@RequestMapping("/ciudadano")
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
            Usuario usuario = usuarioRepository.findById(id)
                    .orElseThrow(() -> new RuntimeException("Usuario no encontrado"));

            // Forzar carga de la localidad para evitar LazyInitializationException
            if (usuario.getLocalidad() != null) {
                usuario.getLocalidad().getNombre();
            }

            model.addAttribute("ciudadano", usuario);
            return "views/Ciudadano/ciudadano";
        } catch (Exception e) {
            throw new RuntimeException("Error al cargar el perfil del ciudadano: " + e.getMessage(), e);
        }
    }

    /**
     * POST /ciudadano/{id} - Actualizar información del ciudadano
     */
    @PostMapping("/{id}")
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
            Usuario usuario = usuarioRepository.findById(id)
                    .orElseThrow(() -> new RuntimeException("Usuario no encontrado"));

            // Actualizar información personal
            if (nombres != null && !nombres.trim().isEmpty()) {
                usuario.setNombres(nombres);
            }
            if (apellidos != null && !apellidos.trim().isEmpty()) {
                usuario.setApellidos(apellidos);
            }
            if (email != null && !email.trim().isEmpty()) {
                usuario.setEmail(email);
            }
            if (ciudad != null && !ciudad.trim().isEmpty()) {
                usuario.setCiudad(ciudad);
            }
            if (celular != null && !celular.trim().isEmpty()) {
                usuario.setCelular(celular);
            }
            if (fechaNacimiento != null && !fechaNacimiento.trim().isEmpty()) {
                usuario.setFechaNacimiento(fechaNacimiento);
            }

            // Cambiar contraseña si se proporciona
            if (contrasenaActual != null && !contrasenaActual.trim().isEmpty() &&
                contrasenaNueva != null && !contrasenaNueva.trim().isEmpty()) {

                // Validar contraseña actual
                if (!passwordEncoder.matches(contrasenaActual, usuario.getPassword())) {
                    redirectAttributes.addFlashAttribute("errorMessage", "La contraseña actual es incorrecta");
                    return "redirect:/ciudadano/" + id;
                }

                // Validar que las contraseñas nuevas coincidan
                if (!contrasenaNueva.equals(confirmarContrasena)) {
                    redirectAttributes.addFlashAttribute("errorMessage", "Las contraseñas nuevas no coinciden");
                    return "redirect:/ciudadano/" + id;
                }

                // Validar requisitos de contraseña
                if (!contrasenaNueva.matches("^(?=.*[a-z])(?=.*[A-Z])(?=.*\\d)(?=.*[@$!%*?&])[A-Za-z\\d@$!%*?&]{8,}$")) {
                    redirectAttributes.addFlashAttribute("errorMessage", "La contraseña no cumple los requisitos de seguridad");
                    return "redirect:/ciudadano/" + id;
                }

                // Actualizar contraseña
                usuario.setPassword(passwordEncoder.encode(contrasenaNueva));
            }

            // Guardar cambios
            usuarioRepository.save(usuario);

            redirectAttributes.addFlashAttribute("successMessage", "Cambios guardados exitosamente");
            return "redirect:/ciudadano/" + id;

        } catch (Exception e) {
            redirectAttributes.addFlashAttribute("errorMessage", "Error al guardar los cambios: " + e.getMessage());
            return "redirect:/ciudadano/" + id;
        }
    }
}

