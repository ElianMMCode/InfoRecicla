package org.sena.inforecicla.model;

import jakarta.persistence.*;
import lombok.AllArgsConstructor;
import lombok.Getter;
import lombok.NoArgsConstructor;
import lombok.Setter;
import org.sena.inforecicla.model.base.EntidadCreacionModificacion;
import org.sena.inforecicla.model.enums.TipoPublicacion;

import java.util.UUID;

@Entity
@AllArgsConstructor
@NoArgsConstructor
@Getter
@Setter
@Table(name = "tb_guardados")
public class Guardados extends EntidadCreacionModificacion {

    @Id
    @GeneratedValue
    @Column(name = "guardados_id", unique = true, nullable=false)
    private UUID guardadoId;


    @Column(name="tipo", length = 15)
    @Enumerated(EnumType.STRING)
    private TipoPublicacion tipo;

    @ManyToOne
    @JoinColumn(name = "usuario_id")
    private Usuario usuario;
}
