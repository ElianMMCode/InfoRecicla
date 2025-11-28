package org.sena.inforecicla.model.enums;

import lombok.AllArgsConstructor;
import lombok.Getter;

@Getter
public enum TipoCentroAcopio {
    PLANTA("Planta"),
    PROVEEDOR("Proveedor"),
    OTRO("OTRO");

    private final String tipo;

    TipoCentroAcopio(String tipo) {
        this.tipo = tipo;
    }/**

     * Buscar tipo de CntAcp exacto (insensible a mayúsculas).
     */
    public static TipoCentroAcopio porTipo(String tipo) {
        for (TipoCentroAcopio tpCntAcp : values()) {
            if (tpCntAcp.tipo.equals(tipo)) {
                return tpCntAcp;
            }
        }
        throw new IllegalArgumentException("Tipo de Centro de Acopio no válido: " + tipo);
    }

}
