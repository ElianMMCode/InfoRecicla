package org.sena.inforecicla.dto.puntoEca.gestor;

import jakarta.validation.constraints.*;

import java.util.UUID;

public record PuntoEcaUpdateDTO(

        // Campos del Punto ECA - EN EL MISMO ORDEN QUE EL CONTROLADOR
        @NotBlank(message = "El nombre del punto es obligatorio")
        @Size(min = 3, max = 30, message = "El nombre debe tener entre 3 y 30 caracteres")
        String nombrePunto,

        @Size(max = 500, message = "La descripción no puede exceder 500 caracteres")
        String descripcionPunto,

        // Teléfono es opcional - solo valida si se proporciona
        @Pattern(
                regexp = "^$|^60\\d{8}$",
                message = "El teléfono fijo debe tener el formato 60 + 8 dígitos (ej: 6012345678) o estar vacío"
        )
        String telefonoPunto,

        @NotBlank(message = "El celular es obligatorio")
        @Size(min = 10, max = 10, message = "El celular debe tener exactamente 10 dígitos")
        String celularPunto,

        @NotBlank(message = "El email es obligatorio")
        @Email(message = "El email debe tener un formato válido")
        String emailPunto,

        @NotBlank(message = "La dirección es obligatoria")
        @Size(max = 150, message = "La dirección no puede exceder 150 caracteres")
        String direccionPunto,

        @NotNull(message = "La localidad es obligatoria")
        UUID localidadPuntoId,

        @DecimalMin(value = "-90.0", message = "Latitud mínima permitida: -90.0")
        @DecimalMax(value = "90.0", message = "Latitud máxima permitida: 90.0")
        Double latitud,

        @DecimalMin(value = "-180.0", message = "Longitud mínima permitida: -180.0")
        @DecimalMax(value = "180.0", message = "Longitud máxima permitida: 180.0")
        Double longitud,

        // Sitio web es opcional
        String sitioWebPunto,

        @Size(max = 150, message = "El horario no puede exceder 150 caracteres")
        String horarioAtencionPunto

) {
}
