package org.sena.inforecicla.model;

import jakarta.persistence.*;
import lombok.*;

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
    @Column(name = "categoiraPublicacionId", unique = true, nullable = false)
    private UUID categoiraPublicacionId;

    @OneToMany(mappedBy = "ctgPublicacion")
    private List<Publicacion> publicaciones;
}