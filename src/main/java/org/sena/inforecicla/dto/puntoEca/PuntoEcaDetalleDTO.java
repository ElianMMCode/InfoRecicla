package org.sena.inforecicla.dto.puntoEca;

import lombok.AllArgsConstructor;
import lombok.Builder;
import lombok.Data;
import lombok.NoArgsConstructor;

import java.util.List;
import java.util.UUID;

/**
 * DTO detallado para modal de punto ECA
 * Incluye información de materiales e inventario
 */
@Data
@NoArgsConstructor
@AllArgsConstructor
@Builder
public class PuntoEcaDetalleDTO {
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
    private String telefonoPunto;
    private List<MaterialInventarioDTO> materiales;

    @Data
    @NoArgsConstructor
    @AllArgsConstructor
    @Builder
    public static class MaterialInventarioDTO {
        private UUID inventarioId;
        private String nombreMaterial;
        private String categoriaMaterial;
        private String tipoMaterial;
        private Double stockActual;
        private Double capacidadMaxima;
        private String unidadMedida;
        private Double precioBuyPrice;
        private Double porcentajeCapacidad;
    }
}

