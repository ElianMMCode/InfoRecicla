package org.sena.inforecicla.model.enums;

import lombok.*;

@Getter
public enum TipoUsuario {

    Admin("Administrador"),
    Ciudadano("Ciudadano"),
    GestorECA("GestorECA");

    private final String descripcion;

    TipoUsuario(String descripcion) {
        this.descripcion = descripcion;
    }
}
