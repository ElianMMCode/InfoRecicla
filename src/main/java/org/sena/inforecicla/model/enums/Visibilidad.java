package org.sena.inforecicla.model.enums;

import lombok.AllArgsConstructor;
import lombok.Getter;

@Getter
public enum Visibilidad {
    GLOBAL("Global"),
    ECA("ECA");

    private final String alcance;

    Visibilidad(String alcance) {
        this.alcance = alcance;
    }
}
