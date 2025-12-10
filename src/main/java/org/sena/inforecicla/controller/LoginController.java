package org.sena.inforecicla.controller;

import lombok.extern.slf4j.Slf4j;
import org.springframework.security.core.Authentication;
import org.springframework.stereotype.Controller;
import org.springframework.ui.Model;
import org.springframework.web.bind.annotation.GetMapping;
import org.springframework.web.bind.annotation.RequestMapping;
import org.springframework.web.bind.annotation.RequestParam;

import jakarta.servlet.http.HttpServletRequest;

@Controller
@RequestMapping
@Slf4j
public class LoginController {

    @GetMapping("/login")
    public String mostrarLogin(
            @RequestParam(value = "error", required = false) String error,
            @RequestParam(value = "registro", required = false) String registro,
            @RequestParam(value = "continue", required = false) String continueParam,
            HttpServletRequest request,
            Model model,
            Authentication authentication) {

        log.info("📌 GET /login - Error: {}, Registro: {}, Continue: {}", error, registro, continueParam);
        log.info("📌 Request URI: {}", request.getRequestURI());
        log.info("📌 Query String: {}", request.getQueryString());
        log.info("📌 Autenticación actual: {}", authentication != null ?
                "AUTENTICADO - " + authentication.getName() : "NO AUTENTICADO");

        // Si ya está autenticado, redirige al dashboard
        if (authentication != null && authentication.isAuthenticated()) {
            log.info("✅ Usuario ya autenticado: {}, redirigiendo al dashboard", authentication.getName());
            return "redirect:/dashboard";
        }

        // Detectar si viene de Chrome DevTools y limpiar
        if (continueParam != null || request.getRequestURI().contains("well-known")) {
            log.warn("⚠️ Detectada redirección problemática de Chrome DevTools, limpiando...");
            return "redirect:/login";
        }

        // Pasar mensaje de error si existe
        if (error != null && !error.isEmpty()) {
            log.warn("❌ Error de login detectado");
            model.addAttribute("error", "Email o contraseña incorrectos");
        }

        // Pasar mensaje de registro exitoso
        if (registro != null && registro.equals("success")) {
            log.info("✅ Registro exitoso");
            model.addAttribute("success", "¡Registro exitoso! Ahora puedes iniciar sesión");
        }

        log.info("📄 Mostrando página de login");
        return "views/Auth/login";
    }

    @GetMapping("/logout")
    public String logout() {
        log.info("🚪 Usuario realizando logout");
        // Spring Security maneja el logout automáticamente
        return "redirect:/";
    }
}


