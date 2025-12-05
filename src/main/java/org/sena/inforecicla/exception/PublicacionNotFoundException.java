package org.sena.inforecicla.exception;

import org.springframework.http.HttpStatus;
import org.springframework.web.bind.annotation.ResponseStatus;

@ResponseStatus(HttpStatus.NOT_FOUND)
public class PublicacionNotFoundException extends RuntimeException {

    public PublicacionNotFoundException(String message) {
        super(message);
    }

    public PublicacionNotFoundException(String message, Throwable cause) {
        super(message, cause);
    }
}
