package org.sena.inforecicla.service;

import lombok.RequiredArgsConstructor;
import org.sena.inforecicla.dto.publicacion.PublicacionRequestDTO;
import org.sena.inforecicla.dto.publicacion.PublicacionResponseDTO;
import org.sena.inforecicla.dto.publicacion.PublicacionUpdateDTO;
import org.sena.inforecicla.exception.PublicacionNotFoundException;
import org.sena.inforecicla.model.CategoriaPublicacion;
import org.sena.inforecicla.model.Publicacion;
import org.sena.inforecicla.model.Usuario;
import org.sena.inforecicla.model.enums.EstadoPublicacion;
import org.sena.inforecicla.repository.CategoriaPublicacionRepository;
import org.sena.inforecicla.repository.PublicacionRepository;
import org.sena.inforecicla.repository.UsuarioRepository;
import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;

import java.time.LocalDateTime;
import java.util.List;
import java.util.UUID;
import java.util.stream.Collectors;

@Service
@RequiredArgsConstructor
public class PublicacionService {

    private final PublicacionRepository publicacionRepository;
    private final UsuarioRepository usuarioRepository;
    private final CategoriaPublicacionRepository categoriaPublicacionRepository;

    @Transactional
    public PublicacionResponseDTO crearPublicacion(PublicacionRequestDTO requestDTO) {
        // Validar usuario
        Usuario usuario = usuarioRepository.findById(requestDTO.getUsuarioId())
                .orElseThrow(() -> new RuntimeException("Usuario no encontrado"));

        // Validar categoría
        CategoriaPublicacion categoria = categoriaPublicacionRepository
                .findById(requestDTO.getCategoriaPublicacionId())
                .orElseThrow(() -> new RuntimeException("Categoría no encontrada"));

        // Crear publicación
        Publicacion publicacion = Publicacion.builder()
                .titulo(requestDTO.getTitulo())
                .contenido(requestDTO.getContenido())
                .nombre(requestDTO.getNombre())
                .descripcion(requestDTO.getDescripcion())
                .estado(requestDTO.getEstado())
                .usuario(usuario)
                .categoriaPublicacion(categoria)
                .build();

        Publicacion publicacionGuardada = publicacionRepository.save(publicacion);

        return convertirAResponseDTO(publicacionGuardada);
    }

    public PublicacionResponseDTO mostrarPublicacionPorId(UUID id) {
        return publicacionRepository.findById(id)
                .map(this::convertirAResponseDTO)
                .orElseThrow(() -> new PublicacionNotFoundException("Publicación no encontrada"));
    }

    public List<PublicacionResponseDTO> mostrarTodasLasPublicaciones() {
        return publicacionRepository.findAll()
                .stream()
                .map(this::convertirAResponseDTO)
                .collect(Collectors.toList());
    }

    public List<PublicacionResponseDTO> mostrarPublicacionesPorUsuario(UUID usuarioId) {
        return publicacionRepository.findByUsuarioId(usuarioId)
                .stream()
                .map(this::convertirAResponseDTO)
                .collect(Collectors.toList());
    }

    public List<PublicacionResponseDTO> mostrarPublicacionesPorEstado(String estado) {
        return publicacionRepository.findByEstado(estado)
                .stream()
                .map(this::convertirAResponseDTO)
                .collect(Collectors.toList());
    }

    public List<PublicacionResponseDTO> buscarPublicaciones(String keyword) {
        return publicacionRepository.buscarPorPalabraClave(keyword)
                .stream()
                .map(this::convertirAResponseDTO)
                .collect(Collectors.toList());
    }

    @Transactional
    public PublicacionResponseDTO actualizarPublicacion(UUID id, PublicacionUpdateDTO updateDTO) {
        Publicacion publicacion = publicacionRepository.findById(id)
                .orElseThrow(() -> new PublicacionNotFoundException("Publicación no encontrada"));

        // Actualizar campos si se proporcionan
        if (updateDTO.getTitulo() != null) {
            publicacion.setTitulo(updateDTO.getTitulo());
        }
        if (updateDTO.getContenido() != null) {
            publicacion.setContenido(updateDTO.getContenido());
        }
        if (updateDTO.getNombre() != null) {
            publicacion.setNombre(updateDTO.getNombre());
        }
        if (updateDTO.getDescripcion() != null) {
            publicacion.setDescripcion(updateDTO.getDescripcion());
        }
        if (updateDTO.getEstado() != null) {
            publicacion.setEstado(updateDTO.getEstado());
        }
        if (updateDTO.getCategoriaPublicacionId() != null) {
            CategoriaPublicacion categoria = categoriaPublicacionRepository
                    .findById(updateDTO.getCategoriaPublicacionId())
                    .orElseThrow(() -> new RuntimeException("Categoría no encontrada"));
            publicacion.setCategoriaPublicacion(categoria);
        }

        publicacion.setFechaActualizacion(LocalDateTime.now());
        Publicacion publicacionActualizada = publicacionRepository.save(publicacion);

        return convertirAResponseDTO(publicacionActualizada);
    }

    @Transactional
    public void cambiarEstadoPublicacion(UUID id, String estado) {
        Publicacion publicacion = publicacionRepository.findById(id)
                .orElseThrow(() -> new PublicacionNotFoundException("Publicación no encontrada"));

        try {
            EstadoPublicacion nuevoEstado = EstadoPublicacion.valueOf(estado);
            publicacion.setEstado(nuevoEstado);
            publicacion.setFechaActualizacion(LocalDateTime.now());
            publicacionRepository.save(publicacion);
        } catch (IllegalArgumentException e) {
            throw new RuntimeException("Estado inválido: " + estado);
        }
    }

    @Transactional
    public void eliminarPublicacion(UUID id) {
        if (!publicacionRepository.existsById(id)) {
            throw new PublicacionNotFoundException("Publicación no encontrada");
        }
        publicacionRepository.deleteById(id);
    }

    public long contarPublicacionesPorUsuario(UUID usuarioId) {
        return publicacionRepository.countByUsuarioId(usuarioId);
    }

    public long contarPublicacionesPorUsuarioYEstado(UUID usuarioId, EstadoPublicacion estado) {
        return publicacionRepository.findAll()
                .stream()
                .filter(p -> p.getUsuario().getUsuarioId().equals(usuarioId) && p.getEstado() == estado)
                .count();
    }

    private PublicacionResponseDTO convertirAResponseDTO(Publicacion publicacion) {
        return PublicacionResponseDTO.builder()
                .publicacionId(publicacion.getPublicacionId())
                .titulo(publicacion.getTitulo())
                .contenido(publicacion.getContenido())
                .nombre(publicacion.getNombre())
                .descripcion(publicacion.getDescripcion())
                .estado(publicacion.getEstado())
                .usuarioId(publicacion.getUsuario().getUsuarioId())
                .nombreUsuario(publicacion.getUsuario().getNombre() + " " + publicacion.getUsuario().getApellido())
                .categoriaPublicacionId(publicacion.getCategoriaPublicacion().getCategoiraPublicacionId())
                .nombreCategoria(publicacion.getCategoriaPublicacion().getNombre())
                .creado(publicacion.getFechaCreacion())
                .actualizado(publicacion.getFechaActualizacion())
                .build();
    }
}