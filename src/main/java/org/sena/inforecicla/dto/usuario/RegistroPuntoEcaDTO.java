package org.sena.inforecicla.dto.usuario;

import jakarta.validation.constraints.*;
import org.sena.inforecicla.model.enums.TipoDocumento;

public record RegistroPuntoEcaDTO(

        @NotBlank(message = "El nombre de la institución es obligatorio")
        @Size(min = 5, max = 100, message = "El nombre debe tener entre 5 y 100 caracteres")
        String nombres,

        @NotBlank(message = "El contacto es obligatorio")
        @Size(min = 3, max = 30, message = "El nombre del contacto debe tener entre 3 y 30 caracteres")
        String apellidos,

        @NotBlank(message = "El email es obligatorio")
        @Email(message = "El email debe ser válido")
        String email,

        @NotBlank(message = "El teléfono es obligatorio")
        @Pattern(
                regexp = "^3\\d{9}$",
                message = "El teléfono debe iniciar con 3 y tener 10 dígitos"
        )
        String celular,

        @NotBlank(message = "La contraseña es obligatoria")
        @Size(min = 8, max = 60, message = "La contraseña debe tener entre 8 y 60 caracteres")
        @Pattern(
                regexp = "^(?=.*[a-z])(?=.*[A-Z])(?=.*\\d)(?=.*[@$!%*?&]).+$",
                message = "Debe incluir mayúscula, minúscula, número y símbolo"
        )
        String password,

        @NotBlank(message = "Confirma tu contraseña")
        String passwordConfirm,

        TipoDocumento tipoDocumento,

        @Size(min = 6, max = 20, message = "El NIT/documento debe tener entre 6 y 20 caracteres")
        String numeroDocumento,

        @NotBlank(message = "La dirección es obligatoria")
        @Size(min = 10, max = 100, message = "La dirección debe tener entre 10 y 100 caracteres")
        String direccion,

        @NotBlank(message = "La ciudad es obligatoria")
        String ciudad,

        @NotBlank(message = "La localidad es obligatoria")
        String localidad,

        @DecimalMin(value = "-90.0", message = "Latitud inválida")
        @DecimalMax(value = "90.0", message = "Latitud inválida")
        Double latitud,

        @DecimalMin(value = "-180.0", message = "Longitud inválida")
        @DecimalMax(value = "180.0", message = "Longitud inválida")
        Double longitud,

        @Size(max = 500, message = "La descripción no debe exceder 500 caracteres")
        String descripcion

) {
}

