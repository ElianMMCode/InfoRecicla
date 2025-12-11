package org.sena.inforecicla.controller;

import lombok.AllArgsConstructor;
import org.sena.inforecicla.dto.puntoEca.PuntoEcaMapDTO;
import org.sena.inforecicla.dto.puntoEca.PuntoEcaDetalleDTO;
import org.sena.inforecicla.service.PuntoEcaService;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;
import org.springframework.stereotype.Controller;
import org.springframework.ui.Model;
import org.springframework.web.bind.annotation.*;

import java.util.List;

/**
 * Controlador para la visualización de mapas interactivos
 * Accesible para todos los usuarios sin requerimientos de autenticación específicos
 */
@Controller
@AllArgsConstructor
@RequestMapping("/mapa")
public class MapaController {

    private static final Logger logger = LoggerFactory.getLogger(MapaController.class);

    private final PuntoEcaService puntoEcaService;

    /**
     * Ruta principal para servir la vista del mapa interactivo con puntos ECA
     * Accesible para todos los usuarios
     *
     * @param model Modelo para pasar datos a la vista
     * @return Vista del mapa interactivo
     */
    @GetMapping
    public String verMapaPuntosEca(Model model) {
        try {
            logger.info("═════════════════════════════════════════════════════════════");
            logger.info("🗺️  CARGANDO VISTA DEL MAPA INTERACTIVO");
            logger.info("═════════════════════════════════════════════════════════════");

            // Obtener lista de puntos ECA activos con coordenadas válidas
            List<PuntoEcaMapDTO> puntos = puntoEcaService.obtenerTodosPuntosEcaActivos();

            logger.info("✅ Total de puntos ECA cargados: {}", puntos.size());

            // Pasar datos al template
            model.addAttribute("puntos", puntos);
            model.addAttribute("totalPuntos", puntos.size());

            logger.info("📍 Datos listos para renderizar la vista del mapa");
            logger.info("═════════════════════════════════════════════════════════════");

            return "views/Mapa/mapa-interactivo";

        } catch (Exception e) {
            logger.error("❌ Error al cargar vista del mapa: {}", e.getMessage(), e);
            model.addAttribute("error", "Error al cargar los puntos ECA");
            return "views/Mapa/mapa-interactivo";
        }
    }

    /**
     * Endpoint REST para obtener todos los puntos ECA en formato JSON
     * Utilizado por el frontend (JavaScript) para cargar marcadores en el mapa
     * Accesible para todas las solicitudes incluyendo AJAX
     *
     * @return Lista de PuntoEcaMapDTO con información de ubicación
     */
    @GetMapping("/api/puntos-eca")
    @ResponseBody
    public List<PuntoEcaMapDTO> obtenerPuntosEcaJson() {
        try {
            logger.info("🔄 Solicitud de puntos ECA en formato JSON");

            List<PuntoEcaMapDTO> puntos = puntoEcaService.obtenerTodosPuntosEcaActivos();

            logger.info("✅ Retornando {} puntos ECA activos para mapa", puntos.size());

            return puntos;

        } catch (Exception e) {
            logger.error("❌ Error al obtener puntos ECA: {}", e.getMessage(), e);
            throw new RuntimeException("Error al cargar puntos ECA", e);
        }
    }

    /**
     * Endpoint REST para obtener un punto ECA específico por su ID
     * Utilizado para mostrar detalles completos de un punto al hacer click
     *
     * @param puntoEcaId ID del punto ECA a buscar
     * @return PuntoEcaMapDTO con información del punto solicitado
     */
    @GetMapping("/api/puntos-eca/{puntoEcaId}")
    @ResponseBody
    public PuntoEcaMapDTO obtenerPuntoEcaPorId(@PathVariable String puntoEcaId) {
        try {
            logger.info("🔍 Buscando punto ECA con ID: {}", puntoEcaId);

            var puntoOptional = puntoEcaService.buscarPuntoEca(java.util.UUID.fromString(puntoEcaId));

            if (puntoOptional.isPresent()) {
                PuntoEcaMapDTO puntoDTO = puntoEcaService.toPuntoEcaMapDTO(puntoOptional.get());
                logger.info("✅ Punto ECA encontrado: {}", puntoDTO.getNombrePunto());
                return puntoDTO;
            } else {
                logger.warn("⚠️ Punto ECA no encontrado: {}", puntoEcaId);
                throw new RuntimeException("Punto ECA no encontrado");
            }

        } catch (IllegalArgumentException e) {
            logger.error("❌ ID de punto ECA inválido: {}", puntoEcaId);
            throw new RuntimeException("ID de punto ECA inválido", e);
        } catch (Exception e) {
            logger.error("❌ Error al obtener punto ECA: {}", e.getMessage(), e);
            throw new RuntimeException("Error al obtener punto ECA", e);
        }
    }

    /**
     * Endpoint REST para buscar puntos ECA por nombre
     * Utilizado para la funcionalidad de búsqueda en el sidebar del mapa
     *
     * @param termino Término de búsqueda (nombre del punto)
     * @return Lista de puntos ECA que coinciden con el término
     */
    @GetMapping("/api/puntos-eca/buscar")
    @ResponseBody
    public List<PuntoEcaMapDTO> buscarPuntosPorNombre(@RequestParam String termino) {
        try {
            logger.info("🔎 Buscando puntos ECA con término: '{}'", termino);

            List<PuntoEcaMapDTO> puntos = puntoEcaService.obtenerTodosPuntosEcaActivos();

            // Filtrar por término de búsqueda (case-insensitive)
            List<PuntoEcaMapDTO> puntosFiltrados = puntos.stream()
                    .filter(p -> p.getNombrePunto().toLowerCase().contains(termino.toLowerCase()) ||
                               (p.getLocalidadNombre() != null && p.getLocalidadNombre().toLowerCase().contains(termino.toLowerCase())))
                    .toList();

            logger.info("✅ Se encontraron {} puntos que coinciden con el término", puntosFiltrados.size());

            return puntosFiltrados;

        } catch (Exception e) {
            logger.error("❌ Error al buscar puntos ECA: {}", e.getMessage(), e);
            throw new RuntimeException("Error al buscar puntos ECA", e);
        }
    }

    /**
     * Endpoint REST para obtener detalles completos de un punto ECA
     * Incluye información de materiales e inventario
     * Utilizado para mostrar modal con detalles al hacer click en tarjeta
     *
     * @param puntoEcaId ID del punto ECA
     * @return PuntoEcaDetalleDTO con materiales e inventario
     */
    @GetMapping("/api/puntos-eca/detalle/{puntoEcaId}")
    @ResponseBody
    public PuntoEcaDetalleDTO obtenerDetallesPuntoEca(@PathVariable String puntoEcaId) {
        try {
            logger.info("📊 Obteniendo detalles completos del punto ECA: {}", puntoEcaId);

            PuntoEcaDetalleDTO detalle = puntoEcaService.obtenerDetallesPuntoEca(java.util.UUID.fromString(puntoEcaId));

            if (detalle != null) {
                logger.info("✅ Detalles obtenidos: {} con {} materiales", detalle.getNombrePunto(), detalle.getMateriales().size());
                return detalle;
            } else {
                logger.warn("⚠️ Punto ECA no encontrado: {}", puntoEcaId);
                throw new RuntimeException("Punto ECA no encontrado");
            }

        } catch (IllegalArgumentException e) {
            logger.error("❌ ID inválido: {}", puntoEcaId);
            throw new RuntimeException("ID de punto ECA inválido", e);
        } catch (Exception e) {
            logger.error("❌ Error al obtener detalles: {}", e.getMessage(), e);
            throw new RuntimeException("Error al obtener detalles del punto ECA", e);
        }
    }
}

