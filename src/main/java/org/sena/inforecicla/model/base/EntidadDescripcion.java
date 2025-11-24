package org.sena.inforecicla.model.base;

import jakarta.persistence.Column;
import jakarta.persistence.MappedSuperclass;
import jakarta.validation.constraints.NotNull;
import lombok.Getter;
import lombok.Setter;


@MappedSuperclass
@Getter
@Setter
public abstract class EntidadDescripcion extends EntidadCreacionModificacion {

    @NotNull
    @Column(nullable = false, length = 30)
    private String nombre;

    @NotNull
    @Column(nullable = false, length = 500)
    private String descripcion;

}
