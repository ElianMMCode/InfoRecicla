package org.sena.inforecicla.model;

import jakarta.persistence.*;
import lombok.AllArgsConstructor;
import lombok.Getter;
import lombok.NoArgsConstructor;
import lombok.Setter;
import org.sena.inforecicla.model.base.EntidadCreacionModificacion;
import org.sena.inforecicla.model.enums.TipoMultimedia;

import java.util.UUID;

@Entity
@AllArgsConstructor
@NoArgsConstructor
@Getter
@Setter
@Table(name = "tb_publicaciones_multimedia")
public class PublicacionesMultimedia extends EntidadCreacionModificacion {

    @Id
    @GeneratedValue
    @Column(name = "publicaciones_multimedia_id", unique = true, nullable=false)
    private UUID publicacionesMultimediaId;

    @Column(name="tipo_multimedia", length = 15)
    @Enumerated(EnumType.STRING)
    private TipoMultimedia tipoMultimedia;

    @Column(name = "url", nullable = false, length = 400)
    private String url;

    @Column(name = "tiulo", nullable = false, length = 150)
    private String titulo;

    @Column(name = "descripcion", nullable = true, length = 500)
    private String descripcion;

    @ManyToOne
    @JoinColumn(name = "publicacion_id", nullable = false)
    private Publicacion publicacion;

}
