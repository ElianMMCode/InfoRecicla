package org.sena.inforecicla.model;

import jakarta.persistence.*;
import jakarta.validation.constraints.NotNull;
import lombok.AllArgsConstructor;
import lombok.Builder;
import lombok.Data;
import lombok.NoArgsConstructor;
import org.hibernate.annotations.CreationTimestamp;

import java.time.LocalDateTime;
import java.util.UUID;

/**
 * Entidad que representa una instancia específica de un evento que se repite.
 *
 * Si un evento tiene repetición semanal, se generará una EventoInstancia por cada semana
 * hasta la fecha de fin de repetición.
 *
 * Para eventos sin repetición, también habrá una EventoInstancia.
 */
@Entity
@Table(
    name = "evento_instancia",
    indexes = {
        @Index(name = "idx_evento_base", columnList = "evento_base_id"),
        @Index(name = "idx_fecha_inicio", columnList = "fecha_inicio"),
        @Index(name = "idx_punto_eca_usuario", columnList = "punto_eca_id, usuario_id")
    }
)
@Data
@NoArgsConstructor
@AllArgsConstructor
@Builder
public class EventoInstancia {

    @Id
    @GeneratedValue(strategy = GenerationType.UUID)
    @Column(nullable = false, updatable = false)
    private UUID instanciaId;

    // ===== RELACIÓN CON EVENTO BASE =====

    @NotNull(message = "La instancia debe estar asociada a un evento base")
    @ManyToOne(fetch = FetchType.LAZY)
    @JoinColumn(
        name = "evento_base_id",
        nullable = false,
        foreignKey = @ForeignKey(name = "fk_instancia_evento")
    )
    private Evento eventoBase;

    // ===== RELACIONES PARA SEGURIDAD Y FILTRADO =====

    @NotNull(message = "La instancia debe estar asociada a un Punto ECA")
    @ManyToOne(fetch = FetchType.LAZY)
    @JoinColumn(
        name = "punto_eca_id",
        nullable = false,
        foreignKey = @ForeignKey(name = "fk_instancia_punto_eca")
    )
    private PuntoECA puntoEca;

    @NotNull(message = "La instancia debe estar asociada a un usuario")
    @ManyToOne(fetch = FetchType.LAZY)
    @JoinColumn(
        name = "usuario_id",
        nullable = false,
        foreignKey = @ForeignKey(name = "fk_instancia_usuario")
    )
    private Usuario usuario;

    // ===== INFORMACIÓN DE LA INSTANCIA =====

    @NotNull(message = "La fecha de inicio es obligatoria")
    @Column(nullable = false, name = "fecha_inicio")
    private LocalDateTime fechaInicio;

    @NotNull(message = "La fecha de fin es obligatoria")
    @Column(nullable = false, name = "fecha_fin")
    private LocalDateTime fechaFin;

    @Column(name = "numero_repeticion")
    private Integer numeroRepeticion;

    @Column(name = "es_completado")
    private Boolean esCompletado = false;

    @Column(name = "completado_en")
    private LocalDateTime completadoEn;

    @Column(name = "observaciones", columnDefinition = "TEXT")
    private String observaciones;

    // ===== AUDITORÍA =====

    @CreationTimestamp
    @Column(name = "created_at", nullable = false, updatable = false)
    private LocalDateTime createdAt;

    // ===== MÉTODOS HELPER =====

    /**
     * Marca esta instancia como completada.
     */
    public void marcarCompletada() {
        this.esCompletado = true;
        this.completadoEn = LocalDateTime.now();
    }

    /**
     * Marca esta instancia como no completada.
     */
    public void desmarcarCompletada() {
        this.esCompletado = false;
        this.completadoEn = null;
    }

    /**
     * Obtiene el número de días desde la creación.
     */
    public long getDiasDesdeCreacion() {
        return java.time.temporal.ChronoUnit.DAYS.between(createdAt, LocalDateTime.now());
    }
}

