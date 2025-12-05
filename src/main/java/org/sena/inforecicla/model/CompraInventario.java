package org.sena.inforecicla.model;

import jakarta.persistence.*;
import jakarta.validation.constraints.NotNull;
import lombok.*;
import org.sena.inforecicla.model.base.EntidadCreacionModificacion;

import java.math.BigDecimal;
import java.time.LocalDateTime;
import java.util.UUID;

@Entity
@Table(name = "compra_inventario")
@Getter
@Setter
@NoArgsConstructor
@AllArgsConstructor
@Builder
public class CompraInventario extends EntidadCreacionModificacion {

    @Id
    @GeneratedValue
    @Column(nullable = false, updatable = false)
    private UUID compraId;

    @ManyToOne(fetch = FetchType.LAZY)
    @JoinColumn(
            name = "inventario_id",
            foreignKey = @ForeignKey(name = "fk_inventario_compra")
    )
    private Inventario inventario;

    @NotNull(message = "La fecha de compra es obligatoria")
    private LocalDateTime fechaCompra;

    @NotNull
    @Column(precision = 12, scale = 2, nullable = false)
    private BigDecimal cantidad;

    @Column(precision = 12, scale = 2)
    private BigDecimal precioCompra;


    @Column(length = 500)
    private String observaciones;

}
