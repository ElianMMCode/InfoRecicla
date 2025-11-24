package org.sena.inforecicla.model.enums;

import lombok.AllArgsConstructor;
import lombok.Getter;

@Getter
@AllArgsConstructor
public enum UnidadMedida {

    KG("Kilogramo"),
    UNI("Unidad"),
    T("Tonelada"),
    M3("Metro Cubico");

    private final String tipo;
}
