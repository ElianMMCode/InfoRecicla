package org.sena.inforecicla.model.base;

import jakarta.persistence.Column;
import jakarta.persistence.MappedSuperclass;
import lombok.Getter;
import lombok.Setter;

@MappedSuperclass
@Getter
@Setter
public abstract class EntidadLocalizacionWebHorario extends EntidadLocalizacion {

    @Column(name = "sitio_web")
    private String sitioWeb;

    @Column(name = "horario_atencion", length = 150)
    private String horarioAtencion;

}

