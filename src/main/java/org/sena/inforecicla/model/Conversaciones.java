package org.sena.inforecicla.model;

import jakarta.persistence.*;
import lombok.AllArgsConstructor;
import lombok.Getter;
import lombok.NoArgsConstructor;
import lombok.Setter;
import org.sena.inforecicla.model.base.EntidadCreacionModificacion;

import java.util.ArrayList;
import java.util.List;
import java.util.UUID;

@Entity
@AllArgsConstructor
@NoArgsConstructor
@Getter
@Setter
@Table(name = "tb_conversacion")
public class Conversaciones extends EntidadCreacionModificacion {

    @Id
    @GeneratedValue
    @Column(name = "conversacion_id", unique = true, nullable=false)
    private UUID conversacionId;

    @Column(name = "titulo", nullable = false, length = 150)
    private String titulo;


    @ManyToMany
    @JoinTable(
            name = "tb_conversacion_usuarios", // Nombre tabla intermedia
            joinColumns = @JoinColumn(name = "conversacion_id"),
            inverseJoinColumns = @JoinColumn(name = "usuario_id")
    )
    private List<Usuario> creadores;

    @OneToMany(mappedBy = "conversacion", cascade = CascadeType.ALL, orphanRemoval = true)
    private List<Mensajes> mensajes = new ArrayList<>();

}
