package org.sena.inforecicla.service;

import lombok.RequiredArgsConstructor;
import org.sena.inforecicla.dto.publicacion.PublicacionRequestDTO;
import org.sena.inforecicla.dto.publicacion.PublicacionResponseDTO;
import org.sena.inforecicla.dto.publicacion.PublicacionUpdateDTO;
import org.sena.inforecicla.exception.PublicacionNotFoundException;
import org.sena.inforecicla.model.CategoriaPublicacion;
import org.sena.inforecicla.model.Publicacion;
import org.sena.inforecicla.model.Usuario;
import org.sena.inforecicla.model.enums.Estado;
import org.sena.inforecicla.model.enums.EstadoPublicacion;
import org.sena.inforecicla.repository.CategoriaPublicacionRepository;
import org.sena.inforecicla.repository.PublicacionRepository;
import org.sena.inforecicla.repository.UsuarioRepository;
import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;

import java.time.LocalDateTime;
import java.util.List;
import java.util.Optional;
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
                .usuario(usuario)
                .categoriaPublicacion(categoria)
                .build();

        // Establecer campos heredados de EntidadDescripcion
        publicacion.setNombre(requestDTO.getNombre());
        publicacion.setDescripcion(requestDTO.getDescripcion());

        // Establecer estado heredado de EntidadCreacionModificacion
        publicacion.setEstado(Estado.valueOf(requestDTO.getEstado().name()));

        Publicacion publicacionGuardada = publicacionRepository.save(publicacion);

        return convertirAResponseDTO(publicacionGuardada);
    }

    public Optional<PublicacionResponseDTO> buscarPublicacionPorId(UUID id) {
        return publicacionRepository.findById(id)
                .map(this::convertirAResponseDTO);
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
        try {
            Estado estadoEnum = Estado.valueOf(estado.toUpperCase());
            return publicacionRepository.findByEstado(estadoEnum)
                    .stream()
                    .map(this::convertirAResponseDTO)
                    .collect(Collectors.toList());
        } catch (IllegalArgumentException e) {
            throw new RuntimeException("Estado inválido: " + estado);
        }
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
            publicacion.setEstado(Estado.valueOf(updateDTO.getEstado().name()));
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
            Estado nuevoEstado = Estado.valueOf(estado.toUpperCase());
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

    private PublicacionResponseDTO convertirAResponseDTO(Publicacion publicacion) {
        return PublicacionResponseDTO.builder()
                .publicacionId(publicacion.getPublicacionId())
                .titulo(publicacion.getTitulo())
                .contenido(publicacion.getContenido())
                .nombre(publicacion.getNombre())
                .descripcion(publicacion.getDescripcion())
                .estado(EstadoPublicacion.valueOf(publicacion.getEstado().name()))
                .usuarioId(publicacion.getUsuario().getUsuarioId())
                .nombreUsuario(publicacion.getUsuario().getNombres() + " " + publicacion.getUsuario().getApellidos())
                .categoriaPublicacionId(publicacion.getCategoriaPublicacion().getCategoriaPublicacionId())
                .nombreCategoria(publicacion.getCategoriaPublicacion().getNombre())
                .creado(publicacion.getFechaCreacion())
                .actualizado(publicacion.getFechaActualizacion())
                .build();
    }
}
