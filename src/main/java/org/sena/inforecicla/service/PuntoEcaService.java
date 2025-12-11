package org.sena.inforecicla.service;

import org.sena.inforecicla.dto.puntoEca.PuntoEcaMapDTO;
import org.sena.inforecicla.dto.puntoEca.PuntoEcaDetalleDTO;
import org.sena.inforecicla.dto.puntoEca.MaterialDTO;
import org.sena.inforecicla.model.PuntoECA;
import org.sena.inforecicla.model.enums.Estado;

import java.util.List;
import java.util.Optional;
import java.util.UUID;

public interface PuntoEcaService {

    Optional<PuntoECA> buscarPuntoEca(UUID puntoId);

    Optional<PuntoECA> buscarPuntoEcaEstado(UUID puntoId, Estado estado);

    List<PuntoEcaMapDTO> obtenerTodosPuntosEcaActivos();

    PuntoEcaMapDTO toPuntoEcaMapDTO(PuntoECA puntoECA);

    PuntoEcaDetalleDTO obtenerDetallesPuntoEca(UUID puntoEcaId);

    List<MaterialDTO> obtenerMaterialesDisponibles();

    List<PuntoEcaMapDTO> obtenerPuntosPorMaterial(UUID materialId);
}
