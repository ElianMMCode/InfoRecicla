package org.sena.inforecicla.exception;

import org.springframework.http.HttpStatus;
import org.springframework.web.bind.annotation.ControllerAdvice;
import org.springframework.web.bind.annotation.ExceptionHandler;
import org.springframework.web.bind.annotation.ResponseStatus;
import org.springframework.web.servlet.ModelAndView;

/**
 * Manejador global de excepciones para la aplicación
 */
@ControllerAdvice
public class GlobalExceptionHandler {

    /**
     * Maneja errores 403 (Acceso Denegado)
     */
    @ExceptionHandler(Exception.class)
    @ResponseStatus(HttpStatus.INTERNAL_SERVER_ERROR)
    public ModelAndView handleException(Exception ex) {
        ModelAndView mav = new ModelAndView("error/error");
        mav.addObject("message", "Ocurrió un error interno del servidor");
        mav.addObject("status", 500);
        return mav;
    }
}

