package org.sena.inforecicla.service;

import org.sena.inforecicla.dto.categoriaPublicacion.CategoriaPublicacionRequestDTO;
import org.sena.inforecicla.dto.categoriaPublicacion.CategoriaPublicacionResponseDTO;
import org.sena.inforecicla.dto.categoriaPublicacion.CategoriaPublicacionUpdateDTO;

import java.util.List;
import java.util.UUID;

public interface CategoriaPublicacionService {
    CategoriaPublicacionResponseDTO crearCategoria(CategoriaPublicacionRequestDTO requestDTO);
    CategoriaPublicacionResponseDTO obtenerCategoriaPorId(UUID id);

    List<CategoriaPublicacionResponseDTO> obtenerTodasLasCategorias();
    List<CategoriaPublicacionResponseDTO> obtenerCategoriasActivas();
    List<CategoriaPublicacionResponseDTO> buscarCategorias(String keyword);

    CategoriaPublicacionResponseDTO actualizarCategoria(UUID id, CategoriaPublicacionUpdateDTO updateDTO);
    void cambiarEstadoCategoria(UUID id, String estado);
    void eliminarCategoria(UUID id);

    long contarCategoriasActivas();
    void validarCategoriaExistente(UUID categoriaId);

    List<CategoriaPublicacionResponseDTO> obtenerTodasCategorias();
}