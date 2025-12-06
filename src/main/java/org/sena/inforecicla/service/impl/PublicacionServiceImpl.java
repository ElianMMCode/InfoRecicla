package org.sena.inforecicla.service.impl;

import lombok.RequiredArgsConstructor;
import org.sena.inforecicla.dto.publicacion.PublicacionRequestDTO;
import org.sena.inforecicla.dto.publicacion.PublicacionResponseDTO;
import org.sena.inforecicla.dto.publicacion.PublicacionUpdateDTO;
import org.sena.inforecicla.exception.PublicacionNotFoundException;
import org.sena.inforecicla.model.Publicacion;
import org.sena.inforecicla.model.enums.EstadoPublicacion;
import org.sena.inforecicla.repository.PublicacionRepository;
import org.sena.inforecicla.service.CategoriaPublicacionService;
import org.sena.inforecicla.service.PublicacionService;
import org.sena.inforecicla.service.UsuarioService;
import org.sena.inforecicla.repository.UsuarioRepository;
import org.sena.inforecicla.repository.CategoriaPublicacionRepository;
import org.springframework.data.domain.Page;
import org.springframework.data.domain.PageImpl;
import org.springframework.data.domain.PageRequest;
import org.springframework.data.domain.Pageable;
import org.springframework.data.domain.Sort;
import org.springframework.data.util.Streamable;
import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;

import java.time.LocalDateTime;
import java.time.temporal.ChronoUnit;
import java.util.*;

@Service
@RequiredArgsConstructor
@Transactional
public class PublicacionServiceImpl implements PublicacionService {

    private final PublicacionRepository publicacionRepository;
    private final CategoriaPublicacionService categoriaService;
    private final UsuarioService usuarioService;
    private final UsuarioRepository usuarioRepository;
    private final CategoriaPublicacionRepository categoriaPublicacionRepository;

    @Override
    public PublicacionResponseDTO crearPublicacion(PublicacionRequestDTO requestDTO) {
        categoriaService.validarCategoriaExistente(requestDTO.getCategoriaPublicacionId());
        usuarioService.validarUsuarioExistente(requestDTO.getUsuarioId());

        Publicacion publicacion = requestDTO.toEntity();

        // enlazar relaciones: usuario y categoría
        var usuario = usuarioRepository.findById(requestDTO.getUsuarioId())
                .orElseThrow(() -> new org.sena.inforecicla.exception.UsuarioNotFoundException("Usuario no encontrado con ID: " + requestDTO.getUsuarioId()));

        var categoria = categoriaPublicacionRepository.findById(requestDTO.getCategoriaPublicacionId())
                .orElseThrow(() -> new org.sena.inforecicla.exception.CategoriaPublicacionNotFoundException("Categoría no encontrada con ID: " + requestDTO.getCategoriaPublicacionId()));

        publicacion.setUsuario(usuario);
        publicacion.setCategoriaPublicacion(categoria);

        if (requestDTO.getEstado() == null) {
            publicacion.setEstadoPublicacion(EstadoPublicacion.BORRADOR);
        }

        Publicacion saved = publicacionRepository.save(publicacion);
        return PublicacionResponseDTO.derivado(saved);
    }

    @Override
    @Transactional(readOnly = true)
    public PublicacionResponseDTO obtenerPublicacionPorId(UUID id) {
        Publicacion publicacion = publicacionRepository.findById(id)
                .orElseThrow(() -> new PublicacionNotFoundException("Publicación no encontrada con ID: " + id));
        return PublicacionResponseDTO.derivado(publicacion);
    }

    @Override
    public PublicacionResponseDTO actualizarPublicacion(UUID id, PublicacionUpdateDTO updateDTO) {
        Publicacion publicacion = publicacionRepository.findById(id)
                .orElseThrow(() -> new PublicacionNotFoundException("Publicación no encontrada con ID: " + id));

        if (updateDTO.getCategoriaPublicacionId() != null) {
            categoriaService.validarCategoriaExistente(updateDTO.getCategoriaPublicacionId());
        }

        // aplicar cambios directamente
        if (updateDTO.getTitulo() != null) publicacion.setTitulo(updateDTO.getTitulo());
        if (updateDTO.getContenido() != null) publicacion.setContenido(updateDTO.getContenido());
        if (updateDTO.getEstado() != null) publicacion.setEstadoPublicacion(EstadoPublicacion.valueOf(updateDTO.getEstado().toUpperCase()));

        Publicacion updated = publicacionRepository.save(publicacion);
        return PublicacionResponseDTO.derivado(updated);
    }

    @Override
    public void eliminarPublicacion(UUID id) {
        Publicacion publicacion = publicacionRepository.findById(id)
                .orElseThrow(() -> new PublicacionNotFoundException("Publicación no encontrada con ID: " + id));
        publicacion.setEstadoPublicacion(EstadoPublicacion.ELIMINADO);
        publicacionRepository.save(publicacion);
    }

    @Override
    public PublicacionResponseDTO cambiarEstadoPublicacion(UUID id, EstadoPublicacion nuevoEstado) {
        Publicacion publicacion = publicacionRepository.findById(id)
                .orElseThrow(() -> new PublicacionNotFoundException("Publicación no encontrada con ID: " + id));

        validarCambioEstado(publicacion.getEstadoPublicacion(), nuevoEstado);
        publicacion.setEstadoPublicacion(nuevoEstado);

        Publicacion updated = publicacionRepository.save(publicacion);
        return PublicacionResponseDTO.derivado(updated);
    }

    private void validarCambioEstado(EstadoPublicacion estadoActual, EstadoPublicacion nuevoEstado) {
        if (estadoActual == EstadoPublicacion.ELIMINADO) {
            throw new IllegalArgumentException("No se puede cambiar el estado de una publicación eliminada");
        }
        if (nuevoEstado == EstadoPublicacion.ELIMINADO && estadoActual != EstadoPublicacion.BORRADOR) {
            throw new IllegalArgumentException("Solo las publicaciones en borrador pueden ser eliminadas");
        }
    }

    @Override
    @Transactional(readOnly = true)
    public Page<PublicacionResponseDTO> filtrarPublicaciones(String texto, String estado, UUID categoriaId, Pageable pageable) {
        Page<Publicacion> publicacionesPage;

        if (texto != null && !texto.trim().isEmpty()) {
            // Usar la variante que devuelve List y envolver en PageImpl para evitar problemas de resolución de tipos
            List<Publicacion> list = publicacionRepository.buscarPorPalabraClave(texto);
            publicacionesPage = new PageImpl<>(list, pageable, list.size());
        } else if (estado != null && categoriaId != null) {
            publicacionesPage = publicacionRepository.findByEstadoPublicacionAndCategoriaPublicacionId(
                    EstadoPublicacion.valueOf(estado.toUpperCase()), categoriaId, pageable);
        } else if (estado != null) {
            publicacionesPage = publicacionRepository.findByEstadoPublicacion(
                    EstadoPublicacion.valueOf(estado.toUpperCase()), pageable);
        } else if (categoriaId != null) {
            publicacionesPage = publicacionRepository.findByCategoriaPublicacionId(categoriaId, pageable);
        } else {
            publicacionesPage = publicacionRepository.findAll(pageable);
        }

        // Page no implementa filter; extraemos el contenido, filtramos y recreamos un PageImpl
        List<Publicacion> contenidoFiltrado = publicacionesPage.getContent()
                .stream()
                .filter(p -> p.getEstadoPublicacion() != EstadoPublicacion.ELIMINADO)
                .toList();

        Page<Publicacion> pageFiltrada = new PageImpl<>(contenidoFiltrado, pageable, contenidoFiltrado.size());
        return pageFiltrada.map(PublicacionResponseDTO::derivado);
    }

    @Override
    @Transactional(readOnly = true)
    public List<PublicacionResponseDTO> obtenerUltimasPublicaciones(int cantidad) {
        Pageable pageable = PageRequest.of(0, Math.max(1, cantidad), Sort.by("fechaCreacion").descending());

        List<Publicacion> resultados = publicacionRepository.findByEstadoPublicacionNot(EstadoPublicacion.ELIMINADO, pageable);
        return resultados.stream().map(PublicacionResponseDTO::derivado).toList();
    }

    @Override
    @Transactional(readOnly = true)
    public Map<String, Long> contarPorEstadoFiltrado(String texto, String estado, UUID categoriaId) {
        Map<String, Long> conteo = new HashMap<>();

        for (EstadoPublicacion estadoPub : EstadoPublicacion.values()) {
            if (estadoPub != EstadoPublicacion.ELIMINADO) {
                long count = publicacionRepository.countByEstadoPublicacion(estadoPub);
                conteo.put(estadoPub.name(), count);
            }
        }
        return conteo;
    }

    @Override
    @Transactional(readOnly = true)
    public List<PublicacionResponseDTO> obtenerPublicacionesPorUsuario(UUID usuarioId) {
        return publicacionRepository.findByUsuarioId(usuarioId)
                .stream()
                .filter(p -> p.getEstadoPublicacion() != EstadoPublicacion.ELIMINADO)
                .map(PublicacionResponseDTO::derivado)
                .toList();
    }

    @Override
    @Transactional(readOnly = true)
    public List<PublicacionResponseDTO> obtenerPublicacionesPorCategoria(UUID categoriaId) {
        return publicacionRepository.findByCategoriaPublicacionId(categoriaId)
                .stream()
                .filter(p -> p.getEstadoPublicacion() != EstadoPublicacion.ELIMINADO)
                .map(PublicacionResponseDTO::derivado)
                .toList();
    }

    @Override
    public String calcularTiempoDesdeCreacion(UUID publicacionId) {
        Publicacion publicacion = publicacionRepository.findById(publicacionId)
                .orElseThrow(() -> new PublicacionNotFoundException("Publicación no encontrada"));

        LocalDateTime fechaCreacion = publicacion.getFechaCreacion();
        LocalDateTime ahora = LocalDateTime.now();

        long minutos = ChronoUnit.MINUTES.between(fechaCreacion, ahora);
        long horas = ChronoUnit.HOURS.between(fechaCreacion, ahora);
        long dias = ChronoUnit.DAYS.between(fechaCreacion, ahora);

        if (minutos < 60) return minutos + " minutos";
        else if (horas < 24) return horas + " horas";
        else return dias + " días";
    }

    @Override
    @Transactional(readOnly = true)
    public long contarPublicacionesActivas() {
        return publicacionRepository.countByEstadoPublicacionNot(EstadoPublicacion.ELIMINADO);
    }

    @Override
    @Transactional(readOnly = true)
    public long contarPublicacionesPorUsuario(UUID usuarioId) {
        return publicacionRepository.countByUsuarioIdAndEstadoPublicacionNot(usuarioId, EstadoPublicacion.ELIMINADO);
    }

    @Override
    @Transactional(readOnly = true)
    public long contarPublicacionesPorCategoria(UUID categoriaId) {
        return publicacionRepository.countByCategoriaPublicacionIdAndEstadoPublicacionNot(categoriaId, EstadoPublicacion.ELIMINADO);
    }
}
