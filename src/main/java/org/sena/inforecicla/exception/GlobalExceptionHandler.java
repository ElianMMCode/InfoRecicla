package org.sena.inforecicla.exception;

import org.springframework.ui.Model;
import org.springframework.web.bind.annotation.ControllerAdvice;
import org.springframework.web.bind.annotation.ExceptionHandler;

@ControllerAdvice
public class GlobalExceptionHandler {

    @ExceptionHandler(InventarioNotFoundException.class)
    public String manejarInventarioNoEncontrado(
            InventarioNotFoundException ex,
            Model model
    ) {
        model.addAttribute("error", ex.getMessage());
        return "error/custom-error"; // vista donde muestras el mensaje
    }

}
