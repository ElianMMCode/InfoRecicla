package org.sena.inforecicla.controller;

import jakarta.validation.Valid;
import lombok.AllArgsConstructor;
import org.sena.inforecicla.dto.puntoEca.gestor.GestorUpdateDTO;
import org.sena.inforecicla.dto.puntoEca.gestor.PuntoEcaUpdateDTO;
import org.sena.inforecicla.dto.puntoEca.inventario.InventarioRequestDTO;
import org.sena.inforecicla.dto.puntoEca.inventario.InventarioUpdateDTO;
import org.sena.inforecicla.dto.puntoEca.materiales.MaterialInvResponseDTO;
import org.sena.inforecicla.dto.usuario.UsuarioGestorResponseDTO;
import org.sena.inforecicla.exception.InventarioFoundExistException;
import org.sena.inforecicla.exception.InventarioNotFoundException;
import org.sena.inforecicla.exception.MaterialNotFoundException;
import org.sena.inforecicla.exception.PuntoEcaNotFoundException;
import org.sena.inforecicla.model.Localidad;
import org.sena.inforecicla.model.enums.Alerta;
import org.sena.inforecicla.model.enums.TipoDocumento;
import org.sena.inforecicla.model.enums.UnidadMedida;
import org.sena.inforecicla.service.*;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;
import org.springframework.http.HttpStatus;
import org.springframework.http.ResponseEntity;
import org.springframework.stereotype.Controller;
import org.springframework.ui.Model;
import org.springframework.web.bind.annotation.*;

import java.util.*;

@Controller
@AllArgsConstructor
@RequestMapping("/punto-eca")
public class PuntoEcaController {

    private static final Logger logger = LoggerFactory.getLogger(PuntoEcaController.class);

    // ✅ Services (no repositories)
    private final GestorEcaService gestorEcaService;
    private final InventarioService inventarioService;
    private final LocalidadService localidadService;
    private final CentroAcopioService centroAcopioService;
    private final PuntoEcaService puntoEcaService;
    private final CompraInventarioService compraInventarioService;
    private final VentaInventarioService ventaInventarioService;

    // Vista principal con usuarioId
    @GetMapping("/{nombrePunto}/{gestorId}")
    public String puntoEca(@PathVariable String nombrePunto, @PathVariable UUID gestorId, Model model) {
        UsuarioGestorResponseDTO usuario = gestorEcaService.buscarGestorPuntoEca(gestorId);
        model.addAttribute("usuario", usuario);
        // Usar el path variable nombrePunto para evitar warning por parámetro no usado
        model.addAttribute("gestor", nombrePunto);
        model.addAttribute("seccion", "resumen");
        model.addAttribute("inventarios", inventarioService.mostrarInventarioPuntoEca(usuario.puntoEcaId()));
        return "views/PuntoECA/puntoECA-layout";
    }

    // Navegación por path con filtros: /punto-eca/{nombrePunto}/{gestorId}/{seccion}?texto=...&alerta=...
    @GetMapping("/{nombrePunto}/{gestorId}/{seccion}")
    public String puntoEca(
            @PathVariable String nombrePunto,
            @PathVariable UUID gestorId,
            @PathVariable String seccion,
            @RequestParam(required = false) String texto,
            @RequestParam(required = false) String categoria,
            @RequestParam(required = false) String tipo,
            @RequestParam(required = false) Alerta alerta,
            @RequestParam(required = false) String unidad,
            @RequestParam(required = false) String ocupacion,
            @RequestParam(defaultValue = "0") int page,
            @RequestParam(defaultValue = "10") int size,
            Model model
    ) {
        // Normalizar sección a minúsculas
        seccion = seccion != null ? seccion.toLowerCase() : "resumen";
        Objects.requireNonNull(nombrePunto);

        // Obtener usuario
        UsuarioGestorResponseDTO usuario = gestorEcaService.buscarGestorPuntoEca(gestorId);

        // Agregar datos básicos siempre
        model.addAttribute("usuario", usuario);
        model.addAttribute("gestor", nombrePunto);
        model.addAttribute("seccion", seccion);

        // Cargar datos condicionales según la sección
        switch (seccion) {
            case "materiales" ->
                    cargarSeccionMateriales(usuario, texto, categoria, tipo, alerta, unidad, ocupacion, model);
            case "perfil" -> cargarSeccionPerfil(usuario, model);
            case "movimientos" -> cargarSeccionMovimientos(usuario, model, page, size);
            case "historial" -> cargarSeccionHistorial(usuario, model);
            case "centros" -> cargarSeccionCentros(usuario, model);
            case "configuracion" -> cargarSeccionConfiguracion(usuario, model);
            default -> cargarSeccionResumen(usuario, model);
        }

        return "views/PuntoECA/puntoECA-layout";
    }

    /**
     * Carga datos para la sección de Resumen
     */
    private void cargarSeccionResumen(UsuarioGestorResponseDTO usuario, Model model) {
        model.addAttribute("inventarios", inventarioService.mostrarInventarioPuntoEca(usuario.puntoEcaId()));
    }

    /**
     * Carga datos para la sección de Materiales con filtros
     */
    private void cargarSeccionMateriales(UsuarioGestorResponseDTO usuario, String texto, String categoria,
                                         String tipo, Alerta alerta, String unidad, String ocupacion, Model model) {
        List<?> inventarioFiltrado = Collections.emptyList();
        String mensajeAlerta = null;

        boolean hayFiltros = (texto != null && !texto.trim().isEmpty()) ||
                (categoria != null && !categoria.trim().isEmpty()) ||
                (tipo != null && !tipo.trim().isEmpty()) ||
                (alerta != null) ||
                (unidad != null && !unidad.trim().isEmpty()) ||
                (ocupacion != null && !ocupacion.trim().isEmpty());

        try {
            if (hayFiltros) {
                List<?> resultado = inventarioService.filtraInventario(usuario.puntoEcaId(), texto, categoria, tipo, alerta, unidad, ocupacion);
                if (resultado == null || resultado.isEmpty()) {
                    mensajeAlerta = "No se encontraron coincidencias con los filtros aplicados.";
                } else {
                    inventarioFiltrado = resultado;
                }
            } else {
                inventarioFiltrado = inventarioService.mostrarInventarioPuntoEca(usuario.puntoEcaId());
            }
        } catch (Exception e) {
            mensajeAlerta = "No se encontraron coincidencias con los filtros aplicados.";
        }

        model.addAttribute("inventario", inventarioFiltrado);
        model.addAttribute("unidadesMedida", construirUnidadesMedida());
        model.addAttribute("alerta", construirAlertas());

        if (mensajeAlerta != null) {
            model.addAttribute("mensajeAlerta", mensajeAlerta);
        }
        model.addAttribute("categoriaMateriales", inventarioService.listarCategoriasMateriales());
        model.addAttribute("tiposMateriales", inventarioService.listarTiposMateriales());
    }

    /**
     * Carga datos para la sección de Perfil
     */
    private void cargarSeccionPerfil(UsuarioGestorResponseDTO usuario, Model model) {
        // Aseguramos que la vista de perfil tenga lo mínimo necesario: el usuario y catálogos si se requieren
        model.addAttribute("usuario", usuario);
        model.addAttribute("localidades", construirLocalidades());
        model.addAttribute("tiposDocumentos", TipoDocumento.values());
        // Añade otros atributos necesarios para perfil cuando estén disponibles
    }

    /**
     * Carga datos para la sección de Movimientos
     */
    private void cargarSeccionMovimientos(UsuarioGestorResponseDTO usuario, Model model, int page, int size) {
        try {
            logger.debug("Cargando datos para sección de movimientos del usuario: {}", usuario.usuarioId());

            UUID puntoEcaId = usuario.puntoEcaId();

            Map<String, ?> atributos = Map.of(
                "categoriaMateriales", inventarioService.listarCategoriasMateriales(),
                "tiposMateriales", inventarioService.listarTiposMateriales(),
                "centrosAcopio", centroAcopioService.obtenerPorPuntoECA(puntoEcaId),
                "usuario", usuario,
                "puntoEca", puntoEcaService.buscarPuntoEca(puntoEcaId)
                    .orElseThrow(() -> new PuntoEcaNotFoundException("Punto ECA no encontrado: " + puntoEcaId)),
                "entradasIniciales", compraInventarioService.comprasDelPunto(puntoEcaId, page, size),
                "salidasIniciales", ventaInventarioService.ventasDelPunto(puntoEcaId, page, size),
                "unidadesMedida", construirUnidadesMedida(),
                "alerta", construirAlertas()
            );

            model.addAllAttributes(atributos);
            logger.debug("Datos de movimientos cargados exitosamente");

        } catch (PuntoEcaNotFoundException e) {
            logger.error("Error: Punto ECA no encontrado: {}", e.getMessage());
            model.addAttribute("error", "Error: " + e.getMessage());
        } catch (Exception e) {
            logger.error("Error al cargar sección de movimientos: {}", e.getMessage(), e);
            model.addAttribute("error", "Error al cargar los datos de movimientos");
        }
    }

    /**
     * Carga datos para la sección de Historial
     */
    private void cargarSeccionHistorial(UsuarioGestorResponseDTO usuario, Model model) {
        Objects.requireNonNull(usuario);
        Objects.requireNonNull(model);
        // TODO: Implementar cuando haya servicio de auditoría/historial
    }

    /**
     * Carga datos para la sección de Centros de Acopio
     */
    private void cargarSeccionCentros(UsuarioGestorResponseDTO usuario, Model model) {
        Objects.requireNonNull(usuario);
        Objects.requireNonNull(model);
        // TODO: Implementar cuando haya servicio de centros
    }

    /**
     * Construye lista de unidades de medida
     */
    private List<Map<String, String>> construirUnidadesMedida() {
        return Arrays.stream(UnidadMedida.values())
                .map(unidadMd -> {
                    Map<String, String> map = new HashMap<>();
                    map.put("clave", unidadMd.name());
                    map.put("nombre", unidadMd.getNombre());
                    return map;
                })
                .toList();
    }

    private List<Localidad> construirLocalidades() {
        return localidadService.listadoLocalidades();
    }

    /**
     * Construye lista de alertas
     */
    private List<Map<String, String>> construirAlertas() {
        return Arrays.stream(Alerta.values())
                .map(alertaTp -> {
                    Map<String, String> map = new HashMap<>();
                    map.put("clave", alertaTp.name());
                    map.put("tipo", alertaTp.getTipo());
                    return map;
                })
                .toList();
    }

    /**
     * Carga datos para la sección de Configuración
     */
    private void cargarSeccionConfiguracion(UsuarioGestorResponseDTO usuario, Model model) {
        Objects.requireNonNull(usuario);
        Objects.requireNonNull(model);
        // Datos necesarios para configuración
        // TODO: Implementar cuando haya servicio de configuración
    }

    @PostMapping("/{usuarioId}/perfil/encargado")
    @ResponseBody
    public ResponseEntity<?> actualizarGestor(
            @PathVariable UUID usuarioId,
            @RequestParam String nombres,
            @RequestParam String apellidos,
            @RequestParam String email,
            @RequestParam String celular,
            @RequestParam(defaultValue = "CC") String tipoDocumento,
            @RequestParam(defaultValue = "") String numeroDocumento,
            @RequestParam(defaultValue = "") String fechaNacimiento,
            @RequestParam(defaultValue = "") String biografia
    ) {
        try {
            // Validaciones
            if (nombres == null || nombres.trim().isEmpty() || nombres.length() < 3) {
                return ResponseEntity.badRequest().body(Map.of("error", "Nombres: mínimo 3 caracteres"));
            }
            if (apellidos == null || apellidos.trim().isEmpty() || apellidos.length() < 2) {
                return ResponseEntity.badRequest().body(Map.of("error", "Apellidos: mínimo 2 caracteres"));
            }
            if (email == null || email.trim().isEmpty() || !email.contains("@")) {
                return ResponseEntity.badRequest().body(Map.of("error", "Email inválido"));
            }
            if (celular == null || celular.trim().isEmpty() || celular.length() != 10) {
                return ResponseEntity.badRequest().body(Map.of("error", "Celular: 10 dígitos"));
            }

            var dto = new GestorUpdateDTO(
                    nombres.trim(),
                    apellidos.trim(),
                    TipoDocumento.valueOf(tipoDocumento),
                    numeroDocumento.trim(),
                    fechaNacimiento.trim(),
                    celular.trim(),
                    email.trim(),
                    biografia.trim()
            );
            var resultado = gestorEcaService.actualizarGestor(usuarioId, dto);
            return ResponseEntity.ok(resultado);
        } catch (IllegalArgumentException e) {
            return ResponseEntity.badRequest().body(Map.of("error", "Tipo de documento inválido"));
        } catch (Exception e) {
            logger.error("Error al actualizar gestor: {}", e.getMessage(), e);
            return ResponseEntity.status(500).body(Map.of("error", "Error interno: " + e.getMessage()));
        }
    }

    @PostMapping("/{usuarioId}/perfil/punto/{puntoEcaId}")
    @ResponseBody
    public ResponseEntity<?> actualizarPunto(
            @PathVariable UUID usuarioId,
            @PathVariable UUID puntoEcaId,
            @RequestParam String nombrePunto,
            @RequestParam String direccionPunto,
            @RequestParam UUID localidadPuntoId,
            @RequestParam(defaultValue = "0") Double latitud,
            @RequestParam(defaultValue = "0") Double longitud,
            @RequestParam(defaultValue = "") String telefonoPunto,
            @RequestParam(defaultValue = "") String celularPunto,
            @RequestParam(defaultValue = "") String emailPunto,
            @RequestParam(defaultValue = "") String descripcionPunto,
            @RequestParam(defaultValue = "") String sitioWebPunto,
            @RequestParam(defaultValue = "") String horarioAtencionPunto
    ) {
        try {
            logger.info("========== GUARDAR PUNTO ECA ==========");
            logger.info("usuarioId: {}", usuarioId);
            logger.info("puntoEcaId: {}", puntoEcaId);
            logger.info("nombrePunto (RAW): '{}' | trim(): '{}'", nombrePunto, nombrePunto != null ? nombrePunto.trim() : "null");
            logger.info("direccionPunto (RAW): '{}' | trim(): '{}'", direccionPunto, direccionPunto != null ? direccionPunto.trim() : "null");
            logger.info("localidadPuntoId: {}", localidadPuntoId);
            logger.info("celularPunto (RAW): '{}' | trim(): '{}' | length: {}", celularPunto, celularPunto != null ? celularPunto.trim() : "null", celularPunto != null ? celularPunto.length() : 0);
            logger.info("emailPunto (RAW): '{}' | trim(): '{}'", emailPunto, emailPunto != null ? emailPunto.trim() : "null");
            logger.info("telefonoPunto (RAW): '{}'", telefonoPunto);
            logger.info("latitud: {} | longitud: {}", latitud, longitud);
            logger.info("descripcionPunto: {}", descripcionPunto);
            logger.info("sitioWebPunto: {}", sitioWebPunto);
            logger.info("horarioAtencionPunto: {}", horarioAtencionPunto);

            // Validaciones básicas
            if (nombrePunto == null || nombrePunto.trim().isEmpty() || nombrePunto.length() < 3) {
                logger.warn("Validación fallida: nombrePunto");
                return ResponseEntity.badRequest().body(Map.of("error", "Nombre: mínimo 3 caracteres"));
            }
            if (direccionPunto == null || direccionPunto.trim().isEmpty()) {
                logger.warn("Validación fallida: direccionPunto");
                return ResponseEntity.badRequest().body(Map.of("error", "Dirección: obligatoria"));
            }
            if (celularPunto == null || celularPunto.trim().isEmpty() || celularPunto.length() != 10) {
                logger.warn("Validación fallida: celularPunto - length: {}", celularPunto != null ? celularPunto.length() : 0);
                return ResponseEntity.badRequest().body(Map.of("error", "Celular: 10 dígitos"));
            }
            if (emailPunto == null || emailPunto.trim().isEmpty() || !emailPunto.contains("@")) {
                logger.warn("Validación fallida: emailPunto");
                return ResponseEntity.badRequest().body(Map.of("error", "Email inválido"));
            }
            if (localidadPuntoId == null) {
                return ResponseEntity.badRequest().body(Map.of("error", "Localidad es obligatoria"));
            }

            // Crear DTO con datos validados
            var dto = new PuntoEcaUpdateDTO(
                    nombrePunto.trim(),
                    descripcionPunto.trim(),
                    telefonoPunto.trim(),
                    celularPunto.trim(),
                    emailPunto.trim(),
                    direccionPunto.trim(),
                    localidadPuntoId,
                    latitud,
                    longitud,
                    sitioWebPunto.trim(),
                    horarioAtencionPunto.trim()
            );

            var resultado = gestorEcaService.actualizarPunto(usuarioId, puntoEcaId, dto);
            return ResponseEntity.ok(resultado);
        } catch (IllegalArgumentException e) {
            logger.error("❌ Datos inválidos: {}", e.getMessage(), e);
            return ResponseEntity.badRequest().body(Map.of("error", "Datos inválidos: " + e.getMessage()));
        } catch (Exception e) {
            logger.error("❌ Error al guardar punto: {}", e.getMessage(), e);
            return ResponseEntity.status(500).body(Map.of("error", "Error interno: " + e.getMessage()));
        }
    }

    // Endpoint REST: búsqueda de materiales - Devuelve JSON
    @GetMapping("/catalogo/materiales/buscar")
    @ResponseBody
    public ResponseEntity<?> buscarMateriales(
            @RequestParam UUID puntoId,
            @RequestParam(required = false, defaultValue = "") String texto,
            @RequestParam(required = false, defaultValue = "") String categoria,
            @RequestParam(required = false, defaultValue = "") String tipo
    ) {
        try {
            List<MaterialInvResponseDTO> resultados = inventarioService.buscarMaterialFiltrandoInventario(puntoId, texto, categoria, tipo);
            return ResponseEntity.ok(resultados);
        } catch (InventarioFoundExistException e) {
            // Devolver el mensaje de error de forma legible al frontend
            return ResponseEntity.badRequest().body(Map.of(
                    "error", true,
                    "mensaje", e.getMessage()
            ));
        } catch (Exception e) {
            return ResponseEntity.internalServerError().body(Map.of(
                    "error", true,
                    "mensaje", "Error al buscar materiales: " + e.getMessage()
            ));
        }
    }


    // Ruta: /punto-eca/{gestor}/{usuarioId}/actualizar-inventario/{inventarioId}
    @PutMapping("/{nombrePunto}/{usuarioId}/actualizar-inventario/{inventarioId}")
    public ResponseEntity<?> actualizarInventario(
            @PathVariable String nombrePunto,
            @PathVariable UUID usuarioId,
            @PathVariable UUID inventarioId,
            @RequestBody InventarioUpdateDTO dto
    ) throws InventarioNotFoundException {
        // Referenciar los path variables para evitar warnings de parámetros no usados
        Objects.requireNonNull(nombrePunto);
        Objects.requireNonNull(usuarioId);

        var resultado = inventarioService.actualizarInventario(inventarioId, dto);
        return ResponseEntity.ok(resultado);
    }

    @PostMapping("/inventario/agregar")
    @ResponseBody
    public ResponseEntity<?> agregarInventario(
            @Valid @RequestBody InventarioRequestDTO dto
    ) {
        try {
            inventarioService.guardarInventario(dto);
            return ResponseEntity.status(HttpStatus.CREATED).body(Map.of(
                    "error", false,
                    "mensaje", "Inventario guardado exitosamente"
            ));
        } catch (MaterialNotFoundException | PuntoEcaNotFoundException e) {
            logger.error("Entidad no encontrada: {}", e.getMessage(), e);
            return ResponseEntity.status(HttpStatus.NOT_FOUND).body(Map.of(
                    "error", true,
                    "mensaje", e.getMessage()
            ));
        } catch (Exception e) {
            logger.error("Error al guardar inventario: {}", e.getMessage(), e);
            return ResponseEntity.internalServerError().body(Map.of(
                    "error", true,
                    "mensaje", "Error al guardar inventario: " + e.getMessage(),
                    "detalles", e.getClass().getSimpleName()
            ));
        }
    }

    // Endpoint: Eliminar material del inventario
    @DeleteMapping("/{nombrePunto}/{gestorId}/eliminar-inventario/{inventarioId}")
    @ResponseBody
    public ResponseEntity<?> eliminarInventario(
            @PathVariable String nombrePunto,
            @PathVariable UUID gestorId,
            @PathVariable UUID inventarioId
    ) {
        // Evitar warnings por parámetros no usados
        Objects.requireNonNull(nombrePunto);
        Objects.requireNonNull(gestorId);
        try {
            inventarioService.eliminarInventario(inventarioId);
            return ResponseEntity.ok(Map.of(
                    "success", true,
                    "mensaje", "Material eliminado del inventario correctamente"
            ));
        } catch (InventarioNotFoundException e) {
            logger.warn("Inventario no encontrado al eliminar: {}", e.getMessage());
            return ResponseEntity.status(HttpStatus.NOT_FOUND).body(Map.of(
                    "error", true,
                    "mensaje", "Inventario no encontrado: " + e.getMessage()
            ));
        } catch (Exception e) {
            logger.error("Error al eliminar inventario: {}", e.getMessage(), e);
            return ResponseEntity.status(HttpStatus.INTERNAL_SERVER_ERROR).body(Map.of(
                    "error", true,
                    "mensaje", "Error al eliminar inventario: " + e.getMessage()
            ));
        }
    }

}
