package org.sena.inforecicla.model.enums;

import lombok.*;

@AllArgsConstructor
@Getter
public enum EstadoPublicacion {
    Borrador("Borrador"),
    Publicado("Publicado"),
    Archivado("Archivado");

    private final String descripcion;
}