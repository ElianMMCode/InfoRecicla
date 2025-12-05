package org.sena.inforecicla.model.enums;

import lombok.AllArgsConstructor;
import lombok.Getter;

@Getter
@AllArgsConstructor

public enum TipoPublicacion {

    Publicacion("Publicacion"),
    PuntoEca("Punto_Eca");

    private String tipo;

}
