package org.sena.inforecicla.model;

import jakarta.persistence.*;
import jakarta.validation.constraints.NotNull;
import lombok.*;
import org.sena.inforecicla.model.base.EntidadDescripcion;

import java.util.List;
import java.util.UUID;

@Entity
@Table(name = "material")
@Getter
@Setter
@NoArgsConstructor
@AllArgsConstructor
@Builder
public class Material extends EntidadDescripcion {

    @Id
    @GeneratedValue
    @Column(nullable = false, updatable = false)
    private UUID materialId;

    @Column(name = "imgen_url")
    private String imagenUrl;

    @OneToMany(mappedBy = "material")
    private List<Inventario> inventario;

    @ManyToOne(fetch = FetchType.LAZY)
    @JoinColumn(
            name = "ctgMtId",
            nullable = false,
            foreignKey = @ForeignKey(name = "fk_material_categoria")
    )
    @NotNull
    private CategoriaMaterial ctgMaterial;

    @ManyToOne(fetch = FetchType.LAZY)
    @JoinColumn(
            name = "tipoMtId",
            nullable = false,
            foreignKey = @ForeignKey(name = "fk_material_tipo")
    )
    @NotNull
    private TipoMaterial tipoMaterial;
}
