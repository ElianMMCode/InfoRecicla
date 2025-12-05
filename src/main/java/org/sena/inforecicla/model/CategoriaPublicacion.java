package org.sena.inforecicla.model;

import java.util.List;
import java.util.UUID;

import org.sena.inforecicla.model.base.EntidadDescripcion;

import jakarta.persistence.Column;
import jakarta.persistence.Entity;
import jakarta.persistence.GeneratedValue;
import jakarta.persistence.Id;
import jakarta.persistence.OneToMany;
import jakarta.persistence.Table;
import lombok.AllArgsConstructor;
import lombok.Builder;
import lombok.Getter;
import lombok.NoArgsConstructor;
import lombok.Setter;

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