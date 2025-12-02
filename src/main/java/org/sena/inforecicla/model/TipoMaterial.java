package org.sena.inforecicla.model;

import jakarta.persistence.*;
import lombok.*;
import org.sena.inforecicla.model.base.EntidadDescripcion;

import java.util.List;
import java.util.UUID;

@Entity
@Table(name = "tipo_material")
@Getter
@Setter
@NoArgsConstructor
@AllArgsConstructor
@Builder
public class TipoMaterial extends EntidadDescripcion {

    @Id
    @GeneratedValue
    @Column(name="tipo_material_id",nullable = false, updatable = false)
    private UUID tipoMtId;

    @OneToMany(mappedBy = "tipoMaterial")
    private List<Material> materiales;
}
