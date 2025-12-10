package org.sena.inforecicla.dto.usuario;

import jakarta.validation.constraints.*;
import org.sena.inforecicla.model.enums.TipoDocumento;

public record RegistroCiudadanoDTO(

        @NotBlank(message = "El nombre es obligatorio")
        @Size(min = 3, max = 30, message = "El nombre debe tener entre 3 y 30 caracteres")
        String nombres,

        @NotBlank(message = "El apellido es obligatorio")
        @Size(min = 2, max = 40, message = "El apellido debe tener entre 2 y 40 caracteres")
        String apellidos,

        @NotBlank(message = "El email es obligatorio")
        @Email(message = "El email debe ser válido")
        String email,

        @NotBlank(message = "El celular es obligatorio")
        @Pattern(
                regexp = "^3\\d{9}$",
                message = "El celular debe iniciar con 3 y tener 10 dígitos"
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

        @Size(min = 6, max = 20, message = "El documento debe tener entre 6 y 20 caracteres")
        String numeroDocumento,

        @Pattern(
                regexp = "^(19|20)\\d{2}-(0[1-9]|1[0-2])-(0[1-9]|[12]\\d|3[01])$",
                message = "La fecha debe ser YYYY-MM-DD"
        )
        String fechaNacimiento,

        @NotBlank(message = "La ciudad es obligatoria")
        String ciudad,

        @NotBlank(message = "La localidad es obligatoria")
        String localidad

) {
}

