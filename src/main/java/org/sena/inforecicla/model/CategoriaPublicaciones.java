package org.sena.inforecicla.model;

import jakarta.persistence.*;
import lombok.AllArgsConstructor;
import lombok.Getter;
import lombok.NoArgsConstructor;
import lombok.Setter;

import java.io.Serializable;
import java.util.List;
import java.util.UUID;

@Entity
@AllArgsConstructor
@NoArgsConstructor
@Getter
@Setter
@Table(name="tb_categoria_publicaciones")
public class CategoriaPublicaciones {


    @Id
    @GeneratedValue
    @Column(name = "categorias_publicaciones_id", unique = true, nullable=false)
    private UUID catePublId;

    @Column(name = "nombre", nullable = false, length = 30)
    private String nombre;

    @Column(name = "descripcion", nullable = false, length = 300)
    private String descripcion;

    @OneToMany(mappedBy = "categoria")
    private List<Publicaciones> publicaciones;
}
