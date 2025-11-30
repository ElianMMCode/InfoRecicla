package org.sena.inforecicla.model;

import jakarta.persistence.*;
import jakarta.validation.constraints.Max;
import jakarta.validation.constraints.Min;
import jakarta.validation.constraints.NotNull;
import lombok.*;
import org.sena.inforecicla.model.base.EntidadCreacionModificacion;
import org.sena.inforecicla.model.enums.UnidadMedida;

import java.math.BigDecimal;
import java.util.List;
import java.util.UUID;

@Entity
@Table(name = "inventario")
@Getter
@Setter
@Builder
@NoArgsConstructor
@AllArgsConstructor
public class Inventario extends EntidadCreacionModificacion {

    @Id
    @GeneratedValue
    @Column(name = "inventario_id")
    private UUID inventarioId;

    @NotNull
    @Column(name = "capacidad_maxima", precision = 12, scale = 2, nullable = false)
    private BigDecimal capacidadMaxima;

    @NotNull
    @Enumerated(EnumType.STRING)
    @Column(length = 4)
    private UnidadMedida unidadMedida;

    @NotNull
    @Column(name = "stock_actual", precision = 12, scale = 2, nullable = false)
    private BigDecimal stockActual;

    @NotNull
    @Column(name = "umbral_alerta", columnDefinition = "TINYINT", nullable = false)
    @Min(0)
    @Max(100)
    private Short umbralAlerta;

    @Column(name = "umbral_critico", columnDefinition = "TINYINT", nullable = false)
    @Min(0)
    @Max(100)
    private Short umbralCritico;

    @Column(name = "precio_compra", precision = 12, scale = 2)
    private BigDecimal precioCompra;

    @Column(name = "precio_venta", precision = 12, scale = 2)
    private BigDecimal precioVenta;

    @ManyToOne(fetch = FetchType.LAZY)
    @JoinColumn(
            name = "material_id",
            nullable = false,
            foreignKey = @ForeignKey(name = "fk_inventario_material")
    )
    @NotNull
    private Material material;

    @ManyToOne(fetch = FetchType.LAZY)
    @JoinColumn(
            name = "punto_id",
            nullable = false,
            foreignKey = @ForeignKey(name = "fk_puntoEca_inventario")
    )
    @NotNull
    private PuntoECA puntoEca;

    @OneToMany(mappedBy = "inventario", cascade = CascadeType.REMOVE)
    @Column(nullable = false)
    private List<CompraInventario> compras;

    @OneToMany(mappedBy = "inventario", cascade = CascadeType.REMOVE)
    @Column(nullable = false)
    private List<VentaInventario> ventas;



}
