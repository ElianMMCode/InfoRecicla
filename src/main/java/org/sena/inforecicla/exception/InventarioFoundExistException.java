package org.sena.inforecicla.exception;

import org.springframework.http.HttpStatus;
import org.springframework.web.bind.annotation.ResponseStatus;

/**
 * Excepción lanzada cuando se intenta agregar un material que ya existe en el inventario
 * del punto ECA o cuando el inventario ya contiene ese material.
 */
@ResponseStatus(HttpStatus.NOT_FOUND)
public class InventarioFoundExistException extends Exception {

    public InventarioFoundExistException(String mensaje) {
        super(mensaje);
    }

}
