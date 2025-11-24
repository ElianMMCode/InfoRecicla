package org.sena.inforecicla.model.enums;

import lombok.*;

@Getter
@AllArgsConstructor
public enum TipoUsuario {

    Admin("Administrador"),
    Ciudadano("Ciudadano"),
    GestorECA("GestorECA");

    private final String descripcion;
}
