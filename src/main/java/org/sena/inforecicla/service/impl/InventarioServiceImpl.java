package org.sena.inforecicla.service.impl;

import lombok.RequiredArgsConstructor;
import org.sena.inforecicla.dto.puntoEca.inventario.InventarioRequestDTO;
import org.sena.inforecicla.dto.puntoEca.inventario.InventarioResponseDTO;
import org.sena.inforecicla.dto.puntoEca.inventario.InventarioUpdateDTO;
import org.sena.inforecicla.exception.InventarioNotFoundException;
import org.sena.inforecicla.exception.PuntoEcaNotFoundException;
import org.sena.inforecicla.model.Inventario;
import org.sena.inforecicla.model.PuntoECA;
import org.sena.inforecicla.model.enums.Alerta;
import org.sena.inforecicla.model.enums.Estado;
import org.sena.inforecicla.repository.*;
import org.sena.inforecicla.service.InventarioService;
import org.sena.inforecicla.service.PuntoEcaService;
import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;

import java.math.BigDecimal;
import java.math.RoundingMode;
import java.util.List;
import java.util.UUID;

import static java.util.Comparator.comparing;

@Service
@RequiredArgsConstructor
public class InventarioServiceImpl implements InventarioService {

    private final InventarioRepository inventarioRepository;
    private final PuntoEcaService puntoEcaService;
    private final MaterialRepository materialRepository;

    // ...existing code...

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

        // Calcular estado de alerta basado en ocupación y umbrales actualizados
        BigDecimal ocupacion = invUpdate.stockActual()
                .divide(invUpdate.capacidadMaxima(), 2, RoundingMode.HALF_UP)
                .multiply(BigDecimal.valueOf(100));

        Alerta estadoAlerta = Alerta.OK;
        if (ocupacion.compareTo(BigDecimal.valueOf(invUpdate.umbralCritico())) >= 0) {
            estadoAlerta = Alerta.Critico;
        } else if (ocupacion.compareTo(BigDecimal.valueOf(invUpdate.umbralAlerta())) >= 0) {
            estadoAlerta = Alerta.Alerta;
        }

        inventario.setAlerta(estadoAlerta);

        Inventario actualizado = inventarioRepository.save(inventario);

        return InventarioResponseDTO.derivado(actualizado);
    }

    @Override
    @Transactional
    public void guardarInventario(InventarioRequestDTO dto) throws PuntoEcaNotFoundException {

        boolean existe = inventarioRepository.existsByPuntoEca_PuntoEcaIDAndMaterial_MaterialId(
                dto.puntoEcaId(),
                dto.materialId()
        );

        if (existe) {
            throw new IllegalArgumentException(
                    "Este material ya existe en el inventario de este punto"
            );
        }

        // Obtener Punto ECA a través del servicio
        PuntoECA punto = puntoEcaService.buscarPuntoEca(dto.puntoEcaId())
                .orElseThrow(() -> new PuntoEcaNotFoundException("Punto ECA no encontrado con ID: " + dto.puntoEcaId()));

        // Obtener Material desde el repository
        var material = materialRepository.findById(dto.materialId())
                .orElseThrow(() -> new PuntoEcaNotFoundException("Material no encontrado con ID: " + dto.materialId()));

        // Calcular estado de alerta basado en ocupación y umbrales
        BigDecimal ocupacion = dto.stockActual()
                .divide(dto.capacidadMaxima(), 2, RoundingMode.HALF_UP)
                .multiply(BigDecimal.valueOf(100));

        Alerta estadoAlerta = Alerta.OK;
        if (ocupacion.compareTo(BigDecimal.valueOf(dto.umbralCritico())) >= 0) {
            estadoAlerta = Alerta.Critico;
        } else if (ocupacion.compareTo(BigDecimal.valueOf(dto.umbralAlerta())) >= 0) {
            estadoAlerta = Alerta.Alerta;
        }

        Inventario inv = Inventario.builder()
                .capacidadMaxima(dto.capacidadMaxima())
                .unidadMedida(dto.unidadMedida())
                .stockActual(dto.stockActual())
                .umbralAlerta(dto.umbralAlerta())
                .umbralCritico(dto.umbralCritico())
                .precioVenta(dto.precioVenta())
                .precioCompra(dto.precioCompra())
                .puntoEca(punto)
                .material(material)
                .alerta(estadoAlerta)
                .build();

        // Asignar estado automáticamente en el servidor
        inv.setEstado(Estado.Activo);

        inventarioRepository.save(inv);
    }

    @Override
    public List<InventarioResponseDTO> filtraInventario(UUID gestorId, String texto, String categoria, String tipo, Alerta alerta, String unidad, String ocupacion) {
        String textoNormalizado = texto != null ? texto : "";
        String categoriaNormalizada = categoria != null ? categoria : "";
        String tipoNormalizado = tipo != null ? tipo : "";
        String ocupacionNormalizada = ocupacion != null ? ocupacion : "";

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

    @Override
    public void eliminarInventario(UUID inventarioId, UUID puntoId) throws InventarioNotFoundException {
        // Este método fue movido a InventarioEliminacionService para evitar referencia circular
        // No debe llamarse directamente desde aquí
        throw new UnsupportedOperationException("Use InventarioEliminacionService.eliminarInventarioConMovimientos() en su lugar");
    }

    private boolean verificarOcupacion(Inventario inv, String ocupacion) {
        BigDecimal stock = inv.getStockActual();
        BigDecimal capacidad = inv.getCapacidadMaxima();

        int porcentajeOcupacion = capacidad.compareTo(BigDecimal.ZERO) == 0 ? 0 :
                stock.multiply(new BigDecimal(100))
                        .divide(capacidad, 0, RoundingMode.HALF_UP)
                        .intValue();

        return switch (ocupacion) {
            case "0-25" -> porcentajeOcupacion >= 0 && porcentajeOcupacion <= 25;
            case "25-50" -> porcentajeOcupacion > 25 && porcentajeOcupacion <= 50;
            case "50-75" -> porcentajeOcupacion > 50 && porcentajeOcupacion <= 75;
            case "75-100" -> porcentajeOcupacion > 75 && porcentajeOcupacion <= 100;
            default -> false;
        };
    }

    @Transactional(readOnly = true)
    @Override
    public Inventario obtenerInventarioValido(UUID inventarioId, UUID puntoId, UUID materialId) throws InventarioNotFoundException {
        return inventarioRepository
                .findByInventarioIdAndPuntoEca_PuntoEcaIDAndMaterial_MaterialId(inventarioId, puntoId, materialId)
                .orElseThrow(() -> new InventarioNotFoundException(
                        "No existe inventario con esos datos (inventario, punto, material)"
                ));
    }

    @Override
    public void actualizarStock(Inventario inv){
        inventarioRepository.save(inv);
    }

}

