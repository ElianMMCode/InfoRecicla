package org.sena.inforecicla.model;

import jakarta.persistence.*;
import jakarta.validation.constraints.Pattern;
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
    @Column(name = "venta_id")
    private UUID ventaId;

    @ManyToOne(fetch = FetchType.LAZY)
    @JoinColumn(
            name = "inventario_id",
            foreignKey = @ForeignKey(name = "fk_inventario_venta")
    )
    private Inventario inventario;

    @Pattern(
            regexp = "^(19|20)\\d{2}-(0[1-9]|1[0-2])-(0[1-9]|[12]\\d|3[01])$",
            message = "La fecha debe tener un formato válido YYYY-MM-DD"
    )
    private LocalDateTime fechaVenta;

    @Column(precision = 12, scale = 2)
    private BigDecimal precioVenta;

    @Column(length = 500)
    private String observaciones;


    @OneToOne
    @JoinColumn(name = "centro_acopio_id", foreignKey = @ForeignKey(name = "fk_venta_centroAcopio") )
    private CentroAcopio ctrAcopio;
}
