package org.sena.inforecicla.dto;

import com.fasterxml.jackson.annotation.JsonInclude;
import com.fasterxml.jackson.annotation.JsonProperty;
import lombok.AllArgsConstructor;
import lombok.Builder;
import lombok.Data;
import lombok.NoArgsConstructor;
import org.sena.inforecicla.model.CentroAcopio;

import java.util.UUID;

@Data
@NoArgsConstructor
@AllArgsConstructor
@Builder
@JsonInclude(JsonInclude.Include.NON_NULL)
public class CentroAcopioDTO {

    @JsonProperty("cntAcpId")
    private UUID cntAcpId;

    @JsonProperty("nombreCntAcp")
    private String nombreCntAcp;

    @JsonProperty("tipoCntAcp")
    private String tipoCntAcp;

    @JsonProperty("visibilidad")
    private String visibilidad;

    @JsonProperty("descripcion")
    private String descripcion;

    @JsonProperty("nota")
    private String nota;

    @JsonProperty("nombreContactoCntAcp")
    private String nombreContactoCntAcp;

    @JsonProperty("tienePuntoEca")
    private Boolean tienePuntoEca;

    @JsonProperty("ciudad")
    private String ciudad;

    @JsonProperty("celular")
    private String celular;

    @JsonProperty("email")
    private String email;

    @JsonProperty("localidad")
    private String localidad;

    public static CentroAcopioDTO fromEntity(CentroAcopio centro) {
        if (centro == null) {
            return null;
        }

        return CentroAcopioDTO.builder()
                .cntAcpId(centro.getCntAcpId())
                .nombreCntAcp(centro.getNombreCntAcp())
                .tipoCntAcp(centro.getTipoCntAcp() != null ? centro.getTipoCntAcp().toString() : null)
                .visibilidad(centro.getVisibilidad() != null ? centro.getVisibilidad().toString() : null)
                .descripcion(centro.getDescripcion())
                .nota(centro.getNota())
                .nombreContactoCntAcp(centro.getNombreContactoCntAcp())
                .tienePuntoEca(centro.getPuntoEca() != null)
                .ciudad(centro.getCiudad())
                .celular(centro.getCelular())
                .email(centro.getEmail())
                .localidad(centro.getLocalidad() != null ? centro.getLocalidad().getNombre() : null)
                .build();
    }
}


