package org.sena.inforecicla.controller;

import lombok.extern.slf4j.Slf4j;
import org.springframework.stereotype.Controller;
import org.springframework.web.bind.annotation.GetMapping;
import org.springframework.web.bind.annotation.RequestParam;

/**
 * Controlador para manejar redirecciones problemáticas de herramientas de desarrollo
 */
@Controller
@Slf4j
public class DevToolsRedirectController {

    /**
     * Maneja redirecciones problemáticas de Chrome DevTools
     */
    @GetMapping("/.well-known/appspecific/com.chrome.devtools.json")
    public String handleChromeDevToolsRedirect(@RequestParam(value = "continue", required = false) String continueParam) {
        log.warn("🔧 Interceptada redirección problemática de Chrome DevTools");
        log.warn("   Continue param: {}", continueParam);
        log.warn("   Redirigiendo al dashboard para corregir el flujo");

        return "redirect:/dashboard";
    }

    /**
     * Maneja cualquier otra redirección problemática bajo .well-known
     */
    @GetMapping("/.well-known/**")
    public String handleWellKnownRedirects() {
        log.warn("🔧 Interceptada redirección problemática bajo .well-known");
        log.warn("   Redirigiendo al dashboard para corregir el flujo");

        return "redirect:/dashboard";
    }
}
