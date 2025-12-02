package org.sena.inforecicla.exception;


import org.springframework.http.HttpStatus;
import org.springframework.web.bind.annotation.ResponseStatus;

@ResponseStatus(HttpStatus.NOT_FOUND)
public class InventarioNotFoundException extends Throwable {

    //Excepción para cuando no es encuentra el registro de un inventario en la BBDD
    public InventarioNotFoundException(String mensage){
        super(mensage);
    }
}
