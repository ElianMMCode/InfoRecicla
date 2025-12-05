package org.sena.inforecicla.model;

import jakarta.persistence.*;
import lombok.*;
import org.sena.inforecicla.model.base.EntidadCreacionModificacion;

import java.util.ArrayList;
import java.util.List;
import java.util.UUID;

@Entity
@AllArgsConstructor
@NoArgsConstructor
@Getter
@Setter
@ToString
@Table(name = "tb_etiqueta")
public class Etiquetas extends EntidadCreacionModificacion {

    @Id
    @GeneratedValue
    @Column(name = "etiqueta_id", unique = true, nullable=false)
    private UUID etiquetaId;

    @Column(name = "nombre", nullable = false, length = 80)
    private String nombre;

    @Column(name = "descripcion", nullable = true, length = 300)
    private String descripcion;


    @ManyToMany(mappedBy = "etiquetas")
    private List<Publicacion> publicaciones = new ArrayList<>();
}
