package org.sena.inforecicla.dto.puntoEca;

import lombok.AllArgsConstructor;
import lombok.Builder;
import lombok.Data;
import lombok.NoArgsConstructor;

import java.util.UUID;

/**
 * DTO para exponer información de Puntos ECA en vistas de mapa
 * Contiene solo información pública relevante para visualización en mapa
 */
@Data
@NoArgsConstructor
@AllArgsConstructor
@Builder
public class PuntoEcaMapDTO {
    private UUID puntoEcaID;
    private String nombrePunto;
    private Double latitud;
    private Double longitud;
    private String direccion;
    private String ciudad;
    private String localidadNombre;
    private String celular;
    private String email;
    private String descripcion;
    private String horarioAtencion;
}

