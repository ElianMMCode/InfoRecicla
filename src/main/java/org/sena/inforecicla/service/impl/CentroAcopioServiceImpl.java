package org.sena.inforecicla.service.impl;

import lombok.AllArgsConstructor;
import org.sena.inforecicla.model.CentroAcopio;
import org.sena.inforecicla.repository.CentroAcopioRepository;
import org.sena.inforecicla.service.CentroAcopioService;
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


    private final CentroAcopioRepository centroAcopioRepository;

    @Override
    public List<CentroAcopio> listaCentrosPorPuntoEca(UUID puntoEcaId) {
        return centroAcopioRepository.findAll().stream()
                .filter(c -> c.getPuntoEca() != null && c.getPuntoEca().getPuntoEcaID() != null && c.getPuntoEca().getPuntoEcaID().equals(puntoEcaId))
                .toList();
    }

    @Override
    public List<CentroAcopio> obtenerCentrosGlobales() {
        // Obtener todos los centros y filtrar aquellos cuyo puntoEca es null
        return centroAcopioRepository.findAll().stream()
                .filter(c -> c.getPuntoEca() == null)
                .toList();
    }

    @Override
    public List<CentroAcopio> obtenerCentrosPuntoYGlobales(UUID puntoEcaId) {
        try {
            System.out.println("🎯 Buscando centros para punto: " + puntoEcaId);

            // Obtener TODOS los centros de la BD
            List<CentroAcopio> todosCentros = centroAcopioRepository.findAll();
            System.out.println("📊 Total de centros en BD: " + todosCentros.size());

            // Filtrar en memoria:
            // 1. Centros que pertenecen a ESTE punto específico (puntoEca.puntoEcaID == puntoEcaId)
            // 2. Centros GLOBALES (puntoEca == null)
            List<CentroAcopio> resultado = todosCentros.stream()
                    .filter(c -> {
                        // Caso 1: Centro del punto actual
                        if (c.getPuntoEca() != null &&
                            c.getPuntoEca().getPuntoEcaID() != null &&
                            c.getPuntoEca().getPuntoEcaID().equals(puntoEcaId)) {
                            System.out.println("  ✅ Centro DEL PUNTO: " + c.getNombreCntAcp() +
                                             " (Tipo: " + c.getVisibilidad() + ")");
                            return true;
                        }

                        // Caso 2: Centro global (sin punto asignado)
                        if (c.getPuntoEca() == null) {
                            System.out.println("  🌍 Centro GLOBAL: " + c.getNombreCntAcp() +
                                             " (Tipo: " + c.getVisibilidad() + ")");
                            return true;
                        }

                        // No incluir: centros de otros puntos
                        System.out.println("  ❌ Centro IGNORADO (otro punto): " + c.getNombreCntAcp());
                        return false;
                    })
                    .toList();

            System.out.println("✅ Total retornado: " + resultado.size());
            return resultado;

        } catch (Exception e) {
            System.err.println("❌ Error en obtenerCentrosPuntoYGlobales: " + e.getMessage());
            e.printStackTrace();
            return new ArrayList<>();
        }
    }

    @Override
    public CentroAcopio obtenerPorId(UUID centroAcopioId) {
        return centroAcopioRepository.findById(centroAcopioId)
                .orElseThrow(() -> {
                    return new IllegalArgumentException("Centro de acopio no encontrado: " + centroAcopioId);
                });
    }

   @Override
    public CentroAcopio obtenerCentroValidoPunto(UUID centroId, UUID puntoId) {
        return centroAcopioRepository
                .findAllByPuntoEca_PuntoEcaIDAndCntAcpId(puntoId, centroId)
                .or(() -> centroAcopioRepository.findById(centroId))
                .orElse(null);
    }

}

