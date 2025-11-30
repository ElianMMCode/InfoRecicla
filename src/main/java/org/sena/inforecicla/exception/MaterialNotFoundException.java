package org.sena.inforecicla.exception;

import org.springframework.http.HttpStatus;
import org.springframework.web.bind.annotation.ResponseStatus;

@ResponseStatus(HttpStatus.NOT_FOUND)
public class MaterialNotFoundException extends Exception {
    public MaterialNotFoundException(String materialNoEncontrado) {
        super(materialNoEncontrado);
    }
}
