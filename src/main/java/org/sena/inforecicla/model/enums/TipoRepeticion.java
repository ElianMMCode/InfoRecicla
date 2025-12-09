package org.sena.inforecicla.model.enums;

import lombok.Getter;

/**
 * Enum que define los tipos de repetición de eventos en el calendario.
 */
@Getter
public enum TipoRepeticion {
    SEMANAL("Semanal", "Se repite cada 7 días", 7),
    QUINCENAL("Quincenal", "Se repite cada 14 días", 14),
    MENSUAL("Mensual", "Se repite cada mes (30 días)", 30),
    SIN_REPETICION("Sin repetición", "Evento único, no se repite", 0);

    private final String nombre;
    private final String descripcion;
    private final int diasIntervalo;

    TipoRepeticion(String nombre, String descripcion, int diasIntervalo) {
        this.nombre = nombre;
        this.descripcion = descripcion;
        this.diasIntervalo = diasIntervalo;
    }

    /**
     * Obtiene el intervalo en días para este tipo de repetición.
     * @return Número de días entre repeticiones
     */
    public int getIntervaloDias() {
        return diasIntervalo;
    }

    /**
     * Verifica si este tipo tiene repetición.
     * @return true si el tipo es diferente a SIN_REPETICION
     */
    public boolean tieneRepeticion() {
        return this != SIN_REPETICION;
    }
}

