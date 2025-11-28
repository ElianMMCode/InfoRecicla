package org.sena.inforecicla.model.enums;

import lombok.*;

@Getter
public enum Estado {

    Activo("Activo"),
    Suspendido("Suspendido"),
    Inactivo("Inactivo"),
    Bloqueado("Bloqueado");

    private final String descripcion;
    
    Estado(String descripcion) {
        this.descripcion = descripcion;
    }
}
