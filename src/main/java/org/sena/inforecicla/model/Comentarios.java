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
@Table(name = "tb_comentarios")
public class Comentarios extends EntidadCreacionModificacion {

    @Id
    @GeneratedValue
    @Column(name = "comentarios_id", unique = true, nullable=false)
    private UUID comentariosId;

    @Column(name="tipo", length = 15)
    @Enumerated(EnumType.STRING)
    private TipoPublicacion tipo;



    @Column(name="texto", columnDefinition = "TEXT", nullable = false )
    private String texto;


    @ManyToOne
    @JoinColumn(name = "usuario_id")
    private Usuario usuario;

}
