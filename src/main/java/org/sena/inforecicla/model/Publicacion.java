package org.sena.inforecicla.model;

import jakarta.persistence.*;
import jakarta.validation.constraints.*;
import lombok.*;
import org.sena.inforecicla.model.base.EntidadLocalizacionWebHorario;
import org.sena.inforecicla.model.enums.Estado;

import java.util.List;
import java.util.UUID;

@Entity
@Table(name = "publicacion")
@Getter
@Setter
@NoArgsConstructor
@AllArgsConstructor
@Builder
public class Publicacion extends EntidadDescription {
    @Id
    @GeneratedValue(Strategy = GenerationType IDENTITY)
    @Column(name = "publicacion_id", unique = true, nullable = false)
    private UUID publicacionId;

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

    @CreationTimestamp
    @Column(name = "creado", nullable = false, updatable = false)
    private LocalDateTime creado;

    @UpdateTimestamp
    @Column(name = "actualizado", nullable = false)
    private LocalDateTime actualizado;

    @OneToMany(mappedBy = "publicacion")
    private List<Publicacion> publicaciones;

    @Column(name = "usuario_id", insertable = false, updatable = false")
    private UUID usuarioId;

    @ManyToOne(fetch = FetchType.LAZY)
    @JoinColumn(
            name = "usuario_id",
            nullable = false,
            foreignKey = @ForeignKey(name = "fk_publicacion_usuario")
    )
    @NotNull(message = "El usuario es obligatorio")
    private Usuario usuario;

    @Column(name = "categoria_id", insertable = false, updatable = false")
    private UUID categoriaId;

    @OneToOne
    @JoinColumn(name = "usuario_id", nullable = false, foreignKey = @ForeignKey(name = "fk_puntoeca_gestor"))
    private Usuario usuario;

    @ManyToOne(fetch = FetchType.LAZY)
    @JoinColumn(
            name = "categoria_publicacion_id", nullable =false,
            foreignKey = @ForeignKey(name = "fk_publicacion_categoria")
    )
    @NotNull
    private CategoriaPublicacion categoriaPublicacion;
}