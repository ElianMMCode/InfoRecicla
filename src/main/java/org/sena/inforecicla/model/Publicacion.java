package org.sena.inforecicla.model;

import jakarta.persistence.*;
import jakarta.validation.constraints.*;
import lombok.*;
import org.sena.inforecicla.model.base.EntidadDescripcion;
import org.sena.inforecicla.model.enums.Estado;

import java.util.Set;
import java.util.UUID;

@Entity
@Table(name = "publicacion")
@Getter
@Setter
@NoArgsConstructor
@AllArgsConstructor
@Builder
public class Publicacion extends EntidadDescripcion {
    @Id
    @GeneratedValue
    @Column(nullable = false, updatable = false)
    private UUID publicacionId;

    @Column(name = "titulo", nullable = false, length = 50)
    @NotBlank(message = "El título no puede estar vacío")
    @Size(min = 3, max = 50)
    private String titulo;

    @Lob
    @Column(name = "contenido", nullable = false)
    @NotBlank(message = "El contenido no puede estar vacío")
    private String contenido;

    @Enumerated(EnumType.STRING)
    @Column(name = "estado", nullable = false)
    @NotNull(message = "El estado es obligatorio")
    private Estado estado;

    @OneToOne(fetch = FetchType.LAZY)
    @JoinColumn(
            name = "usuario_id",
            nullable = false,
            foreignKey = @ForeignKey(name = "fk_publicacion_usuario")
    )
    @NotNull(message = "El usuario es obligatorio")
    private Usuario usuario;

    @ManyToMany(fetch = FetchType.LAZY)
    @JoinTable(
            name = "publicacion_categoria",
            joinColumns = @JoinColumn(name = "publicacion_id", foreignKey = @ForeignKey(name = "fk_pub_cat_publicacion")),
            inverseJoinColumns = @JoinColumn(name = "categoria_id", foreignKey = @ForeignKey(name = "fk_pub_cat_categoria"))
    )
    @NotEmpty(message = "Debe tener al menos una categoría")
    private Set<CategoriaPublicacion> categorias;
}
