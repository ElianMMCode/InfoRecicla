package org.sena.inforecicla.model.base;

import jakarta.persistence.Column;
import jakarta.persistence.MappedSuperclass;
import jakarta.persistence.EnumType;
import jakarta.persistence.Enumerated;
import jakarta.validation.constraints.DecimalMax;
import jakarta.validation.constraints.DecimalMin;
import jakarta.validation.constraints.NotNull;
import lombok.Getter;
import lombok.Setter;
import org.sena.inforecicla.model.enums.LocalidadBogota;

@MappedSuperclass
@Getter
@Setter
public abstract class EntidadLocalizacion extends EntidadContacto {

    @Column(length = 15,nullable = false)
    private String ciudad;

    @NotNull(message = "Debe escoger una localidad")
    @Enumerated(EnumType.STRING)
    @Column(name = "localidad", nullable = false, length = 20)
    private LocalidadBogota localidad;

    @DecimalMin(value = "-90.0", message = "Latitud mínima permitida: -90.0")
    @DecimalMax(value = "90.0", message = "Latitud máxima permitida: 90.0")
    private Double latitud;

    @DecimalMin(value = "-180.0", message = "Longitud mínima permitida: -180.0")
    @DecimalMax(value = "180.0", message = "Longitud máxima permitida: 180.0")
    private Double longitud;

}

