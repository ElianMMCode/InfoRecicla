package org.sena.inforecicla.model.enums;

import lombok.AllArgsConstructor;
import lombok.Getter;

@Getter
public enum UnidadMedida {

    KG("Kilogramo"),
    UNI("Unidad"),
    T("Tonelada"),
    M3("Metro Cubico");

    private final String nombre;

    UnidadMedida(String nombre) {
        this.nombre = nombre;
    }
}
