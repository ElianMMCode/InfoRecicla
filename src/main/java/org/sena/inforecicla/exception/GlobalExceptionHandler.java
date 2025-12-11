package org.sena.inforecicla.exception;

import org.slf4j.Logger;
import org.slf4j.LoggerFactory;
import org.springframework.http.HttpStatus;
import org.springframework.http.ResponseEntity;
import org.springframework.http.converter.HttpMessageNotReadableException;
import org.springframework.web.bind.annotation.ControllerAdvice;
import org.springframework.web.bind.annotation.ExceptionHandler;
import org.springframework.web.servlet.ModelAndView;
import jakarta.servlet.http.HttpServletRequest;

import java.util.Map;

/**
 * Manejador global de excepciones para la aplicación
 */
@ControllerAdvice
public class GlobalExceptionHandler {

    private static final Logger logger = LoggerFactory.getLogger(GlobalExceptionHandler.class);

    /**
     * Maneja excepciones de JSON inválido
     */
    @ExceptionHandler(HttpMessageNotReadableException.class)
    public ResponseEntity<?> handleHttpMessageNotReadable(HttpMessageNotReadableException e, HttpServletRequest request) {
        logger.error("═══════════════════════════════════════════════════════════");
        logger.error("❌ EXCEPCIÓN GLOBAL: HttpMessageNotReadableException");
        logger.error("═══════════════════════════════════════════════════════════");
        logger.error("URL: {}", request.getRequestURL());
        logger.error("Método: {}", request.getMethod());
        logger.error("Tipo: {}", e.getClass().getSimpleName());
        logger.error("Mensaje: {}", e.getMessage());
        if (e.getMostSpecificCause() != null) {
            logger.error("Causa: {}", e.getMostSpecificCause().getMessage());
        }
        logger.error("Stack trace:", e);
        logger.error("═══════════════════════════════════════════════════════════");

        return ResponseEntity.badRequest()
            .body(Map.of(
                "error", "JSON inválido",
                "tipo", "JSON_PARSE_ERROR",
                "detalles", e.getMostSpecificCause() != null ? e.getMostSpecificCause().getMessage() : e.getMessage()
            ));
    }

    /**
     * Maneja excepciones genéricas - retorna JSON para AJAX, HTML para navegadores
     */
    @ExceptionHandler(Exception.class)
    public Object handleGenericException(Exception e, HttpServletRequest request) {
        logger.error("═══════════════════════════════════════════════════════════");
        logger.error("❌ EXCEPCIÓN GLOBAL NO MANEJADA");
        logger.error("═══════════════════════════════════════════════════════════");
        logger.error("URL: {}", request.getRequestURL());
        logger.error("Método: {}", request.getMethod());
        logger.error("Tipo: {}", e.getClass().getSimpleName());
        logger.error("Nombre completo: {}", e.getClass().getName());
        logger.error("Mensaje: {}", e.getMessage());
        if (e.getCause() != null) {
            logger.error("Causa: {}", e.getCause().getMessage());
        }
        logger.error("Stack trace:", e);
        logger.error("═══════════════════════════════════════════════════════════");

        // Si es una solicitud AJAX/JSON, retornar JSON
        String accept = request.getHeader("Accept");
        if (accept != null && (accept.contains("application/json") || request.getRequestURI().contains("/api/"))) {
            return ResponseEntity.status(HttpStatus.INTERNAL_SERVER_ERROR)
                .body(Map.of(
                    "error", "Error interno del servidor",
                    "tipo", e.getClass().getSimpleName(),
                    "mensaje", e.getMessage(),
                    "detalles", e.toString()
                ));
        }

        // Si es una solicitud normal, retornar HTML
        ModelAndView mav = new ModelAndView("error/error");
        mav.addObject("message", e.getMessage() != null ? e.getMessage() : "Ocurrió un error interno del servidor");
        mav.addObject("status", 500);
        return mav;
    }
}

