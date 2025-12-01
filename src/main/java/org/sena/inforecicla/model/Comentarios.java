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
@Table(name = "tb_comentarios")
public class Comentarios extends CreacionModificacionPublicaciones {

    @Id
    @Column(name = "comentarios_id", unique = true, nullable=false)
    private UUID comentariosId;

    @Column(name="tipo", length = 15)
    @Enumerated(EnumType.STRING)
    private TipoPublicacion tipo;

    // referencia_id campo de llave foranea

    // usuario_id capmpo de llave foranea

    @Column(name="texto", columnDefinition = "TEXT", nullable = false )
    private String texto;

    //llave foranea tabla de Usuarios
    @ManyToOne
    @JoinColumn(name = "usuario_id")
    private Usuario usuario;

}
