package org.sena.inforecicla.dto;

import com.fasterxml.jackson.annotation.JsonProperty;
import jakarta.validation.constraints.NotBlank;
import jakarta.validation.constraints.Size;
import lombok.AllArgsConstructor;
import lombok.Builder;
import lombok.Data;
import lombok.NoArgsConstructor;

/**
 * DTO para crear un nuevo Centro de Acopio
 */
@Data
@NoArgsConstructor
@AllArgsConstructor
@Builder
public class CentroAcopioCreateDTO {

    @JsonProperty("nombreCntAcp")
    @NotBlank(message = "El nombre del centro es obligatorio")
    @Size(min = 3, max = 30, message = "El nombre debe tener entre 3 y 30 caracteres")
    private String nombreCntAcp;

    @JsonProperty("tipoCntAcp")
    @NotBlank(message = "El tipo de centro es obligatorio")
    private String tipoCntAcp;

    @JsonProperty("celular")
    private String celular;

    @JsonProperty("email")
    private String email;

    @JsonProperty("nombreContactoCntAcp")
    @Size(min = 3, max = 30, message = "El nombre del contacto debe tener entre 3 y 30 caracteres")
    private String nombreContactoCntAcp;

    @JsonProperty("nota")
    @Size(max = 500, message = "La nota no puede exceder 500 caracteres")
    private String nota;

    @JsonProperty("descripcion")
    @Size(max = 500, message = "La descripción no puede exceder 500 caracteres")
    private String descripcion;

    @JsonProperty("visibilidad")
    private String visibilidad;

    @JsonProperty("localidadId")
    @NotBlank(message = "La localidad es obligatoria")
    private String localidadId;

    @JsonProperty("ciudad")
    private String ciudad;

    @JsonProperty("latitud")
    private Double latitud;

    @JsonProperty("longitud")
    private Double longitud;
}

