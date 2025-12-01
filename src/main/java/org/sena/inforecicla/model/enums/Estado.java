package org.sena.inforecicla.model.enums;

import lombok.AllArgsConstructor;
import lombok.Getter;

@Getter
@AllArgsConstructor
public enum Estado {

    Activo("Activo"),
    Suspendido("Suspendido"),
    Inactivo("Inactivo"),
    Bloqueado("Bloqueado");

    private final String descripcion;
}
