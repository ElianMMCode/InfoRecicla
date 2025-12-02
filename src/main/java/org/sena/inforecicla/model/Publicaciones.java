package org.sena.inforecicla.model;

import jakarta.persistence.*;
import lombok.AllArgsConstructor;
import lombok.Getter;
import lombok.NoArgsConstructor;
import lombok.Setter;
import org.sena.inforecicla.model.base.EntidadCreacionModificacion;
import org.sena.inforecicla.model.enums.Estado;

import java.util.ArrayList;
import java.util.List;
import java.util.UUID;

@Entity
@AllArgsConstructor
@NoArgsConstructor
@Getter
@Setter
@Table(name = "tb_publicaciones")
public class Publicaciones extends EntidadCreacionModificacion {

    @Id
    @GeneratedValue
    @Column(name = "publicaciones_id", unique = true, nullable=false)
    private UUID publicacionesId;

    @Column(name = "tiulo", nullable = false, length = 200)
    private String titulo;

    @Column(name="contenido", columnDefinition = "TEXT", nullable = false )
    private String contenido;

    @Column(name="estado_publicacion", length = 15, nullable = false)
    @Enumerated(EnumType.STRING)
    private Estado estadoPublicacion;

    @ManyToOne
    @JoinColumn(name = "categoria_id", nullable = false)
    private CategoriaPublicaciones categoria;


    //llave foranea tabla usuarios
    @ManyToOne
    @JoinColumn(name = "usuario_id", nullable = false)
    private Usuario usuarioId;

    @ManyToMany(cascade = {CascadeType.PERSIST, CascadeType.MERGE})
    @JoinTable(
            name = "tb_publicacion_etiquetas",
            joinColumns = @JoinColumn(name = "publicacion_id"),
            inverseJoinColumns = @JoinColumn(name = "etiqueta_id")
    )
    private List<Etiquetas> etiquetas = new ArrayList<>();


    @OneToMany(mappedBy = "publicacion", cascade = CascadeType.ALL, orphanRemoval = true)
    private List<PublicacionesMultimedia> multimedia = new ArrayList<>();

    @OneToMany(mappedBy = "publicacion", cascade = CascadeType.ALL, orphanRemoval = true)
    private List<Votos> votosRecibidos;
}
