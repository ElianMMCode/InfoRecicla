package org.sena.inforecicla.exception;

public class InventarioNotFoundException extends Throwable {

    //Excepción para cuando no es encuentra el registro de un inventario en la BBDD
    public InventarioNotFoundException(String mensage){
        super(mensage);
    }
}
