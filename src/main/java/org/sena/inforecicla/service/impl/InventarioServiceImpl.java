package org.sena.inforecicla.service.impl;

import lombok.RequiredArgsConstructor;
import org.sena.inforecicla.dto.puntoEca.inventario.InventarioGuardarDTO;
import org.sena.inforecicla.dto.puntoEca.inventario.InventarioResponseDTO;
import org.sena.inforecicla.dto.puntoEca.inventario.InventarioUpdateDTO;
import org.sena.inforecicla.dto.puntoEca.materiales.CategoriaMaterialesInvResponseDTO;
import org.sena.inforecicla.dto.puntoEca.materiales.MaterialInvResponseDTO;
import org.sena.inforecicla.dto.puntoEca.materiales.TipoMaterialesInvResponseDTO;
import org.sena.inforecicla.exception.InventarioFoundExistException;
import org.sena.inforecicla.exception.InventarioNotFoundException;
import org.sena.inforecicla.exception.MaterialNotFoundException;
import org.sena.inforecicla.exception.PuntoEcaNotFoundException;
import org.sena.inforecicla.model.Inventario;
import org.sena.inforecicla.model.Material;
import org.sena.inforecicla.model.PuntoECA;
import org.sena.inforecicla.repository.*;
import org.sena.inforecicla.service.InventarioService;
import org.springframework.stereotype.Service;

import java.util.List;
import java.util.Set;
import java.util.UUID;
import java.util.stream.Collectors;

import static java.util.Comparator.comparing;

@Service
@RequiredArgsConstructor
public class InventarioServiceImpl implements InventarioService {

    private final InventarioRepository inventarioRepository;
    private final MaterialRepository materialRepository;
    private final TipoMaterialRepository tipoMaterialRepository;
    private final CategoriaMaterialRepository categoriaMaterialRepository;
    private final PuntoEcaRepository puntoEcaRepository;


    @Override
    public List<InventarioResponseDTO> mostrarInventarioPuntoEca(UUID puntoId) {
        return inventarioRepository.findAllByPuntoEca_PuntoEcaID(puntoId).stream()
                .map(InventarioResponseDTO::derivado)
                .sorted(comparing(InventarioResponseDTO::fechaActualizacion).reversed())
                .toList();
    }

    @Override
    public InventarioResponseDTO actualizarInventario(UUID inventarioId, InventarioUpdateDTO invUpdate) throws InventarioNotFoundException {
        Inventario inventario = inventarioRepository.findById(inventarioId).orElseThrow(
                () -> new InventarioNotFoundException("Registro en el inventario no encontrado"));

        inventario.setStockActual(invUpdate.stockActual());
        inventario.setCapacidadMaxima(invUpdate.capacidadMaxima());
        inventario.setUnidadMedida(invUpdate.unidadMedida());
        inventario.setPrecioCompra(invUpdate.precioCompra());
        inventario.setPrecioVenta(invUpdate.precioVenta());
        inventario.setUmbralAlerta(invUpdate.umbralAlerta());
        inventario.setUmbralCritico(invUpdate.umbralCritico());

        Inventario guardado = inventarioRepository.save(inventario);

        return InventarioResponseDTO.derivado(guardado);
    }

    @Override
    public List<TipoMaterialesInvResponseDTO> listarTiposMateriales() {
        return tipoMaterialRepository.findAll().stream()
                .map(TipoMaterialesInvResponseDTO::derivado)
                .sorted(comparing(TipoMaterialesInvResponseDTO::nmbCategoria))
                .toList();
    }

    @Override
    public List<CategoriaMaterialesInvResponseDTO> listarCategoriasMateriales() {
        return categoriaMaterialRepository.findAll().stream()
                .map(CategoriaMaterialesInvResponseDTO::derivado)
                .sorted(comparing(CategoriaMaterialesInvResponseDTO::nmbCategoria))
                .toList();
    }

    @Override
    public List<MaterialInvResponseDTO> buscarMaterial(UUID puntoId, String texto, String categoria, String tipo) throws InventarioFoundExistException {

        // Normalizar valores nulos a strings vacíos
        String textoNormalizado = texto != null ? texto : "";
        String categoriaNormalizada = categoria != null ? categoria : "";
        String tipoNormalizado = tipo != null ? tipo : "";

        // Validar que al menos un filtro esté presente
        if (textoNormalizado.trim().isEmpty() && categoriaNormalizada.trim().isEmpty() && tipoNormalizado.trim().isEmpty()) {
            throw new InventarioFoundExistException(
                "⚠️ Debes ingresar al menos un criterio de búsqueda. " +
                "Por favor, escribe un nombre, selecciona una categoría o un tipo de material."
            );
        }

        Set<UUID> materialesExistentes = inventarioRepository.findAllByPuntoEca_PuntoEcaID(puntoId).stream()
                .map(inventario -> inventario.getMaterial().getMaterialId())
                .collect(Collectors.toSet());

        final String textoNormal = textoNormalizado.toLowerCase();
        final String categoriaNormal = categoriaNormalizada.toLowerCase();
        final String tipoNormal = tipoNormalizado.toLowerCase();

        // Obtener todos los materiales que coinciden con los filtros
        List<Material> materialesEncontrados = materialRepository.findAll().stream()
                .filter(material -> textoNormalizado.isEmpty() || material.getNombre().toLowerCase().contains(textoNormal))
                .filter(material -> categoriaNormalizada.isEmpty() || material.getCtgMaterial().getNombre().toLowerCase().equals(categoriaNormal))
                .filter(material -> tipoNormalizado.isEmpty() || material.getTipoMaterial().getNombre().toLowerCase().equals(tipoNormal))
                .toList();

        System.out.println("DEBUG buscarMaterial - PuntoId: " + puntoId);
        System.out.println("DEBUG materiales existentes en inventario: " + materialesExistentes.size());
        System.out.println("DEBUG materiales encontrados en búsqueda: " + materialesEncontrados.size());

        // Verificar si hay materiales encontrados y todos ya existen en el inventario
        if (!materialesEncontrados.isEmpty()) {
            // Contar cuántos de los materiales encontrados YA existen en el inventario
            List<Material> materialesQueYaExisten = materialesEncontrados.stream()
                    .filter(material -> materialesExistentes.contains(material.getMaterialId()))
                    .toList();

            System.out.println("DEBUG materiales que ya existen en el inventario: " + materialesQueYaExisten.size());

            // Si TODOS los materiales encontrados ya existen en el inventario
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

        // Filtrar solo los materiales que NO existen en el inventario
        return materialesEncontrados.stream()
                .filter(material -> !materialesExistentes.contains(material.getMaterialId()))
                .map(MaterialInvResponseDTO::derivado)
                .sorted(comparing(MaterialInvResponseDTO::nmbMaterial))
                .toList();
    }

    @Override
    public void guardarInventario(InventarioGuardarDTO dto) throws MaterialNotFoundException, PuntoEcaNotFoundException {

        System.out.println("🔍 Iniciando guardarInventario con DTO: " + dto);

        System.out.println("1️⃣ Buscando material con ID: " + dto.materialId());
        Material material = materialRepository.findById(dto.materialId())
                .orElseThrow(() -> {
                    System.err.println("❌ Material no encontrado");
                    return new MaterialNotFoundException("Material no encontrado");
                });
        System.out.println("✅ Material encontrado: " + material.getNombre());

        System.out.println("2️⃣ Buscando punto ECA con ID: " + dto.puntoEcaId());
        PuntoECA punto = puntoEcaRepository.findById(dto.puntoEcaId())
                .orElseThrow(() -> {
                    System.err.println("❌ Punto ECA no encontrado");
                    return new PuntoEcaNotFoundException("Punto ECA no encontrado");
                });
        System.out.println("✅ Punto ECA encontrado: " + punto.getNombrePunto());

        System.out.println("3️⃣ Construyendo objeto Inventario...");
        Inventario inv = Inventario.builder()
                .capacidadMaxima(dto.capacidadMaxima())
                .unidadMedida(dto.unidadMedida())
                .stockActual(dto.stockActual())
                .umbralAlerta(dto.umbralAlerta())
                .umbralCritico(dto.umbralCritico())
                .precioVenta(dto.precioVenta())
                .precioCompra(dto.precioCompra())
                .material(material)
                .puntoEca(punto)
                .build();

        // Asignar estado automáticamente en el servidor
        inv.setEstado(org.sena.inforecicla.model.enums.Estado.Activo);

        System.out.println("✅ Objeto Inventario construido");

        System.out.println("4️⃣ Guardando en la base de datos...");
        inventarioRepository.save(inv);
        System.out.println("✅ Inventario guardado exitosamente");
    }
}

