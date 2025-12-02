package org.sena.inforecicla.model;

import jakarta.persistence.*;
import lombok.AllArgsConstructor;
import lombok.Getter;
import lombok.NoArgsConstructor;
import lombok.Setter;
import org.sena.inforecicla.model.base.EntidadCreacionModificacion;
import org.sena.inforecicla.model.enums.TipoPublicacion;
import org.sena.inforecicla.model.enums.Valor;

import java.util.UUID;

@Entity
@AllArgsConstructor
@NoArgsConstructor
@Getter
@Setter
@Table(name = "tb_votos")
public class Votos extends EntidadCreacionModificacion {

    @Id
    @Column(name = "votos_id", unique = true, nullable=false)
    private UUID votosId;

    @Column(name="tipo_publicacion", length = 15)
    @Enumerated(EnumType.STRING)
    private TipoPublicacion tipoPublicacion;



    @Column(name="valor", length = 15)
    @Enumerated(EnumType.STRING)
    private Valor valor;

    @ManyToOne
    @JoinColumn(name = "usuario_id", nullable = false)
    private Usuario usuario;

    @ManyToOne(fetch = FetchType.LAZY)
    @JoinColumn(name = "publicacion_id", nullable = false)
    private Publicaciones publicacion;
}
