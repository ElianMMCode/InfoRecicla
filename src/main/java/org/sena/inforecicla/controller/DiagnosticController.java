package org.sena.inforecicla.controller;

import lombok.extern.slf4j.Slf4j;
import org.springframework.security.core.Authentication;
import org.springframework.security.core.context.SecurityContextHolder;
import org.springframework.stereotype.Controller;
import org.springframework.ui.Model;
import org.springframework.web.bind.annotation.GetMapping;
import org.springframework.web.bind.annotation.ResponseBody;

/**
 * Controlador de diagnóstico para resolver problemas de acceso
 */
@Controller
@Slf4j
public class DiagnosticController {

    @GetMapping("/diagnostico")
    @ResponseBody
    public String diagnostico() {
        Authentication auth = SecurityContextHolder.getContext().getAuthentication();

        StringBuilder info = new StringBuilder();
        info.append("=== DIAGNÓSTICO DE SEGURIDAD ===\n");
        info.append("Timestamp: ").append(java.time.LocalDateTime.now()).append("\n");

        if (auth == null) {
            info.append("❌ Authentication es NULL\n");
        } else {
            info.append("✅ Authentication existe\n");
            info.append("   - Autenticado: ").append(auth.isAuthenticated()).append("\n");
            info.append("   - Principal: ").append(auth.getPrincipal().getClass().getSimpleName()).append("\n");
            info.append("   - Name: ").append(auth.getName()).append("\n");
            info.append("   - Authorities: ").append(auth.getAuthorities()).append("\n");

            if (auth.getPrincipal() instanceof org.sena.inforecicla.model.Usuario) {
                org.sena.inforecicla.model.Usuario usuario = (org.sena.inforecicla.model.Usuario) auth.getPrincipal();
                info.append("   - Usuario ID: ").append(usuario.getUsuarioId()).append("\n");
                info.append("   - Email: ").append(usuario.getEmail()).append("\n");
                info.append("   - Tipo Usuario: ").append(usuario.getTipoUsuario()).append("\n");
                info.append("   - Activo: ").append(usuario.getActivo()).append("\n");
                info.append("   - Enabled: ").append(usuario.isEnabled()).append("\n");
            }
        }

        return info.toString();
    }

    @GetMapping("/test-public")
    @ResponseBody
    public String testPublic() {
        return "✅ Ruta pública funcionando correctamente - " + java.time.LocalDateTime.now();
    }

    @GetMapping("/test-auth")
    @ResponseBody
    public String testAuth() {
        Authentication auth = SecurityContextHolder.getContext().getAuthentication();
        return "✅ Ruta autenticada funcionando - Usuario: " + (auth != null ? auth.getName() : "ANÓNIMO");
    }

    @GetMapping("/test-dashboard")
    public String testDashboard(Model model) {
        log.info("🧪 Acceso a test-dashboard");
        Authentication auth = SecurityContextHolder.getContext().getAuthentication();

        if (auth != null && auth.isAuthenticated()) {
            model.addAttribute("usuario", auth.getPrincipal());
            model.addAttribute("mensaje", "Dashboard de prueba funcionando correctamente");
            return "test-dashboard";
        } else {
            return "redirect:/login";
        }
    }
}
