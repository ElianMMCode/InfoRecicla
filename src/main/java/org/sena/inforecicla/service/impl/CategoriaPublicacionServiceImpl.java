package org.sena.inforecicla.service.impl;

import lombok.RequiredArgsConstructor;
import org.sena.inforecicla.dto.categoriaPublicacion.CategoriaPublicacionRequestDTO;
import org.sena.inforecicla.dto.categoriaPublicacion.CategoriaPublicacionResponseDTO;
import org.sena.inforecicla.dto.categoriaPublicacion.CategoriaPublicacionUpdateDTO;
import org.sena.inforecicla.exception.CategoriaPublicacionNotFoundException;
import org.sena.inforecicla.model.CategoriaPublicacion;
import org.sena.inforecicla.repository.CategoriaPublicacionRepository;
import org.sena.inforecicla.service.CategoriaPublicacionService;
import org.sena.inforecicla.service.PublicacionService;
import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;

import java.util.List;
import java.util.UUID;

@Service
@RequiredArgsConstructor
@Transactional
public class CategoriaPublicacionServiceImpl implements CategoriaPublicacionService {

    private final CategoriaPublicacionRepository categoriaRepository;
    private final PublicacionService publicacionService;

    @Override
    public CategoriaPublicacionResponseDTO crearCategoria(CategoriaPublicacionRequestDTO requestDTO) {
        CategoriaPublicacion categoria = requestDTO.toEntity();
        CategoriaPublicacion saved = categoriaRepository.save(categoria);
        return CategoriaPublicacionResponseDTO.derivado(saved);
    }

    @Override
    @Transactional(readOnly = true)
    public CategoriaPublicacionResponseDTO obtenerCategoriaPorId(UUID id) {
        CategoriaPublicacion categoria = categoriaRepository.findById(id)
                .orElseThrow(() -> new CategoriaPublicacionNotFoundException("Categoría no encontrada con ID: " + id));
        return CategoriaPublicacionResponseDTO.derivado(categoria);
    }

    @Override
    @Transactional(readOnly = true)
    public List<CategoriaPublicacionResponseDTO> obtenerTodasLasCategorias() {
        return categoriaRepository.findAll()
                .stream()
                .map(CategoriaPublicacionResponseDTO::derivado)
                .toList();
    }

    @Override
    @Transactional(readOnly = true)
    public List<CategoriaPublicacionResponseDTO> obtenerCategoriasActivas() {
        return categoriaRepository.findByEstado("ACTIVO")
                .stream()
                .map(CategoriaPublicacionResponseDTO::derivado)
                .toList();
    }

    @Override
    @Transactional(readOnly = true)
    public List<CategoriaPublicacionResponseDTO> buscarCategorias(String keyword) {
        return categoriaRepository.findByNombreContainingIgnoreCase(keyword)
                .stream()
                .map(CategoriaPublicacionResponseDTO::derivado)
                .toList();
    }

    @Override
    public CategoriaPublicacionResponseDTO actualizarCategoria(UUID id, CategoriaPublicacionUpdateDTO updateDTO) {
        CategoriaPublicacion categoria = categoriaRepository.findById(id)
                .orElseThrow(() -> new CategoriaPublicacionNotFoundException("Categoría no encontrada con ID: " + id));

        if (updateDTO.getNombre() != null) categoria.setNombre(updateDTO.getNombre());
        if (updateDTO.getDescripcion() != null) categoria.setDescripcion(updateDTO.getDescripcion());

        CategoriaPublicacion updated = categoriaRepository.save(categoria);
        return CategoriaPublicacionResponseDTO.derivado(updated);
    }

    @Override
    public void cambiarEstadoCategoria(UUID id, String estado) {
        CategoriaPublicacion categoria = categoriaRepository.findById(id)
                .orElseThrow(() -> new CategoriaPublicacionNotFoundException("Categoría no encontrada con ID: " + id));

        if (!estado.equals("ACTIVO") && !estado.equals("INACTIVO")) {
            throw new IllegalArgumentException("Estado inválido. Use 'ACTIVO' o 'INACTIVO'");
        }

        categoria.setEstado(org.sena.inforecicla.model.enums.Estado.valueOf(estado));
        categoriaRepository.save(categoria);
    }

    @Override
    public void eliminarCategoria(UUID id) {
        CategoriaPublicacion categoria = categoriaRepository.findById(id)
                .orElseThrow(() -> new CategoriaPublicacionNotFoundException("Categoría no encontrada con ID: " + id));

        long publicacionesAsociadas = publicacionService.contarPublicacionesPorCategoria(id);
        if (publicacionesAsociadas > 0) {
            throw new IllegalStateException(
                    "No se puede eliminar la categoría porque tiene " + publicacionesAsociadas + " publicaciones asociadas");
        }

        categoriaRepository.delete(categoria);
    }

    @Override
    @Transactional(readOnly = true)
    public long contarCategoriasActivas() {
        return categoriaRepository.countByEstado("ACTIVO");
    }

    @Override
    public void validarCategoriaExistente(UUID categoriaId) {
        if (!categoriaRepository.existsById(categoriaId)) {
            throw new CategoriaPublicacionNotFoundException("Categoría no encontrada con ID: " + categoriaId);
        }
    }

    @Override
    public List<CategoriaPublicacionResponseDTO> obtenerTodasCategorias() {
        return obtenerTodasLasCategorias();
    }
}