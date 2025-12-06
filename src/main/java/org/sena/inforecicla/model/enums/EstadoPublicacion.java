package org.sena.inforecicla.model.enums;

public enum EstadoPublicacion {
    BORRADOR("Borrador"),
    PUBLICADO("Publicado"),
    ARCHIVADO("Archivado"),
    ELIMINADO("Eliminado");

    private final String descripcion;

    EstadoPublicacion(String descripcion) {
        this.descripcion = descripcion;
    }

    public String getDescripcion() {
        return descripcion;
    }
}