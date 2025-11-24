package org.sena.inforecicla.model.base;

import jakarta.persistence.Column;
import jakarta.persistence.MappedSuperclass;
import jakarta.validation.constraints.Email;
import jakarta.validation.constraints.NotBlank;
import jakarta.validation.constraints.Pattern;
import lombok.Getter;
import lombok.Setter;

@MappedSuperclass
@Getter
@Setter
public abstract class EntidadContacto extends EntidadCreacionModificacion {

    @NotBlank(message = "El número de celular es obligatorio")
    @Pattern(
            regexp = "^3\\d{9}$",
            message = "El número de celular debe iniciar con 3 y tener 10 dígitos"
    )
    @Column(length = 10)
    private String celular;

    @NotBlank(message = "El correo es obligatorio")
    @Email(message = "Debe ingresar un correo válido")
    @Column(length = 150)
    private String email;

}

