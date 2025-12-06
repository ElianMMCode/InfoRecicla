package org.sena.inforecicla.exception;

import org.springframework.http.HttpStatus;
import org.springframework.web.bind.annotation.ResponseStatus;

@ResponseStatus(HttpStatus.NOT_FOUND)
public class CategoriaPublicacionNotFoundException extends RuntimeException {

    public CategoriaPublicacionNotFoundException(String message) {
         super(message);
    }

    public CategoriaPublicacionNotFoundException(String message, Throwable cause) {
        super(message, cause);
    }
}