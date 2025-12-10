/**
 * ARCHIVO EJEMPLO: DTO para Eventos del Calendario
 * Ubicación: src/main/java/org/sena/inforecicla/dto/EventoCalendarioDTO.java
 *
 * Copia este código en la ubicación indicada
 */

package org.sena.inforecicla.dto;

import com.fasterxml.jackson.annotation.JsonProperty;
import lombok.AllArgsConstructor;
import lombok.Builder;
import lombok.Data;
import lombok.NoArgsConstructor;
import org.sena.inforecicla.model.enums.TipoRepeticion;

import java.time.LocalDateTime;
import java.util.UUID;

/**
 * DTO para eventos del calendario compatible con FullCalendar.
 * Utilizado para devolver eventos a través de la API REST.
 *
 * Este DTO representa una instancia específica de un evento en el calendario.
 */
@Data
@NoArgsConstructor
@AllArgsConstructor
@Builder
public class EventoCalendarioDTO {

    // ===== PROPIEDADES REQUERIDAS POR FULLCALENDAR =====

    @JsonProperty("id")
    private UUID instanciaId;

    @JsonProperty("title")
    private String titulo;

    @JsonProperty("start")
    private String fechaInicio;

    @JsonProperty("end")
    private String fechaFin;

    @JsonProperty("backgroundColor")
    private String backgroundColor;

    @JsonProperty("borderColor")
    private String borderColor;

    @JsonProperty("textColor")
    private String textColor;

    @JsonProperty("description")
    private String descripcion;

    // ===== PROPIEDADES ADICIONALES =====

    @JsonProperty("materialId")
    private UUID materialId;

    @JsonProperty("materialNombre")
    private String materialNombre;

    @JsonProperty("centroAcopioId")
    private UUID centroAcopioId;

    @JsonProperty("centroAcopioNombre")
    private String centroAcopioNombre;

    @JsonProperty("ventaId")
    private UUID ventaInventarioId;

    @JsonProperty("eventoBaseId")
    private UUID eventoBaseId;

    @JsonProperty("tipoRepeticion")
    private String tipoRepeticion;

    @JsonProperty("numeroRepeticion")
    private Integer numeroRepeticion;

    @JsonProperty("esCompletado")
    private Boolean esCompletado;

    @JsonProperty("completadoEn")
    private String completadoEn;

    // ===== INFORMACIÓN DE SEGURIDAD =====

    @JsonProperty("puntoEcaId")
    private UUID puntoEcaId;

    @JsonProperty("usuarioId")
    private UUID usuarioId;

    // ===== MÉTODOS HELPER =====

    /**
     * Constructor simplificado para crear DTOs básicos.
     */
    public EventoCalendarioDTO(UUID instanciaId, String titulo, String fechaInicio, String fechaFin, String color) {
        this.instanciaId = instanciaId;
        this.titulo = titulo;
        this.fechaInicio = fechaInicio;
        this.fechaFin = fechaFin;
        this.backgroundColor = color;
        this.borderColor = color;
        this.textColor = "#ffffff";
    }

    /**
     * Obtiene la descripción completa del evento.
     */
    public String getDescripcionCompleta() {
        StringBuilder desc = new StringBuilder();
        desc.append(titulo).append("\n\n");

        if (materialNombre != null) {
            desc.append("Material: ").append(materialNombre).append("\n");
        }

        if (centroAcopioNombre != null) {
            desc.append("Centro de Acopio: ").append(centroAcopioNombre).append("\n");
        }

        if (tipoRepeticion != null) {
            desc.append("Repetición: ").append(tipoRepeticion).append("\n");
        }

        if (descripcion != null) {
            desc.append("\nDetalles: ").append(descripcion);
        }

        return desc.toString();
    }
}

