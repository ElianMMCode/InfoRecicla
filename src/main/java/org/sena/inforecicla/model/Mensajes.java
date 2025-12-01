package org.sena.inforecicla.model;

import jakarta.persistence.*;
import lombok.AllArgsConstructor;
import lombok.Getter;
import lombok.NoArgsConstructor;
import lombok.Setter;
import org.sena.inforecicla.model.base.CreacionModificacionPublicaciones;

import java.io.Serializable;
import java.time.LocalDateTime;
import java.util.UUID;

@Entity
@AllArgsConstructor
@NoArgsConstructor
@Getter
@Setter
@Table(name = "tb_mensajes")
public class Mensajes extends CreacionModificacionPublicaciones {

    @Id
    @Column(name = "mensajes_id", unique = true, nullable=false)
    private UUID mensajesId;

    //conversacion_id llave foranea tabla conversaciones

    //remitente_id llave foranea tabla usuarios

    @Column(name="cuerpo", columnDefinition = "TEXT", nullable = false )
    private String cuerpo;

    @ManyToOne
    @JoinColumn(name = "conversacion_id") // Nombre de la columna en la BD
    private Conversaciones conversaciones;

    @ManyToOne
    @JoinColumn(name = "usuario_id")
    private Usuario usuario;
}
