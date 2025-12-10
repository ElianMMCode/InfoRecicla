package org.sena.inforecicla.model;

import jakarta.persistence.*;
import jakarta.validation.constraints.*;
import lombok.*;
import org.sena.inforecicla.model.base.EntidadLocalizacion;
import org.sena.inforecicla.model.enums.TipoDocumento;
import org.sena.inforecicla.model.enums.TipoUsuario;
import org.springframework.security.core.GrantedAuthority;
import org.springframework.security.core.userdetails.UserDetails;

import java.util.ArrayList;
import java.util.Collection;
import java.util.List;
import java.util.UUID;

@Entity
@Table(name="usuario")
@Getter
@Setter
@NoArgsConstructor
@AllArgsConstructor
public class Usuario extends EntidadLocalizacion implements UserDetails {

    @Id
    @GeneratedValue(strategy = GenerationType.AUTO)
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
            regexp = "^(\\$2[abyv]\\$\\d{2}\\$.{53}|(?=.*[a-z])(?=.*[A-Z])(?=.*\\d)(?=.*[@$!%*?&]).{8,})$",
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

    @Column(name = "activo", nullable = false)
    private Boolean activo = true;

    // Métodos de UserDetails
    @Override
    public Collection<? extends GrantedAuthority> getAuthorities() {
        List<GrantedAuthority> authorities = new ArrayList<>();
        // Agregar autoridad basada en el tipo de usuario
        authorities.add(() -> "ROLE_" + this.tipoUsuario.name());
        return authorities;
    }

    @Override
    public boolean isEnabled() {
        return this.activo;
    }

    @Override
    public boolean isAccountNonExpired() {
        return true;
    }

    @Override
    public boolean isAccountNonLocked() {
        return true;
    }

    @Override
    public String getPassword() {
        return this.password;
    }

    @Override
    public String getUsername() {
        return this.getEmail();
    }

    @Override
    public boolean isCredentialsNonExpired() {
        return true;
    }
}




