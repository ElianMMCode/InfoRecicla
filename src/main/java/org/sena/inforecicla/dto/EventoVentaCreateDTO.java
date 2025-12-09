package org.sena.inforecicla.dto;

import com.fasterxml.jackson.annotation.JsonProperty;
import jakarta.validation.constraints.NotNull;
import jakarta.validation.constraints.NotBlank;
import lombok.AllArgsConstructor;
import lombok.Builder;
import lombok.Data;
import lombok.NoArgsConstructor;
import org.sena.inforecicla.model.enums.TipoRepeticion;

import java.time.LocalDateTime;
import java.util.UUID;

/**
 * DTO para crear o actualizar un evento de venta de material.
 *
 * Este DTO contiene toda la información necesaria para:
 * 1. Crear un evento base asociado a una venta
 * 2. Configurar la repetición automática
 * 3. Generar las instancias del evento
 */
@Data
@NoArgsConstructor
@AllArgsConstructor
@Builder
public class EventoVentaCreateDTO {

    // ===== INFORMACIÓN OBLIGATORIA =====

    @NotNull(message = "El ID de la venta de inventario es obligatorio")
    @JsonProperty("ventaInventarioId")
    private UUID ventaInventarioId;

    @NotNull(message = "El ID del material es obligatorio")
    @JsonProperty("materialId")
    private UUID materialId;

    @NotNull(message = "El ID del Punto ECA es obligatorio")
    @JsonProperty("puntoEcaId")
    private UUID puntoEcaId;

    @NotNull(message = "El ID del usuario es obligatorio")
    @JsonProperty("usuarioId")
    private UUID usuarioId;

    // ===== INFORMACIÓN DEL EVENTO =====

    @JsonProperty("centroAcopioId")
    private UUID centroAcopioId;

    @NotBlank(message = "El título del evento es obligatorio")
    @JsonProperty("titulo")
    private String titulo;

    @JsonProperty("descripcion")
    private String descripcion;

    @NotNull(message = "La fecha de inicio es obligatoria")
    @JsonProperty("fechaInicio")
    private LocalDateTime fechaInicio;

    @NotNull(message = "La fecha de fin es obligatoria")
    @JsonProperty("fechaFin")
    private LocalDateTime fechaFin;

    @JsonProperty("color")
    private String color;

    // ===== CONFIGURACIÓN DE REPETICIÓN =====

    @JsonProperty("tipoRepeticion")
    private TipoRepeticion tipoRepeticion;

    @JsonProperty("fechaFinRepeticion")
    private LocalDateTime fechaFinRepeticion;

    // ===== OBSERVACIONES =====

    @JsonProperty("observaciones")
    private String observaciones;
}

