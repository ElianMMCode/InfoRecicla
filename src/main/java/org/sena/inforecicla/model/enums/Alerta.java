package org.sena.inforecicla.model.enums;

import lombok.AllArgsConstructor;
import lombok.Getter;

@Getter
@AllArgsConstructor
public enum Alerta {

    OK("Ninguna"),
    Alerta("Alerta"),
    Critico("Critico");

    private final String tipo;
}
