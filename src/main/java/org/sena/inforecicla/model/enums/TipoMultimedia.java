package org.sena.inforecicla.model.enums;

import lombok.AllArgsConstructor;
import lombok.Getter;

@Getter
@AllArgsConstructor
public enum TipoMultimedia {

    IMAGEN("Imagen"),
    VIDEO("Video"),
    DOCUMENTO("Documento"),
    ENLACE("Enlace");

    private String tipoMultimedia;
}
