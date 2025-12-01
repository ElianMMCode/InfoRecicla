package org.sena.inforecicla.model;

import jakarta.persistence.*;
import jakarta.validation.constraints.NotBlank;
import jakarta.validation.constraints.Size;
import lombok.*;
import org.sena.inforecicla.model.base.EntidadCreacionModificacion;

import java.util.List;
import java.util.UUID;

@Entity
@Table(name = "localidad", indexes = {@Index(name = "idx_localidad_nombre", columnList = "nombre")})
@Getter
@Setter
@NoArgsConstructor
@AllArgsConstructor
@Builder
public class Localidad extends EntidadCreacionModificacion {

    @Id
    @GeneratedValue
    @Column(nullable = false, updatable = false)
    private UUID localidadId;

    @Column(nullable = false, length = 30, unique = true)
    @NotBlank(message = "El nombre de la localidad es obligatorio")
    @Size(min = 3, max = 30)
    private String nombre;

    @Column(length = 100)
    private String descripcion;

    // Relaciones
    @OneToMany(mappedBy = "localidad", cascade = {CascadeType.MERGE})
    private List<Usuario> usuarios;

    @OneToMany(mappedBy = "localidad", cascade = {CascadeType.MERGE})
    private List<PuntoECA> puntosEca;

    @OneToMany(mappedBy = "localidad", cascade = {CascadeType.MERGE})
    private List<CentroAcopio> centrosAcopio;

}

