package org.sena.inforecicla.service.impl;

import lombok.RequiredArgsConstructor;
import org.sena.inforecicla.dto.puntoEca.materiales.CategoriaMaterialesInvResponseDTO;
import org.sena.inforecicla.dto.puntoEca.materiales.MaterialInvResponseDTO;
import org.sena.inforecicla.dto.puntoEca.materiales.MaterialResponseDTO;
import org.sena.inforecicla.dto.puntoEca.materiales.TipoMaterialesInvResponseDTO;
import org.sena.inforecicla.exception.InventarioFoundExistException;
import org.sena.inforecicla.model.Inventario;
import org.sena.inforecicla.model.Material;
import org.sena.inforecicla.repository.InventarioRepository;
import org.sena.inforecicla.repository.MaterialRepository;
import org.sena.inforecicla.service.InventarioDetalleService;
import org.springframework.stereotype.Service;

import java.util.*;
import java.util.function.Function;
import java.util.stream.Collectors;

import static java.util.Comparator.comparing;

@Service
@RequiredArgsConstructor
public class InventarioDetalleServiceImpl implements InventarioDetalleService {

    private final InventarioRepository inventarioRepository;
    private final MaterialRepository materialRepository;

    /*@Override
    public Map<String, List<Object>> listaDetallesMaterialesInventario(UUID puntoId) {

        List<CategoriaMaterialesInvResponseDTO> listaCategoriaDelPunto = obtenerCategoriasDelPunto(puntoId);
        List<TipoMaterialesInvResponseDTO> listaTiposDelPunto = obtenerTiposDelPunto(puntoId);

        return Map.of
                "categorias", listaCategoriaDelPunto,
                "tipos", listaTiposDelPunto
        );

    }*/

    /**
     * Obtiene todas las categorías únicas de los materiales en un punto ECA
     *
     * @param puntoId el ID del punto ECA
     * @return lista de categorías sin duplicados
     */
    @Override
    public List<CategoriaMaterialesInvResponseDTO> obtenerCategoriasDelPunto(UUID puntoId) {
        return inventarioRepository.findAllByPuntoEca_PuntoEcaID(puntoId).stream()
                .map(inventario -> inventario.getMaterial().getCtgMaterial())
                .distinct()
                .map(CategoriaMaterialesInvResponseDTO::derivado)
                .toList();
    }

    /**
     * Obtiene todos los tipos únicos de los materiales en un punto ECA
     *
     * @param puntoId el ID del punto ECA
     * @return lista de tipos sin duplicados
     */
    @Override
    public List<TipoMaterialesInvResponseDTO> obtenerTiposDelPunto(UUID puntoId) {
        return inventarioRepository.findAllByPuntoEca_PuntoEcaID(puntoId).stream()
                .map(inventario -> inventario.getMaterial().getTipoMaterial())
                .distinct()
                .map(TipoMaterialesInvResponseDTO::derivado)
                .toList();
    }


    @Override
    public List<MaterialResponseDTO> buscarMaterialNuevoFiltrandoInventario(UUID puntoId, String texto, String categoria, String tipo) throws InventarioFoundExistException {

        // Normalizar y validar parámetros
        FiltroNormalizado filtro = normalizarYValidarFiltros(texto, categoria, tipo);

        // Obtener materiales que ya existen en el inventario del punto
        Set<UUID> materialesExistentes = obtenerMaterialesExistentes(puntoId);

        // Obtener todos los materiales que coinciden con los filtros
        List<Material> materialesEncontrados = buscarMaterialesPorFiltro(filtro);

        // Verificar que no todos los materiales encontrados ya existen
        validarQueNoTodosExisten(materialesEncontrados, materialesExistentes);

        // Filtrar solo los materiales que NO existen en el inventario
        return materialesEncontrados.stream()
                .filter(material -> !materialesExistentes.contains(material.getMaterialId()))
                .map(MaterialResponseDTO::derivado)
                .sorted(comparing(MaterialResponseDTO::nmbMaterial))
                .toList();
    }

    /**
     * Normaliza y valida que al menos un filtro esté presente
     *
     * @param texto     criterio de búsqueda por nombre
     * @param categoria criterio de búsqueda por categoría
     * @param tipo      criterio de búsqueda por tipo
     * @return objeto FiltroNormalizado con valores normalizados
     * @throws InventarioFoundExistException si ningún filtro está presente
     */
    private FiltroNormalizado normalizarYValidarFiltros(String texto, String categoria, String tipo) throws InventarioFoundExistException {
        String textoNorm = normalizarParametro(texto);
        String categoriaNorm = normalizarParametro(categoria);
        String tipoNorm = normalizarParametro(tipo);

        if (textoNorm.trim().isEmpty() && categoriaNorm.trim().isEmpty() && tipoNorm.trim().isEmpty()) {
            throw new InventarioFoundExistException(
                    "⚠️ Debes ingresar al menos un criterio de búsqueda. " +
                            "Por favor, escribe un nombre, selecciona una categoría o un tipo de material."
            );
        }

        return new FiltroNormalizado(textoNorm, categoriaNorm, tipoNorm);
    }

   private Set<UUID> obtenerMaterialesExistentes(UUID puntoId) {
        return inventarioRepository.findAllByPuntoEca_PuntoEcaID(puntoId).stream()
                .map(inventario -> inventario.getMaterial().getMaterialId())
                .collect(Collectors.toSet());
    }

    /**
     * Busca materiales activos que coinciden con los filtros especificados
     *
     * @param filtro objeto con filtros normalizados
     * @return lista de materiales que coinciden con los criterios
     */
    private List<Material> buscarMaterialesPorFiltro(FiltroNormalizado filtro) {
        return materialRepository.findAllActivos().stream()
                .filter(material -> filtro.textoVacio() || material.getNombre().toLowerCase().contains(filtro.textoNormal))
                .filter(material -> filtro.categoriaVacia() || material.getCtgMaterial().getNombre().toLowerCase().equals(filtro.categoriaNormal))
                .filter(material -> filtro.tipoVacio() || material.getTipoMaterial().getNombre().toLowerCase().equals(filtro.tipoNormal))
                .toList();
    }

    /**
     * Valida que no todos los materiales encontrados ya existan en el inventario
     *
     * @param materialesEncontrados lista de materiales encontrados
     * @param materialesExistentes  conjunto de IDs de materiales existentes
     * @throws InventarioFoundExistException si todos los materiales ya existen
     */
    private void validarQueNoTodosExisten(List<Material> materialesEncontrados, Set<UUID> materialesExistentes) throws InventarioFoundExistException {
        if (!materialesEncontrados.isEmpty()) {
            List<Material> materialesQueYaExisten = materialesEncontrados.stream()
                    .filter(material -> materialesExistentes.contains(material.getMaterialId()))
                    .toList();

            if (materialesQueYaExisten.size() == materialesEncontrados.size()) {
                int totalEncontrados = materialesEncontrados.size();
                if (totalEncontrados == 1) {
                    throw new InventarioFoundExistException(
                            "⚠️ El material '" + materialesEncontrados.getFirst().getNombre() +
                                    "' ya ha sido agregado al inventario de este punto ECA. No puedes agregar el mismo material dos veces."
                    );
                } else {
                    throw new InventarioFoundExistException(
                            "⚠️ Todos los " + totalEncontrados +
                                    " materiales encontrados con esos criterios ya han sido agregados al inventario de este punto ECA. " +
                                    "Intenta con diferentes filtros o busca otros materiales disponibles."
                    );
                }
            }
        }
    }

    @Override
    public List<MaterialInvResponseDTO> buscarMaterialExistentesFiltrandoInventario(UUID puntoId, String texto, String categoria, String tipo) throws InventarioFoundExistException {

        // Normalizar valores nulos a strings vacíos
        String textoNormalizado = normalizarParametro(texto);
        String categoriaNormalizada = normalizarParametro(categoria);
        String tipoNormalizado = normalizarParametro(tipo);

        // Validar que al menos un filtro esté presente
        if (textoNormalizado.trim().isEmpty() && categoriaNormalizada.trim().isEmpty() && tipoNormalizado.trim().isEmpty()) {
            throw new InventarioFoundExistException(
                    "⚠️ Debes ingresar al menos un criterio de búsqueda. " +
                            "Por favor, escribe un nombre, selecciona una categoría o un tipo de material."
            );
        }

        final String textoNormal = textoNormalizado.toLowerCase();
        final String categoriaNormal = categoriaNormalizada.toLowerCase();
        final String tipoNormal = tipoNormalizado.toLowerCase();

        // Obtener inventarios que ya existen en el punto, con sus materiales filtrados
        List<Inventario> inventariosExistentes = inventarioRepository.findAllByPuntoEca_PuntoEcaID(puntoId).stream()
                .filter(inventario -> textoNormalizado.isEmpty() || inventario.getMaterial().getNombre().toLowerCase().contains(textoNormal))
                .filter(inventario -> categoriaNormalizada.isEmpty() || inventario.getMaterial().getCtgMaterial().getNombre().toLowerCase().equals(categoriaNormal))
                .filter(inventario -> tipoNormalizado.isEmpty() || inventario.getMaterial().getTipoMaterial().getNombre().toLowerCase().equals(tipoNormal))
                .toList();

        // Convertir a DTOs pasando tanto Material como Inventario
        return inventariosExistentes.stream()
                .collect(Collectors.toMap(
                        inventario -> inventario.getMaterial().getMaterialId(),
                        Function.identity(),
                        (a,ignore) ->a
                )).values().stream()
                .map(inventario -> MaterialInvResponseDTO.derivado(inventario.getMaterial(), inventario))
                .sorted(comparing(MaterialInvResponseDTO::nmbMaterial))
                .toList();
    }

    private String normalizarParametro(String valor) {
        return valor != null ? valor : "";
    }

    /**
         * Clase interna para encapsular filtros normalizados
         */
        private record FiltroNormalizado(String textoNormal, String categoriaNormal, String tipoNormal) {
            private FiltroNormalizado(String textoNormal, String categoriaNormal, String tipoNormal) {
                this.textoNormal = textoNormal.toLowerCase();
                this.categoriaNormal = categoriaNormal.toLowerCase();
                this.tipoNormal = tipoNormal.toLowerCase();
            }

            boolean textoVacio() {
                return textoNormal.trim().isEmpty();
            }

            boolean categoriaVacia() {
                return categoriaNormal.trim().isEmpty();
            }

            boolean tipoVacio() {
                return tipoNormal.trim().isEmpty();
            }
        }
}

