package org.sena.inforecicla.service;

import lombok.RequiredArgsConstructor;
import org.sena.inforecicla.model.CategoriaPublicacion;
import org.sena.inforecicla.repository.CategoriaPublicacionRepository;
import org.springframework.stereotype.Service;

import java.util.List;
import java.util.UUID;

@Service
@RequiredArgsConstructor
public class CategoriaPublicacionService {

    private final CategoriaPublicacionRepository categoriaPublicacionRepository;

    public List<CategoriaPublicacion> obtenerTodas() {
        return categoriaPublicacionRepository.findAll();
    }

    public CategoriaPublicacion obtenerPorId(UUID id) {
        return categoriaPublicacionRepository.findById(id)
                .orElseThrow(() -> new RuntimeException("Categoría no encontrada"));
    }

    public CategoriaPublicacion crearCategoria(CategoriaPublicacion categoria) {
        return categoriaPublicacionRepository.save(categoria);
    }

    public CategoriaPublicacion actualizarCategoria(UUID id, CategoriaPublicacion categoriaActualizada) {
        CategoriaPublicacion categoria = obtenerPorId(id);
        categoria.setNombre(categoriaActualizada.getNombre());
        categoria.setDescripcion(categoriaActualizada.getDescripcion());
        return categoriaPublicacionRepository.save(categoria);
    }

    public void eliminarCategoria(UUID id) {
        categoriaPublicacionRepository.deleteById(id);
    }
}