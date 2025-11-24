package org.sena.inforecicla.model;

import jakarta.persistence.*;
import jakarta.validation.constraints.*;
import lombok.*;
import org.sena.inforecicla.model.base.EntidadLocalizacionWebHorario;
import org.sena.inforecicla.model.enums.TipoCentroAcopio;
import org.sena.inforecicla.model.enums.Visibilidad;

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
        @AttributeOverride(name = "sitioWeb", column = @Column(name = "sitio_web_centro_copio")),
        @AttributeOverride(name = "horarioAtencion", column = @Column(name = "horario_atencion_centro_acopio", length = 150))
})
public class CentroAcopio extends EntidadLocalizacionWebHorario {

    @Id
    @GeneratedValue
    @Column(name = "centro_acopio_id")
    private UUID cntAcpId;

    @Column(name = "nombre_centro_acopio", nullable = false, length = 30)
    @NotBlank
    @Size(min = 3, max = 30)
    private String nombreCntAcp;

    @NotNull(message = "Debe escoger un tipo de Centro de Acopio")
    @Enumerated(EnumType.STRING)
    @Column(name = "tipo_centro_acopio", nullable = false, length = 10)
    private TipoCentroAcopio tipoCntAcp;

    @NotNull(message = "Debe escoger una visibilidad para el Centro de Acopio")
    @Enumerated(EnumType.STRING)
    @Column(nullable = false, length = 10)
    private Visibilidad visibilidad;

    @Column(length = 500)
    private String descripcion;

    @Column(length = 500)
    private String nota;

    @Column(name = "nombre_contacto_centro_acopio", length = 30)
    @Size(min = 3, max = 30)
    private String nombreContactoCntAcp;

    @ManyToOne
    @JoinColumn(name = "punto_eca_id", foreignKey = @ForeignKey(name = "fk_puntoeca_centroacopio"))
    private PuntoECA puntoEca;
}
