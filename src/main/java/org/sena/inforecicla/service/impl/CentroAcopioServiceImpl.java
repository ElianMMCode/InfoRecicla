package org.sena.inforecicla.service.impl;

import lombok.AllArgsConstructor;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;
import org.sena.inforecicla.dto.CentroAcopioCreateDTO;
import org.sena.inforecicla.model.CentroAcopio;
import org.sena.inforecicla.model.Localidad;
import org.sena.inforecicla.model.PuntoECA;
import org.sena.inforecicla.model.enums.TipoCentroAcopio;
import org.sena.inforecicla.model.enums.Visibilidad;
import org.sena.inforecicla.repository.CentroAcopioRepository;
import org.sena.inforecicla.service.CentroAcopioService;
import org.sena.inforecicla.service.LocalidadService;
import org.sena.inforecicla.service.PuntoEcaService;
import org.springframework.stereotype.Service;

import java.util.List;
import java.util.ArrayList;
import java.util.UUID;

/**
 * Implementación del servicio de Centro de Acopio
 */
@Service
@AllArgsConstructor
public class CentroAcopioServiceImpl implements CentroAcopioService {

    private static final Logger logger = LoggerFactory.getLogger(CentroAcopioServiceImpl.class);
    private final CentroAcopioRepository centroAcopioRepository;
    private final PuntoEcaService puntoEcaService;
    private final LocalidadService localidadService;

    @Override
    public CentroAcopio crear(UUID puntoEcaId, CentroAcopioCreateDTO dto) {
        logger.info("➕ [CENTRO-ACOPIO] Creando nuevo centro para punto: {}", puntoEcaId);
        logger.info("   📋 Datos recibidos: nombre={}, tipo={}, celular={}, email={}, localidadId={}",
                dto.getNombreCntAcp(), dto.getTipoCntAcp(), dto.getCelular(), dto.getEmail(), dto.getLocalidadId());

        try {
            // Validar que el PuntoECA exista
            PuntoECA puntoEca = puntoEcaService.buscarPuntoEca(puntoEcaId)
                    .orElseThrow(() -> new IllegalArgumentException("Punto ECA no encontrado: " + puntoEcaId));
            logger.info("   ✅ Punto ECA encontrado: {}", puntoEca.getNombrePunto());

            // Validar que la localidad exista
            UUID localidadId = UUID.fromString(dto.getLocalidadId());
            Localidad localidad = localidadService.buscarPorId(localidadId)
                    .orElseThrow(() -> new IllegalArgumentException("Localidad no encontrada: " + localidadId));
            logger.info("   ✅ Localidad encontrada: {}", localidad.getNombre());

            // Crear la entidad CentroAcopio
            CentroAcopio centro = CentroAcopio.builder()
                    .nombreCntAcp(dto.getNombreCntAcp().trim())
                    .nombreContactoCntAcp(dto.getNombreContactoCntAcp() != null ?
                                         dto.getNombreContactoCntAcp().trim() : null)
                    .nota(dto.getNota() != null ? dto.getNota().trim() : null)
                    .descripcion(dto.getDescripcion() != null ? dto.getDescripcion().trim() : null)
                    .puntoEca(puntoEca)
                    .build();

            // Establecer campos heredados (atributos de la clase base)
            centro.setLocalidad(localidad);
            if (dto.getCiudad() != null) {
                centro.setCiudad(dto.getCiudad().trim());
            }
            centro.setLatitud(dto.getLatitud() != null ? dto.getLatitud() : 0.0);
            centro.setLongitud(dto.getLongitud() != null ? dto.getLongitud() : 0.0);

            // Establecer atributos heredados
            if (dto.getCelular() != null) {
                centro.setCelular(dto.getCelular().trim());
            }
            if (dto.getEmail() != null) {
                centro.setEmail(dto.getEmail().trim());
            }

            // Convertir el tipo a enum
            if (dto.getTipoCntAcp() != null && !dto.getTipoCntAcp().trim().isEmpty()) {
                try {
                    String tipoValue = dto.getTipoCntAcp().trim();
                    TipoCentroAcopio tipo = TipoCentroAcopio.porTipo(tipoValue);
                    centro.setTipoCntAcp(tipo);
                    logger.info("   ✅ Tipo convertido: {}", tipo.getTipo());
                } catch (IllegalArgumentException e) {
                    logger.error("   ❌ Tipo de centro inválido: {}", dto.getTipoCntAcp());
                    throw new IllegalArgumentException("Tipo de centro no válido. Valores permitidos: Planta, Proveedor, OTRO");
                }
            }

            // Establecer visibilidad (ECA por defecto para centros de un punto)
            Visibilidad visibilidad = Visibilidad.ECA;
            if (dto.getVisibilidad() != null && !dto.getVisibilidad().isEmpty()) {
                try {
                    visibilidad = Visibilidad.valueOf(dto.getVisibilidad().toUpperCase());
                    logger.info("   ✅ Visibilidad establecida: {}", visibilidad.getAlcance());
                } catch (IllegalArgumentException e) {
                    logger.warn("   ⚠️ Visibilidad inválida, usando ECA por defecto");
                    visibilidad = Visibilidad.ECA;
                }
            } else {
                logger.info("   ℹ️ Visibilidad no especificada, usando ECA por defecto");
            }
            centro.setVisibilidad(visibilidad);

            // Guardar el centro
            CentroAcopio centroGuardado = centroAcopioRepository.save(centro);
            logger.info("   ✅ Centro creado exitosamente: {}", centroGuardado.getCntAcpId());

            return centroGuardado;

        } catch (Exception e) {
            logger.error("   ❌ Error al crear centro: {}", e.getMessage(), e);
            throw e;
        }
    }

    @Override
    public List<CentroAcopio> listaCentrosPorPuntoEca(UUID puntoEcaId) {
        return centroAcopioRepository.findAllByPuntoEcaWithLocalidad(puntoEcaId);
    }

    @Override
    public List<CentroAcopio> obtenerCentrosGlobales() {
        // Obtener todos los centros con localidad cargada y filtrar aquellos cuyo puntoEca es null
        return centroAcopioRepository.findAllWithLocalidad().stream()
                .filter(c -> c.getPuntoEca() == null)
                .toList();
    }

    @Override
    public List<CentroAcopio> obtenerCentrosPuntoYGlobales(UUID puntoEcaId) {
        try {
            logger.info("🎯 Buscando centros para punto: {}", puntoEcaId);

            // Obtener TODOS los centros con localidad cargada
            List<CentroAcopio> todosCentros = centroAcopioRepository.findAllWithLocalidad();
            logger.info("📊 Total de centros en BD: {}", todosCentros.size());

            // Filtrar en memoria:
            // 1. Centros que pertenecen a ESTE punto específico (puntoEca.puntoEcaID == puntoEcaId)
            // 2. Centros GLOBALES (puntoEca == null)
            List<CentroAcopio> resultado = todosCentros.stream()
                    .filter(c -> {
                        // Caso 1: Centro del punto actual
                        if (c.getPuntoEca() != null &&
                            c.getPuntoEca().getPuntoEcaID() != null &&
                            c.getPuntoEca().getPuntoEcaID().equals(puntoEcaId)) {
                            logger.info("  ✅ Centro DEL PUNTO: {} (Localidad: {})",
                                    c.getNombreCntAcp(),
                                    c.getLocalidad() != null ? c.getLocalidad().getNombre() : "SIN LOCALIDAD");
                            return true;
                        }

                        // Caso 2: Centro global (sin punto asignado)
                        if (c.getPuntoEca() == null) {
                            logger.info("  🌍 Centro GLOBAL: {} (Localidad: {})",
                                    c.getNombreCntAcp(),
                                    c.getLocalidad() != null ? c.getLocalidad().getNombre() : "SIN LOCALIDAD");
                            return true;
                        }

                        // No incluir: centros de otros puntos
                        logger.debug("  ❌ Centro IGNORADO (otro punto): {}", c.getNombreCntAcp());
                        return false;
                    })
                    .toList();

            logger.info("✅ Total retornado: {}", resultado.size());
            return resultado;

        } catch (Exception e) {
            logger.error("❌ Error en obtenerCentrosPuntoYGlobales: {}", e.getMessage(), e);
            return new ArrayList<>();
        }
    }

    @Override
    public CentroAcopio obtenerPorId(UUID centroAcopioId) {
        return centroAcopioRepository.findById(centroAcopioId)
                .orElseThrow(() -> new IllegalArgumentException("Centro de acopio no encontrado: " + centroAcopioId));
    }

    @Override
    public CentroAcopio obtenerCentroValidoPunto(UUID centroId, UUID puntoId) {
        return centroAcopioRepository
                .findAllByPuntoEca_PuntoEcaIDAndCntAcpId(puntoId, centroId)
                .or(() -> centroAcopioRepository.findById(centroId))
                .orElse(null);
    }

    @Override
    public CentroAcopio actualizar(UUID centroId, CentroAcopio centroActualizado) {
        CentroAcopio centroExistente = obtenerPorId(centroId);

        if (centroActualizado.getNombreCntAcp() != null && !centroActualizado.getNombreCntAcp().isEmpty()) {
            centroExistente.setNombreCntAcp(centroActualizado.getNombreCntAcp());
        }
        if (centroActualizado.getCelular() != null && !centroActualizado.getCelular().isEmpty()) {
            centroExistente.setCelular(centroActualizado.getCelular());
        }
        if (centroActualizado.getEmail() != null && !centroActualizado.getEmail().isEmpty()) {
            centroExistente.setEmail(centroActualizado.getEmail());
        }
        if (centroActualizado.getNombreContactoCntAcp() != null && !centroActualizado.getNombreContactoCntAcp().isEmpty()) {
            centroExistente.setNombreContactoCntAcp(centroActualizado.getNombreContactoCntAcp());
        }
        if (centroActualizado.getNota() != null && !centroActualizado.getNota().isEmpty()) {
            centroExistente.setNota(centroActualizado.getNota());
        }
        if (centroActualizado.getTipoCntAcp() != null) {
            centroExistente.setTipoCntAcp(centroActualizado.getTipoCntAcp());
        }

        return centroAcopioRepository.save(centroExistente);
    }

    @Override
    public void eliminar(UUID centroId) {
        CentroAcopio centroExistente = obtenerPorId(centroId);
        centroAcopioRepository.delete(centroExistente);
    }

}

