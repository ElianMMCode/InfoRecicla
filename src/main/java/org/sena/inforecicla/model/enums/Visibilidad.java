package org.sena.inforecicla.model.enums;

import lombok.AllArgsConstructor;
import lombok.Getter;

@Getter
@AllArgsConstructor
public enum Visibilidad {
    GLOBAL("Global"),
    ECA("ECA");

    private final String alcance;
}
