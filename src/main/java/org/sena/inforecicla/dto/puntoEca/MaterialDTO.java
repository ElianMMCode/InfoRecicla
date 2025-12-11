package org.sena.inforecicla.dto.puntoEca;

import lombok.AllArgsConstructor;
import lombok.Builder;
import lombok.Data;
import lombok.NoArgsConstructor;

import java.util.UUID;

/**
 * DTO para listar materiales disponibles
 * Usado para Select2 en el buscador del mapa
 */
@Data
@NoArgsConstructor
@AllArgsConstructor
@Builder
public class MaterialDTO {
    private UUID materialId;
    private String nombre;
    private String categoria;
    private String tipo;
    private Integer puntosCantidad;  // Cantidad de puntos que tienen este material
}

