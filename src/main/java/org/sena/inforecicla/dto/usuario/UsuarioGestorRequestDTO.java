package org.sena.inforecicla.dto.usuario;

import jakarta.validation.constraints.*;
import org.sena.inforecicla.model.enums.TipoDocumento;
import org.sena.inforecicla.model.enums.TipoUsuario;

public record UsuarioGestorRequestDTO(

        @NotBlank
        @Size(min = 3, max = 30)
        String nombres,

        @NotBlank
        @Size(min = 2, max = 40)
        String apellidos,

        @NotBlank
        @Size(min = 8, max = 60)
        @Pattern(
                regexp = "^(?=.*[a-z])(?=.*[A-Z])(?=.*\\d)(?=.*[@$!%*?&]).+$",
                message = "Debe incluir mayúscula, minúscula, número y símbolo"
        )
        String password,

        @NotNull
        TipoUsuario tipoUsuario,

        TipoDocumento tipoDocumento,

        @Size(min = 6, max = 20)
        String numeroDocumento,

        @Pattern(
                regexp = "^(19|20)\\d{2}-(0[1-9]|1[0-2])-(0[1-9]|[12]\\d|3[01])$",
                message = "Formato válido YYYY-MM-DD"
        )
        String fechaNacimiento,

        @NotBlank
        @Size(min = 10, max = 10)
        String celular,

        @NotBlank
        @Email
        String email,

        @NotBlank
        String ciudad,

        @NotBlank
        String localidad,

        String fotoPerfil,

        @Size(max = 500)
        String biografia,

        // Campos del Punto ECA
        @NotBlank
        @Size(min = 3, max = 30)
        String nombrePunto,

        @Size(max = 500)
        String descripcionPunto,

        @Pattern(
                regexp = "^60\\d{8}$",
                message = "El teléfono fijo debe tener el formato 60 + indicativo + 7 dígitos (ej: 6012345678)"
        )
        String telefonoPunto,

        @NotBlank
        @Size(min = 10, max = 10)
        String celularPunto,

        @NotBlank
        @Email
        String emailPunto,

        @Size(max = 150)
        String direccionPunto,

        @NotBlank
        String ciudadPunto,

        @NotBlank
        String localidadPunto,

        @Size(max = 50)
        String coordenadasPunto,

        String logoUrlPunto,

        String fotoUrlPunto,

        String sitioWebPunto,

        @Size(max = 150)
        String horarioAtencionPunto

) {}
