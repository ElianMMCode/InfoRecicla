package org.sena.inforecicla.dto.puntoEca.gestor;

import jakarta.validation.constraints.*;
import org.sena.inforecicla.model.enums.TipoDocumento;

public record GestorUpdateDTO(

        @NotBlank
        @Size(min = 3, max = 30)
        String nombres,

        @NotBlank
        @Size(min = 2, max = 40)
        String apellidos,

        @NotNull
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

        //String fotoPerfil,

        @Size(max = 500)
        String biografia

) {}
