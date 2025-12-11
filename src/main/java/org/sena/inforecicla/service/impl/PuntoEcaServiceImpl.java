package org.sena.inforecicla.service.impl;

import lombok.RequiredArgsConstructor;
import org.sena.inforecicla.dto.puntoEca.PuntoEcaMapDTO;
import org.sena.inforecicla.dto.puntoEca.PuntoEcaDetalleDTO;
import org.sena.inforecicla.model.PuntoECA;
import org.sena.inforecicla.model.Inventario;
import org.sena.inforecicla.model.enums.Estado;
import org.sena.inforecicla.repository.PuntoEcaRepository;
import org.sena.inforecicla.service.PuntoEcaService;
import org.springframework.stereotype.Service;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;

import java.util.List;
import java.util.Optional;
import java.util.UUID;
import java.util.stream.Collectors;

@Service
@RequiredArgsConstructor
public class PuntoEcaServiceImpl implements PuntoEcaService {

    private static final Logger logger = LoggerFactory.getLogger(PuntoEcaServiceImpl.class);
    private final PuntoEcaRepository puntoEcaRepository;

    @Override
    public Optional<PuntoECA> buscarPuntoEca(UUID puntoId) {
        return puntoEcaRepository.findById(puntoId);
    }

    @Override
    public Optional<PuntoECA> buscarPuntoEcaEstado(UUID puntoId, Estado estado) {
        return puntoEcaRepository.findByPuntoEcaIDAndEstado(puntoId, estado);
    }

    @Override
    public List<PuntoEcaMapDTO> obtenerTodosPuntosEcaActivos() {
        // Obtener todos los puntos ECA activos
        List<PuntoECA> puntosECA = puntoEcaRepository.findAll().stream()
                .filter(p -> p.getEstado() == Estado.Activo)
                .filter(p -> p.getLatitud() != null && p.getLongitud() != null) // Solo con coordenadas válidas
                .toList();

        return puntosECA.stream()
                .map(this::toPuntoEcaMapDTO)
                .toList();
    }

    @Override
    public PuntoEcaMapDTO toPuntoEcaMapDTO(PuntoECA puntoECA) {
        return PuntoEcaMapDTO.builder()
                .puntoEcaID(puntoECA.getPuntoEcaID())
                .nombrePunto(puntoECA.getNombrePunto())
                .latitud(puntoECA.getLatitud())
                .longitud(puntoECA.getLongitud())
                .direccion(puntoECA.getDireccion())
                .ciudad(puntoECA.getCiudad())
                .localidadNombre(puntoECA.getLocalidad() != null ? puntoECA.getLocalidad().getNombre() : "")
                .celular(puntoECA.getCelular())
                .email(puntoECA.getEmail())
                .descripcion(puntoECA.getDescripcion())
                .horarioAtencion(puntoECA.getHorarioAtencion())
                .build();
    }

    @Override
    public PuntoEcaDetalleDTO obtenerDetallesPuntoEca(UUID puntoEcaId) {
        logger.info("📊 Buscando detalles para punto ECA: {}", puntoEcaId);

        Optional<PuntoECA> puntoOpt = puntoEcaRepository.findById(puntoEcaId);

        if (puntoOpt.isEmpty()) {
            logger.warn("⚠️ Punto ECA no encontrado: {}", puntoEcaId);
            return null;
        }

        PuntoECA punto = puntoOpt.get();

        // Construir lista de materiales con inventario
        List<PuntoEcaDetalleDTO.MaterialInventarioDTO> materiales = punto.getInventarios().stream()
                .filter(inv -> inv.getEstado() == Estado.Activo)
                .map(this::toMaterialInventarioDTO)
                .collect(Collectors.toList());

        logger.info("✅ Encontrados {} materiales para punto ECA: {}", materiales.size(), punto.getNombrePunto());

        return PuntoEcaDetalleDTO.builder()
                .puntoEcaID(punto.getPuntoEcaID())
                .nombrePunto(punto.getNombrePunto())
                .latitud(punto.getLatitud())
                .longitud(punto.getLongitud())
                .direccion(punto.getDireccion())
                .ciudad(punto.getCiudad())
                .localidadNombre(punto.getLocalidad() != null ? punto.getLocalidad().getNombre() : "")
                .celular(punto.getCelular())
                .email(punto.getEmail())
                .telefonoPunto(punto.getTelefonoPunto())
                .descripcion(punto.getDescripcion())
                .horarioAtencion(punto.getHorarioAtencion())
                .materiales(materiales)
                .build();
    }

    private PuntoEcaDetalleDTO.MaterialInventarioDTO toMaterialInventarioDTO(Inventario inventario) {
        double porcentaje = 0;
        double stockActual = 0;
        double capacidadMaxima = 0;
        double precioCompra = 0;

        // Convertir BigDecimal a double de forma segura
        if (inventario.getStockActual() != null) {
            stockActual = inventario.getStockActual().doubleValue();
        }

        if (inventario.getCapacidadMaxima() != null && inventario.getCapacidadMaxima().doubleValue() > 0) {
            capacidadMaxima = inventario.getCapacidadMaxima().doubleValue();
            porcentaje = (stockActual / capacidadMaxima) * 100;
        }

        if (inventario.getPrecioCompra() != null) {
            precioCompra = inventario.getPrecioCompra().doubleValue();
        }

        return PuntoEcaDetalleDTO.MaterialInventarioDTO.builder()
                .inventarioId(inventario.getInventarioId())
                .nombreMaterial(inventario.getMaterial() != null ? inventario.getMaterial().getNombre() : "Desconocido")
                .categoriaMaterial(inventario.getMaterial() != null && inventario.getMaterial().getCtgMaterial() != null ?
                        inventario.getMaterial().getCtgMaterial().getNombre() : "Sin categoría")
                .tipoMaterial(inventario.getMaterial() != null && inventario.getMaterial().getTipoMaterial() != null ?
                        inventario.getMaterial().getTipoMaterial().getNombre() : "Sin tipo")
                .stockActual(stockActual)
                .capacidadMaxima(capacidadMaxima)
                .unidadMedida(inventario.getUnidadMedida() != null ? inventario.getUnidadMedida().toString() : "Unidad")
                .precioBuyPrice(precioCompra)
                .porcentajeCapacidad(porcentaje)
                .build();
    }
}

