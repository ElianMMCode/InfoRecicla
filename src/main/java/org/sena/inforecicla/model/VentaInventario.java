package org.sena.inforecicla.model;

import jakarta.persistence.*;
import jakarta.validation.constraints.NotNull;
import lombok.*;
import org.sena.inforecicla.model.base.EntidadCreacionModificacion;

import java.math.BigDecimal;
import java.time.LocalDateTime;
import java.util.UUID;

@Entity
@Table(name = "venta_inventario")
@Getter
@Setter
@NoArgsConstructor
@AllArgsConstructor
@Builder
public class VentaInventario extends EntidadCreacionModificacion {

    @Id
    @GeneratedValue
    @Column(nullable = false, updatable = false)
    private UUID ventaId;

    @ManyToOne(fetch = FetchType.LAZY)
    @JoinColumn(
            name = "inventario_id",
            foreignKey = @ForeignKey(name = "fk_inventario_venta")
    )
    private Inventario inventario;

    @NotNull(message = "La fecha de venta es obligatoria")
    private LocalDateTime fechaVenta;

    @Column(precision = 12, scale = 2)
    private BigDecimal precioVenta;

    @NotNull
    @Column(precision = 12, scale = 2, nullable = false)
    private BigDecimal cantidad;

    @Column(length = 500)
    private String observaciones;

    @ManyToOne(fetch = FetchType.LAZY)
    @JoinColumn(name = "centro_acopio_id", foreignKey = @ForeignKey(name = "fk_venta_centroAcopio"))
    private CentroAcopio ctrAcopio;
}
