package org.sena.inforecicla.model;

import jakarta.persistence.*;
import jakarta.validation.constraints.*;
import lombok.*;
import org.hibernate.annotations.CreationTimestamp;
import org.hibernate.annotations.UpdateTimestamp;
import org.sena.inforecicla.model.base.EntidadLocalizacionWebHorario;
import org.sena.inforecicla.model.enums.Estado;

import java.time.LocalDateTime;
import java.util.List;
import java.util.UUID;

@Entity
@Table(name = "punto_eca")
@Getter
@Setter
@NoArgsConstructor
@AllArgsConstructor
@Builder
@AttributeOverrides({
        @AttributeOverride(name = "celular", column = @Column(name = "celular_punto", length = 10, unique = true)),
        @AttributeOverride(name = "email", column = @Column(name = "email_punto", unique = true, length = 150)),
        @AttributeOverride(name = "sitioWeb", column = @Column(name = "sitio_web_punto")),
        @AttributeOverride(name = "horarioAtencion", column = @Column(name = "horario_atencion_punto", length = 150))
})
public class PuntoECA extends EntidadLocalizacionWebHorario {

    @Id
    @GeneratedValue
    @Column(name = "gestor_id", updatable = false, nullable = false)
    private UUID gestorId;

    @Column(name = "nombre_punto", nullable = false, length = 30)
    @NotBlank
    @Size(min = 3, max = 30)
    private String nombrePunto;

    @Column(length = 500)
    private String descripcion;


    @Pattern(
            regexp = "^60\\d{8}$",
            message = "El teléfono fijo debe tener el formato 60 + indicativo + 7 dígitos (ej: 6012345678)"
    )
    @Column(name = "telefono_punto", length = 10, unique = true)
    private String telefonoPunto;


    @Column(length = 150)
    private String direccion;

    @Column(name = "logo_url_punto")
    private String logoUrlPunto;

    @Column(name = "foto_url_punto")
    private String fotoUrlPunto;

    @NotNull
    @Enumerated(EnumType.STRING)
    @Column(nullable = false, length = 15)
    private Estado estado;

    @CreationTimestamp
    @Column(name = "fecha_creacion", nullable = false, updatable = false)
    private LocalDateTime fechaCreacion;

    @UpdateTimestamp
    @Column(name = "fecha_actualizacion", nullable = false)
    private LocalDateTime fechaActualizacion;

    @OneToOne
    @MapsId
    @JoinColumn(name = "gestor_id",nullable = false, foreignKey = @ForeignKey(name = "fk_puntoeca_gestor"))
    private Usuario usuario;

    @OneToMany(mappedBy = "puntoEca", cascade = {CascadeType.MERGE, CascadeType.REMOVE}, orphanRemoval = true)
    private List<CentroAcopio> cntAcps;

}