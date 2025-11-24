package org.sena.inforecicla.model;

import jakarta.persistence.*;
import lombok.*;
import org.sena.inforecicla.model.base.EntidadDescripcion;

import java.util.List;
import java.util.UUID;

@Entity
@Table(name = "categoria_material")
@Getter
@Setter
@NoArgsConstructor
@AllArgsConstructor
@Builder
public class CategoriaMaterial extends EntidadDescripcion {

    @Id
    @GeneratedValue
    @Column(name = "categoria_material_id")
    private UUID ctgMtId;

    @OneToMany(mappedBy = "material_id")
    private List<Material> materiales;
}
