package org.sena.inforecicla.model.enums;

import lombok.AllArgsConstructor;
import lombok.Getter;

@Getter
public enum LocalidadBogota {

    USAQUEN("Usaquén"),
    CHAPINERO("Chapinero"),
    SANTA_FE("Santa Fe"),
    SAN_CRISTOBAL("San Cristóbal"),
    USME("Usme"),
    TUNJUELITO("Tunjuelito"),
    BOSA("Bosa"),
    KENNEDY("Kennedy"),
    FONTIBON("Fontibón"),
    ENGATIVA("Engativá"),
    SUBA("Suba"),
    BARRIOS_UNIDOS("Barrios Unidos"),
    TEUSAQUILLO("Teusaquillo"),
    MARTIRES("Los Mártires"),
    ANTONIO_NARINO("Antonio Nariño"),
    PUENTE_ARANDA("Puente Aranda"),
    CANDELARIA("La Candelaria"),
    RAFAEL_URIBE("Rafael Uribe Uribe"),
    CIUDAD_BOLIVAR("Ciudad Bolívar"),
    SUMAPAZ("Sumapaz");

    private final String nombre;

    LocalidadBogota(String nombre) {
        this.nombre = nombre;
    }

    /**
     * Buscar localidad por nombre exacto (insensible a mayúsculas).
     */
    public static LocalidadBogota porLocalidad(String nombre) {
        for (LocalidadBogota loc : values()) {
            if (loc.nombre.equalsIgnoreCase(nombre)) {
                return loc;
            }
        }
        throw new IllegalArgumentException("Localidad no válida: " + nombre);
    }

    /**
     * Buscar por código (el nombre del enum: USAQUEN, BOSA, SUBA...)
     */
    public static LocalidadBogota fromCodigo(String codigo) {
        try {
            return LocalidadBogota.valueOf(codigo.toUpperCase());
        } catch (Exception e) {
            throw new IllegalArgumentException("Código de localidad no válido: " + codigo);
        }
    }
}
