package org.sena.inforecicla.model;

import jakarta.persistence.*;
import lombok.*;
import org.sena.inforecicla.model.base.EntidadCreacionModificacion;

import java.util.UUID;

@Entity
@Table(name = "categorias_publicacion")
@Getter
@Setter
@NoArgsConstructor
@AllArgsConstructor
@Builder
public class CategoriaPublicacion extends EntidadCreacionModificacion {

    @Id
    @GeneratedValue(generator = "UUID")
    @Column(name = "categoria_publicacion_id", unique = true, nullable = false)
    private UUID categoriaPublicacionId;

    @Column(name = "nombre", nullable = false, length = 100)
    private String nombre;

    @Column(name = "descripcion", length = 500)
    private String descripcion;
}