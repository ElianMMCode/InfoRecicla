package org.sena.inforecicla.model.enums;

import lombok.AllArgsConstructor;
import lombok.Getter;

@Getter
@AllArgsConstructor
public enum EstadoPublicacion {

    BORRADOR("Borrador"),
    PUBLICADO("Publicado"),
    ARCHIVADO("Archivado");

    private String estadoPublicacion;

}
