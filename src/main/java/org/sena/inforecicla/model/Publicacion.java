package org.sena.inforecicla.model;

import jakarta.persistence.*;
import lombok.*;
import jakarta.validation.constraints.*;
import jakarta.persistence.GeneratedValue;

import org.sena.inforecicla.model.base.EntidadDescripcion;
import org.sena.inforecicla.model.enums.EstadoPublicacion;

import java.util.UUID;

@Entity
@Table(name = "publicaciones")
@Getter
@Setter
@NoArgsConstructor
@AllArgsConstructor
@Builder
public class Publicacion extends EntidadDescripcion {
        @Id
        @GeneratedValue(generator = "UUID")
        @Column(name = "publicacion_id", unique = true, nullable = false)
        private UUID publicacionId;

        @Column(name = "titulo", nullable = false, length = 200)
        @NotBlank(message = "El ttulo no puede estar vacío")
        @Size(min = 3, max = 200)
        private String titulo;

        @Lob
        @Column(name = "contenido", nullable = false, columnDefinition = "TEXT")
        @NotBlank(message = "El contenido no puede estar vacío")
        private String contenido;

        @Enumerated(EnumType.STRING)
        @Column(name = "estado", nullable = false, columnDefinition = "ENUM('Borrador','Publicado','Archivado')")
        @NotNull(message = "El estado es obligatorio")
        private EstadoPublicacion estado;

        @Column(name = "usuario_id", insertable = false, updatable = false)
        private UUID usuarioId;

        @ManyToOne(fetch = FetchType.LAZY)
        @JoinColumn(name = "usuario_id", nullable = false, foreignKey = @ForeignKey(name = "fk_publicacion_usuario"))
        @NotNull(message = "El usuario es obligatorio")
        private Usuario usuario;

        @Column(name = "categoria_id", insertable = false, updatable = false)
        private UUID categoriaId;

        @ManyToOne(fetch = FetchType.LAZY)
        @JoinColumn(name = "categoria_publicacion_id", nullable = false, foreignKey = @ForeignKey(name = "fk_publicacion_categoria"))
        @NotNull
        private CategoriaPublicacion categoriaPublicacion;
}