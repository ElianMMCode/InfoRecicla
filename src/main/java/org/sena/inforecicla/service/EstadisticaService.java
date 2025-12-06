package org.sena.inforecicla.service;

import java.util.List;
import java.util.Map;
import java.util.UUID;

public interface EstadisticaService {
    Map<String, Object> obtenerEstadisticasPublicaciones();
    List<Map<String, Object>> obtenerEstadisticasPorEstado();
    Map<String, Object> obtenerEstadisticasPorUsuario(UUID usuarioId);
    Map<String, Object> obtenerEstadisticasPorCategoria(UUID categoriaId);
    Map<String, Long> obtenerConteoMensual(int year, int month);
}