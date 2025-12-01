package org.sena.inforecicla.model;

import jakarta.persistence.*;
import lombok.AllArgsConstructor;
import lombok.Getter;
import lombok.NoArgsConstructor;
import lombok.Setter;
import org.sena.inforecicla.model.base.CreacionModificacionPublicaciones;
import org.sena.inforecicla.model.enums.TipoMultimedia;
import org.sena.inforecicla.model.enums.TipoPublicacion;
import org.sena.inforecicla.model.enums.Valor;

import java.util.UUID;

@Entity
@AllArgsConstructor
@NoArgsConstructor
@Getter
@Setter
@Table(name = "tb_votos")
public class Votos extends CreacionModificacionPublicaciones {

    @Id
    @Column(name = "votos_id", unique = true, nullable=false)
    private UUID votosId;

    @Column(name="tipo_publicacion", length = 15)
    @Enumerated(EnumType.STRING)
    private TipoPublicacion tipoPublicacion;

    //usuario_id llave foranea tabla usuario

    @Column(name="valor", length = 15)
    @Enumerated(EnumType.STRING)
    private Valor valor;

    //Getters y Setters


    public UUID getVotosId() {
        return votosId;
    }

    public void setVotosId(UUID votosId) {
        this.votosId = votosId;
    }

    public TipoPublicacion getTipoPublicacion() {
        return tipoPublicacion;
    }

    public void setTipoPublicacion(TipoPublicacion tipoPublicacion) {
        this.tipoPublicacion = tipoPublicacion;
    }

    public Valor getValor() {
        return valor;
    }

    public void setValor(Valor valor) {
        this.valor = valor;
    }
}
