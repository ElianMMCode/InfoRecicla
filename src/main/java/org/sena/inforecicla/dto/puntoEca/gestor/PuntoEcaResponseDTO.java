package org.sena.inforecicla.dto.puntoEca.gestor;

import org.sena.inforecicla.model.PuntoECA;

import java.util.UUID;

public record PuntoEcaResponseDTO(

        UUID puntoEcaId,
        String nombrePunto,
        String descripcionPunto,
        String fotoPunto,
        String logoPunto,
        String telefonoPunto,
        String direccionPunto,
        UUID localidadId,
        double latitud,
        double longitud,
        String sitioWebPunto,
        String horarioAtencionPunto
) {

    public static PuntoEcaResponseDTO derivado(PuntoECA p){
        return new PuntoEcaResponseDTO(
                p.getPuntoEcaID(),
                p.getNombrePunto(),
                p.getDescripcion(),
                p.getFotoUrlPunto(),
                p.getLogoUrlPunto(),
                p.getTelefonoPunto(),
                p.getDireccion(),
                p.getLocalidad() != null ? p.getLocalidad().getLocalidadId() : null,
                p.getLatitud(),
                p.getLongitud(),
                p.getSitioWeb(),
                p.getHorarioAtencion()
        );
    }
}
