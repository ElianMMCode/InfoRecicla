package org.sena.inforecicla.model;

import jakarta.persistence.*;
import jakarta.validation.constraints.*;
import lombok.*;
import org.sena.inforecicla.model.base.EntidadLocalizacionWebHorario;
import org.sena.inforecicla.model.enums.Alerta;
import org.sena.inforecicla.model.enums.Estado;

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
    @Column(name = "punto_id")
    private UUID puntoEcaID;

    @Column(name = "nombre_punto", nullable = false, length = 30)
    @NotBlank
    @Size(min = 3, max = 30)
    private String nombrePunto;

    @Column(length = 500)
    private String descripcion;

    @Column(name = "gestor_id", insertable = false, updatable = false)
    private UUID gestorId;


    @Pattern(
            regexp = "^60\\d{8}$",
            message = "El teléfono fijo debe tener el formato 60 + indicativo + 7 dígitos (ej: 6012345678)"
    )
    @Column(name = "telefono_punto", length = 10, unique = true)
    private String telefonoPunto;


    @Column(length = 150)
    private String direccion;

    @Column(name = "coordenadas", length = 50)
    private String coordenadas;

    @Column(name = "logo_url_punto")
    private String logoUrlPunto;

    @Column(name = "foto_url_punto")
    private String fotoUrlPunto;

    @NotNull
    @Enumerated(EnumType.STRING)
    @Column(nullable = false, length = 15)
    private Estado estado;

    @OneToOne
    @JoinColumn(name = "gestor_id", nullable = false, foreignKey = @ForeignKey(name = "fk_puntoeca_gestor"))
    private Usuario usuario;

    @OneToMany(mappedBy = "puntoEca", cascade = {CascadeType.MERGE, CascadeType.REMOVE}, orphanRemoval = true)
    private List<CentroAcopio> cntAcps;

    @OneToMany(mappedBy = "puntoEca", cascade = {CascadeType.MERGE, CascadeType.REMOVE}, orphanRemoval = true)
    private List<Inventario> inventarios;
}