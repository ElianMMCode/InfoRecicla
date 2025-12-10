package org.sena.inforecicla.controller;

import lombok.extern.slf4j.Slf4j;
import org.springframework.boot.web.servlet.error.ErrorController;
import org.springframework.http.HttpStatus;
import org.springframework.security.core.Authentication;
import org.springframework.security.core.context.SecurityContextHolder;
import org.springframework.stereotype.Controller;
import org.springframework.ui.Model;
import org.springframework.web.bind.annotation.RequestMapping;

import jakarta.servlet.RequestDispatcher;
import jakarta.servlet.http.HttpServletRequest;

@Controller
@Slf4j
public class CustomErrorController implements ErrorController {

    @RequestMapping("/error")
    public String handleError(HttpServletRequest request, Model model) {
        // Obtener información del error
        Object status = request.getAttribute(RequestDispatcher.ERROR_STATUS_CODE);
        Object errorMsg = request.getAttribute(RequestDispatcher.ERROR_MESSAGE);
        Object requestUri = request.getAttribute(RequestDispatcher.ERROR_REQUEST_URI);

        // Obtener información de autenticación
        Authentication auth = SecurityContextHolder.getContext().getAuthentication();

        log.error("🚨 ERROR DETECTADO:");
        log.error("   Status Code: {}", status);
        log.error("   Error Message: {}", errorMsg);
        log.error("   Request URI: {}", requestUri);
        log.error("   Método: {}", request.getMethod());
        log.error("   Usuario autenticado: {}", auth != null && auth.isAuthenticated() ? auth.getName() : "NO AUTENTICADO");

        if (status != null) {
            int statusCode = Integer.parseInt(status.toString());
            model.addAttribute("status", statusCode);
            model.addAttribute("error", HttpStatus.valueOf(statusCode).getReasonPhrase());

            if (statusCode == HttpStatus.FORBIDDEN.value()) {
                log.error("❌ ERROR 403 FORBIDDEN - Posibles causas:");
                log.error("   1. Usuario no autenticado intentando acceder a ruta protegida");
                log.error("   2. Usuario autenticado sin los roles necesarios");
                log.error("   3. CSRF token faltante o inválido");
                log.error("   4. Configuración de SecurityConfig incorrecta");

                model.addAttribute("errorDetails", "Acceso Prohibido - Verifica tus credenciales y permisos");
                model.addAttribute("suggestions", java.util.Arrays.asList(
                    "Intenta iniciar sesión nuevamente",
                    "Verifica que tengas los permisos necesarios",
                    "Contacta al administrador si el problema persiste"
                ));
            } else if (statusCode == HttpStatus.NOT_FOUND.value()) {
                model.addAttribute("errorDetails", "Página no encontrada");
            }
        }

        model.addAttribute("requestUri", requestUri);
        model.addAttribute("timestamp", java.time.LocalDateTime.now());

        return "error/error-page";
    }
}
