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
import org.sena.inforecicla.model.enums.Alerta;
import org.sena.inforecicla.model.enums.Estado;
import org.sena.inforecicla.repository.*;
import org.sena.inforecicla.service.InventarioService;
import org.springframework.stereotype.Service;

import java.math.BigDecimal;
import java.math.RoundingMode;
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
                .filter(inv -> inv.getEstado() == Estado.Activo)
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
        return tipoMaterialRepository.findAllActivos().stream()
                .map(TipoMaterialesInvResponseDTO::derivado)
                .sorted(comparing(TipoMaterialesInvResponseDTO::nmbCategoria))
                .toList();
    }

    @Override
    public List<CategoriaMaterialesInvResponseDTO> listarCategoriasMateriales() {
        return categoriaMaterialRepository.findAllActivos().stream()
                .map(CategoriaMaterialesInvResponseDTO::derivado)
                .sorted(comparing(CategoriaMaterialesInvResponseDTO::nmbCategoria))
                .toList();
    }

    @Override
    public List<MaterialInvResponseDTO> buscarMaterialFiltrandoInventario(UUID puntoId, String texto, String categoria, String tipo) throws InventarioFoundExistException {

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
        List<Material> materialesEncontrados = materialRepository.findAllActivos().stream()
                .filter(material -> textoNormalizado.isEmpty() || material.getNombre().toLowerCase().contains(textoNormal))
                .filter(material -> categoriaNormalizada.isEmpty() || material.getCtgMaterial().getNombre().toLowerCase().equals(categoriaNormal))
                .filter(material -> tipoNormalizado.isEmpty() || material.getTipoMaterial().getNombre().toLowerCase().equals(tipoNormal))
                .toList();

        // Verificar si hay materiales encontrados y todos ya existen en el inventario
        if (!materialesEncontrados.isEmpty()) {
            // Contar cuántos de los materiales encontrados YA existen en el inventario
            List<Material> materialesQueYaExisten = materialesEncontrados.stream()
                    .filter(material -> materialesExistentes.contains(material.getMaterialId()))
                    .toList();


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
        Material material = materialRepository.findById(dto.materialId())
                .orElseThrow(() -> new MaterialNotFoundException("Material no encontrado"));

        PuntoECA punto = puntoEcaRepository.findById(dto.puntoEcaId())
                .orElseThrow(() -> new PuntoEcaNotFoundException("Punto ECA no encontrado"));

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

        inventarioRepository.save(inv);
    }

    @Override
    public List<InventarioResponseDTO> filtraInventario(UUID gestorId, String texto, String categoria, String tipo, Alerta alerta, String unidad, String ocupacion) throws InventarioFoundExistException {
        String textoNormalizado = texto != null ? texto : "";
        String categoriaNormalizada = categoria != null ? categoria : "";
        String tipoNormalizado = tipo != null ? tipo : "";
        String ocupacionNormalizada = ocupacion != null ? ocupacion : "";

        // Validar que al menos un filtro esté presente
        // Se permite: texto, categoria, tipo, alerta, unidad, O ocupacion
        boolean hayAlgunFiltro = !textoNormalizado.trim().isEmpty() ||
                                !categoriaNormalizada.trim().isEmpty() ||
                                !tipoNormalizado.trim().isEmpty() ||
                                (alerta != null) ||
                                (unidad != null && !unidad.trim().isEmpty()) ||
                                !ocupacionNormalizada.trim().isEmpty();

        if (!hayAlgunFiltro) {
            throw new InventarioFoundExistException(
                    "⚠️ Debes ingresar al menos un criterio de búsqueda. "
            );
        }

        final String textoNormal = textoNormalizado.toLowerCase();
        final String categoriaNormal = categoriaNormalizada.toLowerCase();
        final String tipoNormal = tipoNormalizado.toLowerCase();

        var inventarios = inventarioRepository.findAllByPuntoEca_PuntoEcaID(gestorId);


        return inventarios.stream()
                .filter(inv -> inv.getEstado() == Estado.Activo)
                .filter(inv -> textoNormalizado.isEmpty() || inv.getMaterial().getNombre().toLowerCase().contains(textoNormal))
                .filter(inv -> {
                    String catBD = inv.getMaterial().getCtgMaterial() != null ? inv.getMaterial().getCtgMaterial().getNombre().toLowerCase() : "NULL";
                    return categoriaNormalizada.isEmpty() || catBD.equals(categoriaNormal);
                })
                .filter(inv -> {
                    String tipoBD = inv.getMaterial().getTipoMaterial() != null ? inv.getMaterial().getTipoMaterial().getNombre().toLowerCase() : "NULL";
                    return tipoNormalizado.isEmpty() || tipoBD.equals(tipoNormal);
                })
                .filter(inv -> alerta == null || inv.getAlerta() == alerta)
                .filter(inv -> (unidad == null || unidad.isEmpty()) || (inv.getUnidadMedida() != null && inv.getUnidadMedida().name().equals(unidad)))
                .filter(inv -> ocupacionNormalizada.isEmpty() || verificarOcupacion(inv, ocupacionNormalizada))
                .map(InventarioResponseDTO::derivado)
                .sorted(comparing(InventarioResponseDTO::fechaActualizacion).reversed())
                .toList();
    }

    private boolean verificarOcupacion(Inventario inv, String ocupacion) {
        BigDecimal stock = inv.getStockActual();
        BigDecimal capacidad = inv.getCapacidadMaxima();

        int porcentajeOcupacion = capacidad.compareTo(BigDecimal.ZERO) == 0 ? 0 :
                stock.multiply(new BigDecimal(100))
                        .divide(capacidad, 0, RoundingMode.HALF_UP)
                        .intValue();

        return switch(ocupacion) {
            case "0-25" -> porcentajeOcupacion >= 0 && porcentajeOcupacion <= 25;
            case "25-50" -> porcentajeOcupacion > 25 && porcentajeOcupacion <= 50;
            case "50-75" -> porcentajeOcupacion > 50 && porcentajeOcupacion <= 75;
            case "75-100" -> porcentajeOcupacion > 75 && porcentajeOcupacion <= 100;
            default -> false;
        };
    }

    @Override
    public void eliminarInventario(UUID inventarioId) throws InventarioNotFoundException {
        Inventario inventario = inventarioRepository.findById(inventarioId).orElseThrow(
                () -> new InventarioNotFoundException("Inventario no encontrado con ID: " + inventarioId)
        );
        inventario.setEstado(Estado.Inactivo);
        inventarioRepository.save(inventario);
    }
}

