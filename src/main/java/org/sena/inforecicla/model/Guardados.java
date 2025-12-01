package org.sena.inforecicla.model;

import jakarta.persistence.*;
import lombok.AllArgsConstructor;
import lombok.Getter;
import lombok.NoArgsConstructor;
import lombok.Setter;
import org.sena.inforecicla.model.base.CreacionModificacionPublicaciones;
import org.sena.inforecicla.model.enums.TipoPublicacion;

import java.io.Serializable;
import java.time.LocalDateTime;
import java.util.UUID;

@Entity
@AllArgsConstructor
@NoArgsConstructor
@Getter
@Setter
@Table(name = "tb_guardados")
public class Guardados extends CreacionModificacionPublicaciones {

    @Id
    @Column(name = "guardados_id", unique = true, nullable=false)
    private UUID guardadoId;

    //usuario_id llave foranea a tabla usuario

    @Column(name="tipo", length = 15)
    @Enumerated(EnumType.STRING)
    private TipoPublicacion tipo;

    //referencia_id llave foranea

    //llave foranea tabla usuarios
    @ManyToOne
    @JoinColumn(name = "usuario_id")
    private Usuario usuario;
}
