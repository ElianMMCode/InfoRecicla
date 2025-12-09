package org.sena.inforecicla.model;

import jakarta.persistence.*;
import jakarta.validation.constraints.*;
import lombok.*;
import org.sena.inforecicla.model.base.EntidadLocalizacion;
import org.sena.inforecicla.model.enums.TipoDocumento;
import org.sena.inforecicla.model.enums.TipoUsuario;

import java.util.ArrayList;
import java.util.List;
import java.util.UUID;

@Entity
@Table(name="usuario")
@Getter
@Setter
@NoArgsConstructor
@AllArgsConstructor
@AttributeOverrides({
        @AttributeOverride(name = "celular", column = @Column(name = "celular", nullable = false, length = 10, unique = true)),
        @AttributeOverride(name = "email", column = @Column(name = "email", nullable = false, unique = true, length = 150)),
        @AttributeOverride(name = "ciudad", column = @Column(name = "ciudad", length = 15)),
        @AttributeOverride(name = "localidad", column = @Column(name = "localidad", nullable = false, length = 20))
})
public class Usuario extends EntidadLocalizacion {

    @Id
    @GeneratedValue
    @Column(nullable = false, updatable = false)
    private UUID usuarioId;

    @Column(nullable = false, length = 30)
    @NotBlank
    @Size(min = 3, max = 30)
    private String nombres;

    @Column(nullable = false, length = 40)
    @NotBlank
    @Size(min = 2, max = 40)
    private String apellidos;

    @Column(name = "password", nullable = false, length = 60)
    @NotBlank
    @Size(min = 8, max = 60)
    @Pattern(
            regexp = "^(?=.*[a-z])(?=.*[A-Z])(?=.*\\d)(?=.*[@$!%*?&]).+$",
            message = "Debe incluir mayúscula, minúscula, número y símbolo"
    )
    private String password;

    @Column(name = "tipo_usuario", nullable = false, length = 15)
    @Enumerated(EnumType.STRING)
    @NotNull(message = "El tipo de usuario es obligatorio")
    private TipoUsuario tipoUsuario;

    @Column(name = "tipo_documento", length = 5)
    @Enumerated(EnumType.STRING)
    private TipoDocumento tipoDocumento;

    @Column(name = "numero_documento", length = 20, unique = true)
    @Size(min = 6, max = 20, message = "El número de documento debe tener entre 6 y 20 caracteres")
    private String numeroDocumento;

    @Pattern(
            regexp = "^(19|20)\\d{2}-(0[1-9]|1[0-2])-(0[1-9]|[12]\\d|3[01])$",
            message = "La fecha debe tener un formato válido YYYY-MM-DD"
    )
    private String fechaNacimiento;

    @Column(name = "foto_perfil")
    private String fotoPerfil;

    @Column(length = 500)
    private String biografia;

    @OneToOne(mappedBy = "usuario",cascade = CascadeType.ALL, orphanRemoval = true)
    private PuntoECA puntoECA;

    @ManyToMany(mappedBy = "creadores")
    private List<Conversaciones> conversaciones;

    @OneToMany(mappedBy = "usuario", cascade = CascadeType.ALL, orphanRemoval = true)
    private List<Publicacion> publicaciones = new ArrayList<>();

    @OneToMany(mappedBy = "usuario", cascade = CascadeType.ALL, orphanRemoval = true)
    private List<Votos> votosRealizados;


}
