package org.sena.inforecicla.model;

import com.fasterxml.jackson.annotation.JsonProperty;
import jakarta.persistence.*;
import jakarta.validation.constraints.*;
import lombok.*;
import org.sena.inforecicla.model.base.EntidadLocalizacionWebHorario;
import org.sena.inforecicla.model.enums.TipoCentroAcopio;
import org.sena.inforecicla.model.enums.Visibilidad;

import java.util.List;
import java.util.UUID;

@Entity
@Table(name = "centro_acopio")
@Getter
@Setter
@NoArgsConstructor
@AllArgsConstructor
@Builder
@AttributeOverrides({
        @AttributeOverride(name = "celular", column = @Column(name = "celular_centro_acopio", length = 10, unique = true)),
        @AttributeOverride(name = "email", column = @Column(name = "email_centro_acopio", unique = true, length = 150)),
        @AttributeOverride(name = "sitioWeb", column = @Column(name = "sitio_web_centro_acopio")),
        @AttributeOverride(name = "horarioAtencion", column = @Column(name = "horario_atencion_centro_acopio", length = 150))
})
public class CentroAcopio extends EntidadLocalizacionWebHorario {

    @Id
    @GeneratedValue(strategy = GenerationType.UUID)
    @Column(name="centro_acopio_id",nullable = false, updatable = false)
    private UUID cntAcpId;

    @Column(name = "nombre_centro_acopio", nullable = false, length = 30)
    @NotBlank
    @Size(min = 3, max = 30)
    private String nombreCntAcp;

    @JsonProperty("tipoCntAcp")
    @Enumerated(EnumType.STRING)
    @Column(name = "tipo_centro_acopio")
    private TipoCentroAcopio tipoCntAcp;

    @NotNull(message = "Debe escoger una visibilidad para el Centro de Acopio")
    @Enumerated(EnumType.STRING)
    @Column(nullable = false, length = 10, name = "visibilidad")
    @JsonProperty("visibilidad")
    private Visibilidad visibilidad;

    @JsonProperty("descripcion")
    @Column(name = "descripcion", length = 500)
    private String descripcion;

    @Column(name = "nota", length = 500)
    private String nota;

    @Column(name = "nombre_contacto_centro_acopio", length = 30)
    @Size(min = 3, max = 30)
    private String nombreContactoCntAcp;

    @ManyToOne
    @JoinColumn(name = "punto_eca_id", foreignKey = @ForeignKey(name = "fk_puntoeca_centroacopio"))
    @JsonProperty("puntoEca")
    private PuntoECA puntoEca;

    @OneToMany(mappedBy = "ctrAcopio", cascade = CascadeType.ALL, orphanRemoval = true)
    private List<VentaInventario> ventasInventario;
}
