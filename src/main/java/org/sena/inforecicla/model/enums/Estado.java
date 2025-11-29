package org.sena.inforecicla.model.enums;

import lombok.*;

@AllArgsConstructor
@Getter
public enum Estado {

    Activo("Activo"),
    Suspendido("Suspendido"),
    Inactivo("Inactivo"),
    Bloqueado("Bloqueado");

    private final String descripcion;
}
