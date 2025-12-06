package org.sena.inforecicla.service.impl;

import lombok.RequiredArgsConstructor;
import org.sena.inforecicla.model.enums.EstadoPublicacion;
import org.sena.inforecicla.repository.PublicacionRepository;
import org.sena.inforecicla.service.CategoriaPublicacionService;
import org.sena.inforecicla.service.EstadisticaService;
import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;

import java.util.HashMap;
import java.util.List;
import java.util.Map;
import java.util.UUID;

@Service
@RequiredArgsConstructor
@Transactional(readOnly = true)
public class EstadisticaServiceImpl implements EstadisticaService {

    private final PublicacionRepository publicacionRepository;
    private final CategoriaPublicacionService categoriaService;

    @Override
    public Map<String, Object> obtenerEstadisticasPublicaciones() {
        Map<String, Object> estadisticas = new HashMap<>();

        long totalPublicaciones = publicacionRepository.countByEstadoPublicacionNot(EstadoPublicacion.ELIMINADO);
        long totalPublicadas = publicacionRepository.countByEstadoPublicacion(EstadoPublicacion.PUBLICADO);
        long totalBorradores = publicacionRepository.countByEstadoPublicacion(EstadoPublicacion.BORRADOR);
        long totalArchivadas = publicacionRepository.countByEstadoPublicacion(EstadoPublicacion.ARCHIVADO);
        long categoriasActivas = categoriaService.contarCategoriasActivas();

        double porcentajePublicadas = totalPublicaciones > 0 ? (double) totalPublicadas / totalPublicaciones * 100 : 0;
        double porcentajeBorradores = totalPublicaciones > 0 ? (double) totalBorradores / totalPublicaciones * 100 : 0;
        double porcentajeArchivadas = totalPublicaciones > 0 ? (double) totalArchivadas / totalPublicaciones * 100 : 0;

        estadisticas.put("total", totalPublicaciones);
        estadisticas.put("publicadas", totalPublicadas);
        estadisticas.put("borradores", totalBorradores);
        estadisticas.put("archivadas", totalArchivadas);
        estadisticas.put("categoriasActivas", categoriasActivas);
        estadisticas.put("porcentajePublicadas", Math.round(porcentajePublicadas * 100.0) / 100.0);
        estadisticas.put("porcentajeBorradores", Math.round(porcentajeBorradores * 100.0) / 100.0);
        estadisticas.put("porcentajeArchivadas", Math.round(porcentajeArchivadas * 100.0) / 100.0);

        return estadisticas;
    }

    @Override
    public List<Map<String, Object>> obtenerEstadisticasPorEstado() {
        return List.of(
                crearEstadisticaEstado("Publicadas", EstadoPublicacion.PUBLICADO, "success"),
                crearEstadisticaEstado("Borradores", EstadoPublicacion.BORRADOR, "warning"),
                crearEstadisticaEstado("Archivadas", EstadoPublicacion.ARCHIVADO, "secondary")
        );
    }

    private Map<String, Object> crearEstadisticaEstado(String nombre, EstadoPublicacion estado, String color) {
        Map<String, Object> estadistica = new HashMap<>();
        long cantidad = publicacionRepository.countByEstadoPublicacion(estado);
        long total = publicacionRepository.countByEstadoPublicacionNot(EstadoPublicacion.ELIMINADO);
        double porcentaje = total > 0 ? Math.round((double) cantidad / total * 100 * 100.0) / 100.0 : 0;

        estadistica.put("nombre", nombre);
        estadistica.put("estado", estado.name());
        estadistica.put("cantidad", cantidad);
        estadistica.put("porcentaje", porcentaje);
        estadistica.put("color", color);
        estadistica.put("icono", obtenerIconoEstado(estado));

        return estadistica;
    }

    private String obtenerIconoEstado(EstadoPublicacion estado) {
        return switch (estado) {
            case PUBLICADO -> "bi-check-circle";
            case BORRADOR -> "bi-pencil-square";
            case ARCHIVADO -> "bi-archive";
            default -> "bi-question-circle";
        };
    }

    @Override
    public Map<String, Object> obtenerEstadisticasPorUsuario(UUID usuarioId) {
        Map<String, Object> estadisticas = new HashMap<>();

        long totalUsuario = publicacionRepository.countByUsuarioIdAndEstadoPublicacionNot(usuarioId, EstadoPublicacion.ELIMINADO);
        long publicadasUsuario = publicacionRepository.countByUsuarioIdAndEstadoPublicacion(usuarioId, EstadoPublicacion.PUBLICADO);
        long borradoresUsuario = publicacionRepository.countByUsuarioIdAndEstadoPublicacion(usuarioId, EstadoPublicacion.BORRADOR);
        long archivadasUsuario = publicacionRepository.countByUsuarioIdAndEstadoPublicacion(usuarioId, EstadoPublicacion.ARCHIVADO);

        estadisticas.put("totalUsuario", totalUsuario);
        estadisticas.put("publicadasUsuario", publicadasUsuario);
        estadisticas.put("borradoresUsuario", borradoresUsuario);
        estadisticas.put("archivadasUsuario", archivadasUsuario);

        if (totalUsuario > 0) {
            estadisticas.put("porcentajePublicadasUsuario", Math.round((double) publicadasUsuario / totalUsuario * 100 * 100.0) / 100.0);
            estadisticas.put("porcentajeBorradoresUsuario", Math.round((double) borradoresUsuario / totalUsuario * 100 * 100.0) / 100.0);
            estadisticas.put("porcentajeArchivadasUsuario", Math.round((double) archivadasUsuario / totalUsuario * 100 * 100.0) / 100.0);
        } else {
            estadisticas.put("porcentajePublicadasUsuario", 0.0);
            estadisticas.put("porcentajeBorradoresUsuario", 0.0);
            estadisticas.put("porcentajeArchivadasUsuario", 0.0);
        }

        return estadisticas;
    }

    @Override
    public Map<String, Object> obtenerEstadisticasPorCategoria(UUID categoriaId) {
        Map<String, Object> estadisticas = new HashMap<>();

        long totalCategoria = publicacionRepository.countByCategoriaPublicacionIdAndEstadoPublicacionNot(categoriaId, EstadoPublicacion.ELIMINADO);
        long publicadasCategoria = publicacionRepository.countByCategoriaPublicacionIdAndEstadoPublicacion(categoriaId, EstadoPublicacion.PUBLICADO);
        long borradoresCategoria = publicacionRepository.countByCategoriaPublicacionIdAndEstadoPublicacion(categoriaId, EstadoPublicacion.BORRADOR);
        long archivadasCategoria = publicacionRepository.countByCategoriaPublicacionIdAndEstadoPublicacion(categoriaId, EstadoPublicacion.ARCHIVADO);

        estadisticas.put("totalCategoria", totalCategoria);
        estadisticas.put("publicadasCategoria", publicadasCategoria);
        estadisticas.put("borradoresCategoria", borradoresCategoria);
        estadisticas.put("archivadasCategoria", archivadasCategoria);

        if (totalCategoria > 0) {
            estadisticas.put("porcentajePublicadasCategoria", Math.round((double) publicadasCategoria / totalCategoria * 100 * 100.0) / 100.0);
            estadisticas.put("porcentajeBorradoresCategoria", Math.round((double) borradoresCategoria / totalCategoria * 100 * 100.0) / 100.0);
            estadisticas.put("porcentajeArchivadasCategoria", Math.round((double) archivadasCategoria / totalCategoria * 100 * 100.0) / 100.0);
        }

        return estadisticas;
    }

    @Override
    public Map<String, Long> obtenerConteoMensual(int year, int month) {
        Map<String, Long> conteo = new HashMap<>();
        for (int i = 1; i <= 30; i++) {
            conteo.put(String.valueOf(i), (long) (Math.random() * 10));
        }
        return conteo;
    }
}