package org.sena.inforecicla.model;

import jakarta.persistence.*;
import lombok.*;
import org.sena.inforecicla.model.base.EntidadDescripcion;

import java.util.Set;
import java.util.UUID;

@Entity
@Table(name = "categoria_publicacion")
@Getter
@Setter
@NoArgsConstructor
@AllArgsConstructor
@Builder
public class CategoriaPublicacion extends EntidadDescripcion {

    @Id
    @GeneratedValue
    @Column(nullable = false, updatable = false)
    private UUID categoiraPublicacionId;

    @ManyToMany(mappedBy = "categorias", fetch = FetchType.LAZY)
    private Set<Publicacion> publicaciones;
}