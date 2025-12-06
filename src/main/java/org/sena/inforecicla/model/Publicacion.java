package org.sena.inforecicla.model;

import jakarta.persistence.*;
import lombok.*;
import jakarta.validation.constraints.*;
import org.sena.inforecicla.model.base.EntidadCreacionModificacion;
import org.sena.inforecicla.model.enums.EstadoPublicacion;

import java.util.List;
import java.util.UUID;

@Entity
@Table(name = "publicaciones")
@Getter
@Setter
@NoArgsConstructor
@AllArgsConstructor
@Builder
public class Publicacion extends EntidadCreacionModificacion {

    @Id
    @GeneratedValue(generator = "UUID")
    @Column(name = "publicacion_id", unique = true, nullable = false)
    private UUID publicacionId;

    @Column(name = "titulo", nullable = false, length = 200)
    @NotBlank(message = "El título no puede estar vacío")
    @Size(min = 3, max = 200)
    private String titulo;

    @Column(name = "contenido", nullable = false, columnDefinition = "LONGTEXT")
    @NotBlank(message = "El contenido no puede estar vacío")
    private String contenido;

    @Enumerated(EnumType.STRING)
    @Column(name = "estado_publicacion", nullable = false)  // ¡IMPORTANTE! Nombre correcto
    private EstadoPublicacion estadoPublicacion;

    @ManyToOne(fetch = FetchType.LAZY)
    @JoinColumn(name = "usuario_id", nullable = false,
            foreignKey = @ForeignKey(name = "fk_publicacion_usuario"))
    @NotNull(message = "El usuario es obligatorio")
    private Usuario usuario;

    @ManyToOne(fetch = FetchType.LAZY)
    @JoinColumn(name = "categoria_publicacion_id", nullable = false,
            foreignKey = @ForeignKey(name = "fk_publicacion_categoria"))
    @NotNull
    private CategoriaPublicacion categoriaPublicacion;

    @ManyToMany
    @JoinTable(
            name = "publicacion_etiquetas",
            joinColumns = @JoinColumn(name = "publicacion_id"),
            inverseJoinColumns = @JoinColumn(name = "etiqueta_id")
    )
    private List<Etiquetas> etiquetas;
}