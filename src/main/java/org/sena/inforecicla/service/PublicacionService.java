package org.sena.inforecicla.service;

import org.sena.inforecicla.dto.publicacion.PublicacionRequestDTO;
import org.sena.inforecicla.dto.publicacion.PublicacionResponseDTO;
import org.sena.inforecicla.dto.publicacion.PublicacionUpdateDTO;
import org.sena.inforecicla.model.enums.EstadoPublicacion;
import org.springframework.data.domain.Page;
import org.springframework.data.domain.Pageable;

import java.util.List;
import java.util.Map;
import java.util.UUID;

public interface PublicacionService {
    PublicacionResponseDTO crearPublicacion(PublicacionRequestDTO requestDTO);
    PublicacionResponseDTO obtenerPublicacionPorId(UUID id);
    PublicacionResponseDTO actualizarPublicacion(UUID id, PublicacionUpdateDTO updateDTO);
    void eliminarPublicacion(UUID id);
    PublicacionResponseDTO cambiarEstadoPublicacion(UUID id, EstadoPublicacion nuevoEstado);

    Page<PublicacionResponseDTO> filtrarPublicaciones(String texto, String estado, UUID categoriaId, Pageable pageable);
    List<PublicacionResponseDTO> obtenerUltimasPublicaciones(int cantidad);
    Map<String, Long> contarPorEstadoFiltrado(String texto, String estado, UUID categoriaId);

    List<PublicacionResponseDTO> obtenerPublicacionesPorUsuario(UUID usuarioId);
    List<PublicacionResponseDTO> obtenerPublicacionesPorCategoria(UUID categoriaId);

    String calcularTiempoDesdeCreacion(UUID publicacionId);
    long contarPublicacionesActivas();
    long contarPublicacionesPorUsuario(UUID usuarioId);
    long contarPublicacionesPorCategoria(UUID categoriaId);
}