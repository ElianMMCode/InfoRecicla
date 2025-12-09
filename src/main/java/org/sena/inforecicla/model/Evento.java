package org.sena.inforecicla.model;

import jakarta.persistence.*;
import jakarta.validation.constraints.NotNull;
import lombok.AllArgsConstructor;
import lombok.Builder;
import lombok.Data;
import lombok.NoArgsConstructor;
import org.hibernate.annotations.CreationTimestamp;
import org.hibernate.annotations.UpdateTimestamp;
import org.sena.inforecicla.model.enums.TipoRepeticion;

import java.time.LocalDateTime;
import java.util.List;
import java.util.UUID;

/**
 * Entidad que representa un evento de calendario.
 * Los eventos se crean automáticamente a partir de VentaInventario.
 * Pueden tener repetición semanal, quincenal o mensual.
 */
@Entity
@Table(
    name = "evento",
    uniqueConstraints = @UniqueConstraint(columnNames = {"venta_inventario_id", "punto_eca_id"})
)
@Data
@NoArgsConstructor
@AllArgsConstructor
@Builder
public class Evento {

    @Id
    @GeneratedValue(strategy = GenerationType.UUID)
    @Column(nullable = false, updatable = false)
    private UUID eventoId;

    // ===== RELACIONES CON DATOS FUENTE =====

    @NotNull(message = "El evento debe estar asociado a una venta de inventario")
    @ManyToOne(fetch = FetchType.LAZY)
    @JoinColumn(
        name = "venta_inventario_id",
        nullable = false,
        foreignKey = @ForeignKey(name = "fk_evento_venta_inventario")
    )
    private VentaInventario ventaInventario;

    @NotNull(message = "El evento debe estar asociado a un material")
    @ManyToOne(fetch = FetchType.LAZY)
    @JoinColumn(
        name = "material_id",
        nullable = false,
        foreignKey = @ForeignKey(name = "fk_evento_material")
    )
    private Material material;

    @ManyToOne(fetch = FetchType.LAZY)
    @JoinColumn(
        name = "centro_acopio_id",
        foreignKey = @ForeignKey(name = "fk_evento_centro_acopio")
    )
    private CentroAcopio centroAcopio;

    @NotNull(message = "El evento debe estar asociado a un Punto ECA")
    @ManyToOne(fetch = FetchType.LAZY)
    @JoinColumn(
        name = "punto_eca_id",
        nullable = false,
        foreignKey = @ForeignKey(name = "fk_evento_punto_eca")
    )
    private PuntoECA puntoEca;

    @NotNull(message = "El evento debe estar asociado a un usuario")
    @ManyToOne(fetch = FetchType.LAZY)
    @JoinColumn(
        name = "usuario_id",
        nullable = false,
        foreignKey = @ForeignKey(name = "fk_evento_usuario")
    )
    private Usuario usuario;

    // ===== INFORMACIÓN DE CALENDARIO =====

    @Column(nullable = false, length = 255)
    private String titulo;

    @Column(columnDefinition = "TEXT")
    private String descripcion;

    @NotNull(message = "La fecha de inicio es obligatoria")
    @Column(nullable = false, name = "fecha_inicio")
    private LocalDateTime fechaInicio;

    @NotNull(message = "La fecha de fin es obligatoria")
    @Column(nullable = false, name = "fecha_fin")
    private LocalDateTime fechaFin;

    @Column(length = 7)
    private String color;

    // ===== CONFIGURACIÓN DE REPETICIÓN =====

    @Enumerated(EnumType.STRING)
    @Column(name = "tipo_repeticion", length = 15)
    private TipoRepeticion tipoRepeticion;

    @Column(name = "fecha_fin_repeticion")
    private LocalDateTime fechaFinRepeticion;

    @Column(name = "es_evento_generado")
    private Boolean esEventoGenerado = false;

    // ===== INSTANCIAS DE EVENTOS GENERADOS =====

    @OneToMany(
        mappedBy = "eventoBase",
        cascade = CascadeType.ALL,
        orphanRemoval = true,
        fetch = FetchType.LAZY
    )
    private List<EventoInstancia> instancias;

    // ===== AUDITORÍA =====

    @CreationTimestamp
    @Column(name = "created_at", nullable = false, updatable = false)
    private LocalDateTime createdAt;

    @UpdateTimestamp
    @Column(name = "updated_at")
    private LocalDateTime updatedAt;

    // ===== MÉTODOS HELPER =====

    /**
     * Obtiene el título para mostrar en el calendario.
     * Formato: "[Material] - Venta"
     */
    public void generarTitulo() {
        if (this.material != null) {
            this.titulo = this.material.getDescripcion() + " - Venta";
        }
    }

    /**
     * Obtiene la descripción del evento.
     * Incluye información del material y centro de acopio.
     */
    public void generarDescripcion() {
        StringBuilder desc = new StringBuilder();
        desc.append("Material: ").append(this.material.getDescripcion()).append("\n");
        if (this.centroAcopio != null) {
            desc.append("Centro de Acopio: ").append(this.centroAcopio.getNombreCntAcp()).append("\n");
        }
        if (this.ventaInventario != null && this.ventaInventario.getObservaciones() != null) {
            desc.append("Observaciones: ").append(this.ventaInventario.getObservaciones());
        }
        this.descripcion = desc.toString();
    }

    /**
     * Determina el color según el tipo de material.
     */
    public void asignarColorPorMaterial() {
        if (this.material != null && this.material.getTipoMaterial() != null) {
            // Los colores pueden ser personalizados según el tipo de material
            // Por defecto usa verde de Inforecicla
            this.color = "#28a745";
        }
    }
}
