package org.sena.inforecicla.model;

import jakarta.persistence.*;
import jakarta.validation.constraints.*;
import lombok.*;
import org.sena.inforecicla.model.base.EntidadLocalizacionWebHorario;
import org.sena.inforecicla.model.enums.Estado;

import java.util.List;
import java.util.UUID;

@Entity
@Table(name = "publicaciones")
@Getter
@Setter
@NoArgsConstructor
@AllArgsConstructor
@Builder
public class Publicacion {
    @Id
    @GeneratedValue(Strategy = GenerationType IDENTITY)
    @Column(name = "publicacion_id", unique = true, nullable = false)
    private UUID publicacionId;

    @ManyToOne(fetch = FetchType.LAZY)
    @JoinColumn(
            name = "usuario_id",
            nullable = false,
            foreignKey = @ForeignKey(name = "fk_publicacion_usuario")
    )
    @NotNull(message = "El usuario es obligatorio")
    private Usuario usuario;

    @Column(name = "usuario_id", insertable = false, updatable = false")
    private UUID usuarioId;

    @Column(name = "categoria_id", insertable = false, updatable = false")
    private UUID categoriaId;

    @Column(name = "titulo", nullable = false, length = 50)
    @NotBlank(message = "El ttulo no puede estar vacío")
    @Size(min = 3, max = 50)
    private String titulo;

    @Lob
    @Column(name = "contenido", nullable = false)
    @NotBlank(message = "El contenido no puede estar vacío")
    private String contenido;

    @Enumerated(EnumType.STRING)
    @Column(name = "estado", nullable = false, columnDefinition = "ENUM('borrador','publicado','archivado')")
    @NotNull(message = "El estado es obligatorio")
    private EstadoPublicacion estado;

    @ManyToOne(fetch = FetchType.LAZY)
    @JoinColumn(
            name = "categoria_id",
            foreignKey = @ForeignKey(name = "fk_publicacion_categoria")
    )
    private CategoriaPublicacion categoria;

    @CreationTimestamp
    @Column(name = "creado", nullable = false, updatable = false)
    private LocalDateTime creado;

    @UpdateTimestamp
    @Column(name = "actualizado", nullable = false)
    private LocalDateTime actualizado;
}

    @OneToOne
    @JoinColumn(name = "usuario_id", nullable = false, foreignKey = @ForeignKey(name = "fk_puntoeca_gestor"))
    private Usuario usuario;

    @OneToOne
    @JoinColumn(name = "categoria_id", nullable = false, foreignKey = @ForeignKey(name = "fk_puntoeca_gestor"))
    private Categoria categoria;


}

