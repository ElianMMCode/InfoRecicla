package org.sena.inforecicla.controller;

import org.sena.inforecicla.model.PuntoECA;
import org.sena.inforecicla.repository.PuntoEcaRepository;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.security.core.Authentication;
import org.springframework.security.core.context.SecurityContextHolder;
import org.springframework.stereotype.Controller;
import org.springframework.ui.Model;
import org.springframework.web.bind.annotation.GetMapping;
import org.springframework.web.bind.annotation.RequestMapping;
import org.springframework.transaction.annotation.Transactional;

import jakarta.servlet.http.HttpServletRequest;
import lombok.extern.slf4j.Slf4j;
import java.util.Optional;
import org.sena.inforecicla.model.Usuario;

@Controller
@RequestMapping("/dashboard")
@Slf4j
public class DashboardController {

    @Autowired
    private PuntoEcaRepository puntoEcaRepository;

    @GetMapping("")
    @Transactional(readOnly = true)
    public String dashboard(Model model, HttpServletRequest request) {
        log.info("🏠 Acceso al dashboard desde: {}", request.getRequestURI());
        log.info("🔍 Query string: {}", request.getQueryString());

        Authentication auth = SecurityContextHolder.getContext().getAuthentication();
        log.info("🔐 Estado de autenticación: {}", auth != null && auth.isAuthenticated() ? "AUTENTICADO" : "NO AUTENTICADO");

        if (auth != null && auth.isAuthenticated() && auth.getPrincipal() instanceof Usuario) {
            Usuario usuario = (Usuario) auth.getPrincipal();
            log.info("👤 Usuario autenticado: {} ({})", usuario.getNombres(), usuario.getTipoUsuario());

            model.addAttribute("usuario", usuario);
            model.addAttribute("nombreCompleto", usuario.getNombres() + " " + usuario.getApellidos());
            model.addAttribute("tipoUsuario", usuario.getTipoUsuario());

            // Redirigir según el tipo de usuario
            switch (usuario.getTipoUsuario()) {
                case Admin:
                    log.info("📊 Redirigiendo a dashboard de administrador");
                    return "dashboard/admin-dashboard";
                case GestorECA:
                    log.info("🏢 Usuario GestorECA, buscando punto ECA asociado...");
                    // Buscar el PuntoECA asociado al gestor y redirigir directamente
                    Optional<PuntoECA> puntoEcaOptional = puntoEcaRepository.findByUsuario_UsuarioId(usuario.getUsuarioId());
                    if (puntoEcaOptional.isPresent()) {
                        PuntoECA puntoEca = puntoEcaOptional.get();
                        String nombrePunto = puntoEca.getNombrePunto().replace(" ", "-").toLowerCase();
                        String redirectUrl = "/punto-eca/" + nombrePunto + "/" + usuario.getUsuarioId();
                        log.info("✅ Punto ECA encontrado: '{}' -> Redirigiendo a: {}", puntoEca.getNombrePunto(), redirectUrl);
                        // Redirigir al controlador específico del punto ECA
                        return "redirect:" + redirectUrl;
                    } else {
                        log.warn("⚠️ No se encontró punto ECA para el gestor, usando dashboard general");
                        // Si no tiene punto ECA asociado, ir al dashboard de gestor general
                        return "dashboard/gestor-dashboard";
                    }
                case Ciudadano:
                    log.info("👥 Redirigiendo a dashboard de ciudadano");
                    return "redirect:/ciudadano/" + usuario.getUsuarioId();
                default:
                    log.info("❓ Tipo de usuario desconocido, usando dashboard general");
                    return "dashboard/general-dashboard";
            }
        }

        log.warn("🚫 Usuario no autenticado, redirigiendo al login");
        // Si no está autenticado, redirigir al login
        return "redirect:/login";
    }

    @GetMapping("/admin")
    public String adminDashboard(Model model) {
        Authentication auth = SecurityContextHolder.getContext().getAuthentication();

        if (auth != null && auth.isAuthenticated() && auth.getPrincipal() instanceof Usuario) {
            Usuario usuario = (Usuario) auth.getPrincipal();

            // Verificar que sea administrador
            if (usuario.getTipoUsuario().name().equals("Admin")) {
                model.addAttribute("usuario", usuario);
                return "dashboard/admin-dashboard";
            }
        }

        // Si no es admin, redirigir al dashboard normal
        return "redirect:/dashboard";
    }

    @GetMapping("/gestor")
    public String gestorDashboard(Model model) {
        Authentication auth = SecurityContextHolder.getContext().getAuthentication();

        if (auth != null && auth.isAuthenticated() && auth.getPrincipal() instanceof Usuario) {
            Usuario usuario = (Usuario) auth.getPrincipal();
            model.addAttribute("usuario", usuario);
            model.addAttribute("nombreCompleto", usuario.getNombres() + " " + usuario.getApellidos());
            model.addAttribute("tipoUsuario", usuario.getTipoUsuario());

            // Permitir acceso tanto a admin como a gestor ECA
            if (usuario.getTipoUsuario().name().equals("Admin") ||
                usuario.getTipoUsuario().name().equals("GestorECA")) {
                return "dashboard/gestor-dashboard";
            }
        }

        // Si no tiene permisos, redirigir al dashboard normal
        return "redirect:/dashboard";
    }
}
