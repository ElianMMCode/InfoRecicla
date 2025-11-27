package org.sena.inforecicla.model.enums;

import lombok.AllArgsConstructor;
import lombok.Getter;

@Getter
@AllArgsConstructor
public enum TipoDocumento {

    CC("Cédula de ciudadanía"),
    TI("Tarjeta de identidad"),
    RC("Registro civil"),
    CE("Cédula de extranjería"),
    PA("Pasaporte"),
    NIT("Número de Identificación Tributaria"),
    PPT("Permiso por Protección Temporal"),
    SC("Salvoconducto"),
    DIE("Documento de Identidad Extranjero");

    private final String descripcion;

    public static TipoDocumento porCodigo(String codigo) {
        for (TipoDocumento tipo : values()) {
            if (tipo.name().equalsIgnoreCase(codigo)) {
                return tipo;
            }
        }
        throw new IllegalArgumentException("Tipo de documento no válido: " + codigo);
    }
}
