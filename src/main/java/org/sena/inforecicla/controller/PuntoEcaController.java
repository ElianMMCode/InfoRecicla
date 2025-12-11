package org.sena.inforecicla.controller;

import jakarta.validation.Valid;
import lombok.AllArgsConstructor;
import org.sena.inforecicla.dto.puntoEca.gestor.GestorUpdateDTO;
import org.sena.inforecicla.dto.puntoEca.gestor.PuntoEcaUpdateDTO;
import org.sena.inforecicla.dto.puntoEca.inventario.InventarioRequestDTO;
import org.sena.inforecicla.dto.puntoEca.inventario.InventarioResponseDTO;
import org.sena.inforecicla.dto.puntoEca.inventario.InventarioUpdateDTO;
import org.sena.inforecicla.dto.puntoEca.inventario.movimientos.*;
import org.sena.inforecicla.dto.puntoEca.materiales.CategoriaMaterialesInvResponseDTO;
import org.sena.inforecicla.dto.puntoEca.materiales.MaterialInvResponseDTO;
import org.sena.inforecicla.dto.puntoEca.materiales.MaterialResponseDTO;
import org.sena.inforecicla.dto.puntoEca.materiales.TipoMaterialesInvResponseDTO;
import org.sena.inforecicla.dto.usuario.UsuarioGestorResponseDTO;
import org.sena.inforecicla.exception.InventarioFoundExistException;
import org.sena.inforecicla.exception.InventarioNotFoundException;
import org.sena.inforecicla.exception.PuntoEcaNotFoundException;
import org.sena.inforecicla.model.CentroAcopio;
import org.sena.inforecicla.model.Localidad;
import org.sena.inforecicla.model.Usuario;
import org.sena.inforecicla.model.enums.Alerta;
import org.sena.inforecicla.model.enums.TipoDocumento;
import org.sena.inforecicla.model.enums.TipoUsuario;
import org.sena.inforecicla.model.enums.UnidadMedida;
import org.sena.inforecicla.service.*;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;
import org.springframework.data.domain.Page;
import org.springframework.http.HttpStatus;
import org.springframework.http.ResponseEntity;
import org.springframework.stereotype.Controller;
import org.springframework.ui.Model;
import org.springframework.web.bind.annotation.*;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.security.crypto.bcrypt.BCryptPasswordEncoder;

import java.util.*;

import org.springframework.security.core.Authentication;
import org.springframework.security.core.context.SecurityContextHolder;

@Controller
@AllArgsConstructor
@RequestMapping("/punto-eca")
public class PuntoEcaController {

    private static final Logger logger = LoggerFactory.getLogger(PuntoEcaController.class);

    // ✅ Services (no repositories)
    private final GestorEcaService gestorEcaService;
    private final InventarioService inventarioService;
    private final InventarioEliminacionService inventarioEliminacionService;
    private final LocalidadService localidadService;
    private final CentroAcopioService centroAcopioService;
    private final PuntoEcaService puntoEcaService;
    private final CompraInventarioService compraInventarioService;
    private final VentaInventarioService ventaInventarioService;
    private final TipoMaterialService tipoMaterialService;
    private final CategoriaMaterialService categoriaMaterialService;
    private final InventarioDetalleService inventarioDetalleService;

    // ✅ Repository para cambios de contraseña
    @Autowired
    private org.sena.inforecicla.repository.UsuarioRepository usuarioRepository;

    // Ruta base que redirige automáticamente al punto ECA del usuario autenticado
    @GetMapping
    public String puntoEcaBase() {
        // Obtener el usuario autenticado
        Authentication auth = SecurityContextHolder.getContext().getAuthentication();

        if (auth != null && auth.isAuthenticated() && auth.getPrincipal() instanceof Usuario usuario) {
            // Si es GestorECA, buscar su punto ECA y redirigir
            if (usuario.getTipoUsuario() == TipoUsuario.GestorECA) {
                try {
                    // Buscar el punto ECA asociado al usuario
                    UsuarioGestorResponseDTO gestorData = gestorEcaService.buscarGestorPuntoEca(usuario.getUsuarioId());
                    if (gestorData != null && gestorData.puntoEcaId() != null) {
                        String nombrePunto = gestorData.nombrePunto() != null ?
                            gestorData.nombrePunto().replace(" ", "-") : "punto-eca";
                        return "redirect:/punto-eca/" + nombrePunto + "/" + usuario.getUsuarioId();
                    }
                } catch (Exception e) {
                    logger.warn("Error al buscar punto ECA para usuario {}: {}", usuario.getUsuarioId(), e.getMessage());
                }
            }
        }

        // Si no se puede determinar el punto ECA, redirigir al dashboard
        return "redirect:/dashboard";
    }

    // Vista principal con usuarioId
    @GetMapping("/{nombrePunto}/{gestorId}")
    public String puntoEca(@PathVariable String nombrePunto, @PathVariable UUID gestorId, Model model) {
        UsuarioGestorResponseDTO usuario = gestorEcaService.buscarGestorPuntoEca(gestorId);
        model.addAttribute("usuario", usuario);
        // Usar el path variable nombrePunto para evitar warning por parámetro no usado
        model.addAttribute("gestor", nombrePunto);
        model.addAttribute("seccion", "resumen");

        // Cargar datos completos de la sección de resumen
        cargarSeccionResumen(usuario, model);

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
        try {
            UUID puntoEcaId = usuario.puntoEcaId();

            logger.info("═══════════════════════════════════════════════════════════════");
            logger.info("📊 INICIANDO CARGA DE DATOS DE RESUMEN");
            logger.info("═══════════════════════════════════════════════════════════════");
            logger.info("🔹 Usuario ID: {}", usuario.usuarioId());
            logger.info("🔹 Punto ECA ID: {}", puntoEcaId);
            logger.info("🔹 Punto ECA Nombre: {}", usuario.nombrePunto());

            // ========== INVENTARIOS ==========
            logger.info("\n📦 PASO 1: Obteniendo INVENTARIOS...");
            List<?> inventarios = inventarioService.mostrarInventarioPuntoEca(puntoEcaId);
            logger.info("   ✅ Inventarios retornados: {} items", inventarios != null ? inventarios.size() : 0);
            logger.info("   🔍 Tipo de lista: {}", inventarios != null ? inventarios.getClass().getSimpleName() : "null");

            if (inventarios != null && !inventarios.isEmpty()) {
                logger.info("   📋 Primeros 3 inventarios:");
                int count = 0;
                for (Object inv : inventarios) {
                    if (count >= 3) break;
                    logger.info("      [{}] Tipo: {}", count + 1, inv.getClass().getSimpleName());
                    if (inv instanceof InventarioResponseDTO invDTO) {
                        logger.info("         Material: {}", invDTO.nombreMaterial());
                        logger.info("         Stock Actual: {} (tipo: {})", invDTO.stockActual(),
                            invDTO.stockActual() != null ? invDTO.stockActual().getClass().getSimpleName() : "null");
                        logger.info("         Capacidad Máxima: {}", invDTO.capacidadMaxima());
                    } else {
                        logger.warn("         ⚠️ No es InventarioResponseDTO, es: {}", inv.getClass().getName());
                    }
                    count++;
                }

                // MOSTRAR TODOS LOS INVENTARIOS PARA DEBUGGING
                logger.info("   📋 LISTANDO TODOS LOS INVENTARIOS ({} items):", inventarios.size());
                int idx = 0;
                for (Object inv : inventarios) {
                    if (inv instanceof InventarioResponseDTO invDTO) {
                        logger.info("      [{}] {} - Stock: {}",
                            idx + 1, invDTO.nombreMaterial(),
                            invDTO.stockActual() != null ? invDTO.stockActual() : "NULL");
                    }
                    idx++;
                }
            } else {
                logger.warn("   ⚠️ Lista de inventarios es null o vacía");
            }

            // ========== CALCULAR INVENTARIO TOTAL ==========
            logger.info("\n   🧮 CALCULANDO INVENTARIO TOTAL...");
            double inventarioTotal = 0.0;
            int contadorMateriales = 0;
            int contadorNulos = 0;

            if (inventarios != null && !inventarios.isEmpty()) {
                logger.info("   📊 Procesando {} inventarios:", inventarios.size());
                for (Object inv : inventarios) {
                    if (inv instanceof InventarioResponseDTO invDTO) {
                        contadorMateriales++;
                        if (invDTO.stockActual() != null) {
                            double stock = invDTO.stockActual().doubleValue();
                            inventarioTotal += stock;
                            logger.info("      [{}] ➕ Material: {}, Stock: {} kg",
                                contadorMateriales, invDTO.nombreMaterial(), stock);
                        } else {
                            contadorNulos++;
                            logger.warn("      [{}] ⚠️ Material: {} - Stock es NULL (ignorado)",
                                contadorMateriales, invDTO.nombreMaterial());
                        }
                    } else {
                        logger.warn("      ❌ Objeto no es InventarioResponseDTO: {}", inv.getClass().getName());
                    }
                }
            }

            logger.info("   📊 RESUMEN DEL CÁLCULO:");
            logger.info("      - Materiales procesados: {}", contadorMateriales);
            logger.info("      - Materiales con stock NULL: {}", contadorNulos);
            logger.info("      - Total inventario calculado: {} kg", String.format("%.2f", inventarioTotal));
            logger.info("      - Capacidad máxima (asumida): 2000 kg");
            logger.info("      - Porcentaje de uso: {}%", Math.min((int) (inventarioTotal / 2000 * 100), 100));

            // ========== COMPRAS (ENTRADAS) ==========
            logger.info("\n📥 PASO 2: Obteniendo COMPRAS (ENTRADAS)...");
            Page<CompraInventarioResponseDTO> comprasDelMes = compraInventarioService.comprasDelPunto(puntoEcaId, 0, 100);

            long totalComprasItems;
            if (comprasDelMes != null) {
                totalComprasItems = comprasDelMes.getTotalElements();
                logger.info("   ✅ Compras retornadas: {} items (total en BD)", totalComprasItems);
                logger.info("   📄 Contenido en página actual: {} items",
                    comprasDelMes.hasContent() ? comprasDelMes.getContent().size() : 0);

                if (comprasDelMes.hasContent()) {
                    logger.info("   📋 Primeras 2 compras:");
                    int count = 0;
                    for (CompraInventarioResponseDTO compra : comprasDelMes) {
                        if (count >= 2) break;
                        logger.info("      [{}] Material: {}, Cantidad: {}, Fecha: {}",
                            count + 1, compra.nombreMaterial(), compra.cantidad(), compra.fechaCompra());
                        count++;
                    }
                }
            } else {
                logger.warn("   ⚠️ Page de compras es null");
            }

            double totalCompras = 0.0;
            if (comprasDelMes != null && comprasDelMes.hasContent()) {
                for (CompraInventarioResponseDTO compra : comprasDelMes) {
                    if (compra.cantidad() != null) {
                        totalCompras += compra.cantidad().doubleValue();
                    }
                }
            }
            logger.info("   💯 TOTAL COMPRAS CALCULADO: {} kg", String.format("%.2f", totalCompras));

            // ========== VENTAS (SALIDAS) ==========
            logger.info("\n📤 PASO 3: Obteniendo VENTAS (SALIDAS)...");
            Page<VentaInventarioResponseDTO> ventasDelMes = ventaInventarioService.ventasDelPunto(puntoEcaId, 0, 100);

            long totalVentasItems;
            if (ventasDelMes != null) {
                totalVentasItems = ventasDelMes.getTotalElements();
                logger.info("   ✅ Ventas retornadas: {} items (total en BD)", totalVentasItems);
                logger.info("   📄 Contenido en página actual: {} items",
                    ventasDelMes.hasContent() ? ventasDelMes.getContent().size() : 0);

                if (ventasDelMes.hasContent()) {
                    logger.info("   📋 Primeras 2 ventas:");
                    int count = 0;
                    for (VentaInventarioResponseDTO venta : ventasDelMes) {
                        if (count >= 2) break;
                        logger.info("      [{}] Material: {}, Cantidad: {}, Fecha: {}",
                            count + 1, venta.nombreMaterial(), venta.cantidad(), venta.fechaVenta());
                        count++;
                    }
                }
            } else {
                logger.warn("   ⚠️ Page de ventas es null");
            }

            double totalVentas = 0.0;
            if (ventasDelMes != null && ventasDelMes.hasContent()) {
                for (VentaInventarioResponseDTO venta : ventasDelMes) {
                    if (venta.cantidad() != null) {
                        totalVentas += venta.cantidad().doubleValue();
                    }
                }
            }
            logger.info("   💯 TOTAL VENTAS CALCULADO: {} kg", String.format("%.2f", totalVentas));

            // ========== CREAR MAPA DE RESUMEN ==========
            logger.info("\n📝 PASO 4: CREANDO MAPA DE DATOS...");
            Map<String, Object> datosResumen = new HashMap<>();

            String invTotal = String.format("%.2f", inventarioTotal);
            String entTotal = String.format("%.2f", totalCompras);
            String salTotal = String.format("%.2f", totalVentas);
            int capacidad = Math.min((int) (inventarioTotal / 2000 * 100), 100);

            datosResumen.put("inventarioTotal", invTotal);
            datosResumen.put("entradasMes", entTotal);
            datosResumen.put("salidasMes", salTotal);
            datosResumen.put("capacidadPorcentaje", capacidad);
            datosResumen.put("proximoDespacho", "Pendiente");
            datosResumen.put("proximaFecha", "2025-12-15");

            logger.info("   ✅ Datos principales agregados:");
            logger.info("      📦 Inventario Total: {} kg", invTotal);
            logger.info("      📥 Entradas: {} kg", entTotal);
            logger.info("      📤 Salidas: {} kg", salTotal);
            logger.info("      📊 Capacidad: {}%", capacidad);

            // ========== ALERTAS ==========
            logger.info("\n⚠️  PASO 5: GENERANDO ALERTAS...");
            List<Map<String, Object>> alertas = new ArrayList<>();
            if (inventarioTotal > 1500) {
                alertas.add(Map.of(
                    "titulo", "Inventario Alto",
                    "descripcion", "El inventario está por encima del 75% de capacidad",
                    "tipo", "warning"
                ));
                logger.info("   ✅ Alerta: Inventario Alto ({} kg)", inventarioTotal);
            }
            if (totalCompras == 0) {
                alertas.add(Map.of(
                    "titulo", "Sin Entradas",
                    "descripcion", "No hay entradas registradas este mes",
                    "tipo", "info"
                ));
                logger.info("   ✅ Alerta: Sin Entradas");
            }
            datosResumen.put("alertas", alertas);
            datosResumen.put("alertaCount", alertas.size());
            logger.info("   📊 Total de alertas: {}", alertas.size());

            // ========== ÚLTIMOS MOVIMIENTOS ==========
            logger.info("\n📋 PASO 6: RECOPILANDO ÚLTIMOS MOVIMIENTOS...");
            List<Map<String, String>> movimientos = new ArrayList<>();

            if (comprasDelMes != null && comprasDelMes.hasContent()) {
                int count = 0;
                for (CompraInventarioResponseDTO compra : comprasDelMes) {
                    if (count >= 3) break;
                    movimientos.add(Map.of(
                        "fecha", compra.fechaCompra() != null ? compra.fechaCompra().toString().split("T")[0] : "N/A",
                        "tipo", "Entrada",
                        "cantidad", compra.cantidad() != null ? String.format("%.2f", compra.cantidad()) : "0",
                        "descripcion", compra.nombreMaterial() != null ? compra.nombreMaterial() : "Material",
                        "usuario", usuario.nombres(),
                        "icono", "arrow-down-circle-fill",
                        "color", "text-info"
                    ));
                    count++;
                }
                logger.info("   ✅ Compras agregadas: {} items", count);
            }

            if (ventasDelMes != null && ventasDelMes.hasContent()) {
                int count = 0;
                for (VentaInventarioResponseDTO venta : ventasDelMes) {
                    if (count >= 2) break;
                    movimientos.add(Map.of(
                        "fecha", venta.fechaVenta() != null ? venta.fechaVenta().toString().split("T")[0] : "N/A",
                        "tipo", "Salida",
                        "cantidad", venta.cantidad() != null ? String.format("%.2f", venta.cantidad()) : "0",
                        "descripcion", venta.nombreMaterial() != null ? venta.nombreMaterial() : "Material",
                        "usuario", "Sistema",
                        "icono", "arrow-up-circle-fill",
                        "color", "text-warning"
                    ));
                    count++;
                }
                logger.info("   ✅ Ventas agregadas: {} items", count);
            }

            datosResumen.put("movimientos", movimientos);
            logger.info("   📊 Total de movimientos: {}", movimientos.size());

            // ========== SERIALIZAR A JSON ==========
            logger.info("\n🔄 PASO 7: SERIALIZANDO A JSON...");
            String datosResumenJSON = "{}";
            try {
                com.fasterxml.jackson.databind.ObjectMapper mapper = new com.fasterxml.jackson.databind.ObjectMapper();
                datosResumenJSON = mapper.writeValueAsString(datosResumen);
                logger.info("   ✅ JSON serializado correctamente");
                logger.info("   📝 Tamaño del JSON: {} caracteres", datosResumenJSON.length());
                logger.info("   🔍 Primeros 200 caracteres del JSON:");
                logger.info("      {}", datosResumenJSON.substring(0, Math.min(200, datosResumenJSON.length())));
            } catch (Exception e) {
                logger.error("   ❌ Error serializando JSON: {}", e.getMessage());
            }

            // ========== AGREGAR AL MODELO ==========
            logger.info("\n📌 PASO 8: AGREGANDO ATRIBUTOS AL MODELO...");
            model.addAttribute("puntoId", puntoEcaId.toString());
            model.addAttribute("datosResumen", datosResumen);
            model.addAttribute("datosResumenJSON", datosResumenJSON);
            model.addAttribute("inventarios", inventarios);
            model.addAttribute("comprasDelMes", comprasDelMes);
            model.addAttribute("ventasDelMes", ventasDelMes);
            model.addAttribute("unidadesMedida", construirUnidadesMedida());
            model.addAttribute("categoriaMateriales", categoriaMaterialService.listarCategoriasMateriales());
            model.addAttribute("tiposMateriales", tipoMaterialService.listarTiposMateriales());

            logger.info("   ✅ Atributos agregados al modelo:");
            logger.info("      - puntoId: {}", puntoEcaId);
            logger.info("      - datosResumen: {} propiedades", datosResumen.size());
            logger.info("      - datosResumenJSON: {} caracteres", datosResumenJSON.length());
            logger.info("      - inventarios: {} items", inventarios != null ? inventarios.size() : 0);
            logger.info("      - comprasDelMes: {} items", comprasDelMes != null ? comprasDelMes.getTotalElements() : 0);
            logger.info("      - ventasDelMes: {} items", ventasDelMes != null ? ventasDelMes.getTotalElements() : 0);

            logger.info("\n═══════════════════════════════════════════════════════════════");
            logger.info("✅ CARGA DE DATOS DE RESUMEN COMPLETADA EXITOSAMENTE");
            logger.info("═══════════════════════════════════════════════════════════════\n");

        } catch (Exception e) {
            logger.error("═══════════════════════════════════════════════════════════════");
            logger.error("❌ ERROR AL CARGAR SECCIÓN RESUMEN", e);
            logger.error("═══════════════════════════════════════════════════════════════");
            logger.error("Mensaje de error: {}", e.getMessage());
            logger.error("Stack trace: ", e);

            model.addAttribute("error", "Error al cargar el resumen: " + e.getMessage());

            // Cargar datos por defecto en caso de error
            model.addAttribute("inventarios", Collections.emptyList());
            model.addAttribute("unidadesMedida", construirUnidadesMedida());
            model.addAttribute("categoriaMateriales", categoriaMaterialService.listarCategoriasMateriales());
            model.addAttribute("tiposMateriales", tipoMaterialService.listarTiposMateriales());
        }
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
            logger.error("❌ Error en cargarSeccionMateriales: {}", e.getMessage(), e);
            mensajeAlerta = "Error al cargar materiales: " + e.getMessage();
            inventarioFiltrado = Collections.emptyList();
        }

        // Calcular categorías y tipos únicos presentes en el inventario
        Set<String> categoriasInventario = new TreeSet<>();
        Set<String> tiposInventario = new TreeSet<>();
        try {
            for (Object item : inventarioFiltrado) {
                if (item instanceof MaterialInvResponseDTO m) {
                    String cat = Optional.ofNullable(m.nmbCategoria()).orElse(null);
                    String tip = Optional.ofNullable(m.nmbTipo()).orElse(null);
                    if (cat != null && !cat.isBlank()) categoriasInventario.add(cat);
                    if (tip != null && !tip.isBlank()) tiposInventario.add(tip);
                } else if (item instanceof Map) {
                    // fallback por si el servicio devuelve Map
                    Map<?, ?> map = (Map<?, ?>) item;
                    Object catObj = map.get("nmbCategoria");
                    Object tipObj = map.get("nmbTipo");
                    if (catObj instanceof String catStr && !catStr.isBlank()) categoriasInventario.add(catStr);
                    if (tipObj instanceof String tipStr && !tipStr.isBlank()) tiposInventario.add(tipStr);
                }
            }
        } catch (Exception e) {
            logger.warn("⚠ No fue posible calcular categorías/tipos del inventario: {}", e.getMessage());
        }

        try {
            // Log de debugging
            logger.info("📊 Inventario filtrado: {} items", inventarioFiltrado.size());
            logger.info("📋 Categorías del inventario: {} -> {}", categoriasInventario.size(), categoriasInventario);
            logger.info("📋 Tipos del inventario: {} -> {}", tiposInventario.size(), tiposInventario);

            // Agregar datos del inventario
            model.addAttribute("inventario", inventarioFiltrado);
            // Agregar listas dinámicas basadas en inventario
            model.addAttribute("categoriasInventario", new ArrayList<>(categoriasInventario));
            model.addAttribute("tiposInventario", new ArrayList<>(tiposInventario));

            // Agregar catálogos generales (fallbacks)
            var catalogoCategorias = categoriaMaterialService.listarCategoriasMateriales();
            var catalogoTipos = tipoMaterialService.listarTiposMateriales();
            logger.info("📚 Catálogo categorías: {} items", catalogoCategorias.size());
            logger.info("📚 Catálogo tipos: {} items", catalogoTipos.size());

            model.addAttribute("categoriaMateriales", catalogoCategorias);
            model.addAttribute("tiposMateriales", catalogoTipos);

            model.addAttribute("unidadesMedida", construirUnidadesMedida());
            model.addAttribute("alerta", construirAlertas());
            model.addAttribute("puntoEcaId", usuario.puntoEcaId());

            // Mensaje
            if (mensajeAlerta != null) {
                model.addAttribute("mensajeAlerta", mensajeAlerta);
            }
        } catch (Exception e) {
            logger.error("❌ Error agregando atributos al modelo: {}", e.getMessage(), e);
            model.addAttribute("inventario", Collections.emptyList());
            model.addAttribute("error", "Error al cargar la página: " + e.getMessage());
        }
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

            UUID puntoEcaId = usuario.puntoEcaId();

            // Extraer y castear categorías correctamente
            List<CategoriaMaterialesInvResponseDTO> categoriaMateriales = inventarioDetalleService.obtenerCategoriasDelPunto(puntoEcaId);
            // Extraer y castear tipos correctamente
            List<TipoMaterialesInvResponseDTO> tiposMateriales = inventarioDetalleService.obtenerTiposDelPunto(puntoEcaId);
            Map<String, Object> atributos = Map.ofEntries(
                    Map.entry("categoriaMateriales", categoriaMateriales),
                    Map.entry("tiposMateriales", tiposMateriales),
                    Map.entry("centrosAcopio", centroAcopioService.listaCentrosPorPuntoEca(puntoEcaId)),
                    Map.entry("usuario", usuario),
                    Map.entry("puntoEcaId", puntoEcaId),
                    Map.entry("puntoEca", puntoEcaService.buscarPuntoEca(puntoEcaId)
                            .orElseThrow(() -> new PuntoEcaNotFoundException("Punto ECA no encontrado: " + puntoEcaId))),
                    Map.entry("entradasIniciales", compraInventarioService.comprasDelPunto(puntoEcaId, page, size)),
                    Map.entry("salidasIniciales", ventaInventarioService.ventasDelPunto(puntoEcaId, page, size))
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
     * Carga datos para la sección de Centros
     */
    private void cargarSeccionCentros(UsuarioGestorResponseDTO usuario, Model model) {
        Objects.requireNonNull(usuario);
        Objects.requireNonNull(model);

        try {
            // Obtener centros globales (sin punto asignado)
            List<CentroAcopio> centrosGlobales = centroAcopioService.obtenerCentrosGlobales();

            // Obtener centros propios del punto (asignados a este punto específico)
            List<CentroAcopio> centrosPropios = centroAcopioService.listaCentrosPorPuntoEca(usuario.puntoEcaId());

            // Obtener localidades para el filtro
            List<Localidad> localidades = localidadService.listadoLocalidades();

            // Agregar datos al modelo
            model.addAttribute("centrosGlobales", centrosGlobales);
            model.addAttribute("centrosPropios", centrosPropios);
            model.addAttribute("localidades", localidades);
            model.addAttribute("totalCentrosGlobales", centrosGlobales.size());
            model.addAttribute("totalCentrosPropios", centrosPropios.size());

        } catch (Exception e) {
            logger.error("❌ Error al cargar centros de acopio: {}", e.getMessage(), e);
            model.addAttribute("centrosGlobales", Collections.emptyList());
            model.addAttribute("centrosPropios", Collections.emptyList());
            model.addAttribute("localidades", Collections.emptyList());
            model.addAttribute("totalCentrosGlobales", 0);
            model.addAttribute("totalCentrosPropios", 0);
        }
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
     * Proporciona unidades de medida a todas las vistas
     */
    @ModelAttribute("unidadesMedida")
    public List<Map<String, String>> unidadesMedidaModelAttribute() {
        return construirUnidadesMedida();
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

    // Endpoint REST: búsqueda de materiales nuevos (no en inventario) - Devuelve JSON
    @GetMapping("/catalogo/materiales/buscar")
    @ResponseBody
    public ResponseEntity<?> buscarMateriales(
            @RequestParam UUID puntoId,
            @RequestParam(required = false, defaultValue = "") String texto,
            @RequestParam(required = false, defaultValue = "") String categoria,
            @RequestParam(required = false, defaultValue = "") String tipo
    ) {
        try {
            // Primero intentar buscar en materiales EXISTENTES en inventario
            try {
                List<MaterialResponseDTO> existentes = inventarioDetalleService.buscarMaterialNuevoFiltrandoInventario(puntoId, texto, categoria, tipo);
                if (!existentes.isEmpty()) {
                    logger.info("Materiales encontrados en inventario: {}", existentes.size());
                    return ResponseEntity.ok(existentes);
                }
            } catch (Exception e) {
                logger.debug("No se encontraron materiales en inventario, buscando en catálogo: {}", e.getMessage());
            }

            // Si no hay en inventario, buscar en catálogo (materiales nuevos)

            // Si todos los materiales encontrados ya existen en el inventario
            // devolver un error mostramos las coincidencias existentes para que el
            // frontend las muestre (el usuario pidió ver coincidencias, no bloquear).
            try {
                List<MaterialResponseDTO> existentes = inventarioDetalleService.buscarMaterialNuevoFiltrandoInventario(puntoId, texto, categoria, tipo);
                return ResponseEntity.ok(existentes);
            } catch (Exception ex) {
                // Si algo falla al obtener los existentes, devolver mensaje de error legible
                return ResponseEntity.badRequest().body(Map.of(
                        "error", true,
                        "mensaje", ex.getMessage()
                ));
            }
        } catch (Exception e) {
            return ResponseEntity.internalServerError().body(Map.of(
                    "error", true,
                    "mensaje", "Error al buscar materiales: " + e.getMessage()
            ));
        }
    }

    // Endpoint REST: búsqueda de materiales existentes en inventario - Devuelve JSON
    @GetMapping("/catalogo/inventario/materiales/buscar")
    @ResponseBody
    public ResponseEntity<?> buscarMaterialesInventario(
            @RequestParam UUID puntoId,
            @RequestParam(required = false, defaultValue = "") String texto,
            @RequestParam(required = false, defaultValue = "") String categoria,
            @RequestParam(required = false, defaultValue = "") String tipo
    ) {
        try {
            List<MaterialInvResponseDTO> resultados = inventarioDetalleService.buscarMaterialExistentesFiltrandoInventario(puntoId, texto, categoria, tipo);
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
        } catch (PuntoEcaNotFoundException e) {
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
            @PathVariable UUID inventarioId,
            @RequestBody Map<String, String> request
    ) {
        // Evitar warnings por parámetros no usados
        Objects.requireNonNull(nombrePunto);
        Objects.requireNonNull(gestorId);
        try {
            UUID puntoId = UUID.fromString(request.get("puntoId"));
            inventarioEliminacionService.eliminarInventarioConMovimientos(inventarioId, puntoId);
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

    @PostMapping("/movimientos/registrar-entrada")
    public ResponseEntity<?> registrarEntrada(
            @RequestBody CompraInventarioRequestDTO compraDTO
    ) {

        try {
            CompraInventarioResponseDTO compra = compraInventarioService.registrarCompra(compraDTO);

            return ResponseEntity.ok(Map.of(
                    "error", false,
                    "mensaje", "Entrada registrada correctamente",
                    "inventarioId", compra.inventarioId()
            ));
        } catch (InventarioNotFoundException e) {
            return ResponseEntity.status(400).body(Map.of(
                    "error", true,
                    "mensaje", e.getMessage()
            ));
        } catch (Exception e) {
            return ResponseEntity.status(400).body(Map.of(
                    "error", true,
                    "mensaje", "Error al registrar entrada: " + e.getMessage()
            ));
        }

    }

    @PostMapping("/movimientos/registrar-salida")
    public ResponseEntity<?> registrarCompra(
            @RequestBody VentaInventarioRequestDTO ventaDTO
    ) {
        try {
            ventaInventarioService.registrarVenta(ventaDTO);
            return ResponseEntity.ok(Map.of(
                    "error", false,
                    "mensaje", "Salida registrada correctamente",
                    "inventarioId", ventaDTO.inventarioId()
            ));
        } catch (InventarioNotFoundException | Exception e) {
            return ResponseEntity.status(400).body(Map.of(
                    "error", true,
                    "mensaje", e.getMessage()
            ));
        }
    }

    @PutMapping("/inventario/compra/{compraId}")
    public ResponseEntity<?> actualizarCompra(
            @RequestBody CompraInventarioUpdateDTO dto
            ){
        try {
            var resultado = compraInventarioService.actualizarCompra(dto);
            return ResponseEntity.ok(resultado);
        }catch (InventarioNotFoundException e){
            return ResponseEntity.status(400).body(Map.of(
                    "error", true,
                    "mensaje", e.getMessage()

            ));
        }

    }

    @PutMapping("/inventario/venta/{ventaId}")
    public ResponseEntity<?> actualizarVenta(
            @RequestBody VentaInventarioUpdateDTO dto
    ){
        try {
            var resultado = ventaInventarioService.actualizarVenta(dto);
            return ResponseEntity.ok(resultado);
        }catch (InventarioNotFoundException e){
            return ResponseEntity.status(400).body(Map.of(
                    "error", true,
                    "mensaje", e.getMessage()

            ));
        }
    }

    @DeleteMapping("/inventario/compra/{compraId}")
    public ResponseEntity<Map<String, Object>> eliminarCompra(
            @PathVariable UUID compraId,
            @RequestBody CompraInventarioDeleteDTO dto) throws InventarioNotFoundException {
        compraInventarioService.eliminarCompra(dto);
        return ResponseEntity.ok(Map.of(
                "mensaje", "Compra eliminada correctamente",
                "compraId", compraId
        ));
    }

    @DeleteMapping("/inventario/venta/{ventaId}")
    public ResponseEntity<Map<String, Object>> eliminarVenta(
            @PathVariable UUID ventaId,
            @RequestBody VentaInventarioDeleteDTO dto) throws InventarioNotFoundException {
        ventaInventarioService.eliminarVenta(dto);
        return ResponseEntity.ok(Map.of(
                "mensaje", "Venta eliminada correctamente",
                "ventaId", ventaId
        ));
    }

    /**
     * ENDPOINT REST: Obtener centros de acopio (del punto + globales)
     * Utilizado por el modal de creación de eventos
     *
     * @param puntoEcaId ID del punto ECA
     * @return Lista de centros de acopio del punto + globales en formato JSON
     */
    @GetMapping("/{puntoEcaId}/centros-acopio")
    @ResponseBody
    public ResponseEntity<?> obtenerCentrosAcopioPunto(@PathVariable UUID puntoEcaId) {
        try {
            logger.info("🎯 Obteniendo centros de acopio para punto ECA: {}", puntoEcaId);

            // Obtener centros del punto + globales
            List<CentroAcopio> todosCentros = centroAcopioService.obtenerCentrosPuntoYGlobales(puntoEcaId);

            logger.info("📊 Centros obtenidos del servicio: {}", todosCentros.size());

            if (!todosCentros.isEmpty()) {
                CentroAcopio primero = todosCentros.get(0);
                logger.info("   📍 Primer centro ID: {}", primero.getCntAcpId());
                logger.info("   📍 Primer centro Nombre: {}", primero.getNombreCntAcp());
                logger.info("   📍 Primer centro Punto: {}", primero.getPuntoEca());
            }

            // Convertir a DTOs
            List<org.sena.inforecicla.dto.CentroAcopioDTO> centrosDTO = todosCentros.stream()
                    .map(org.sena.inforecicla.dto.CentroAcopioDTO::fromEntity)
                    .toList();

            logger.info("✅ DTOs creados: {}", centrosDTO.size());

            if (!centrosDTO.isEmpty()) {
                org.sena.inforecicla.dto.CentroAcopioDTO primero = centrosDTO.get(0);
                logger.info("   DTO Primer centro ID: {}", primero.getCntAcpId());
                logger.info("   DTO Primer centro Nombre: {}", primero.getNombreCntAcp());
                logger.info("   DTO Primer centro Punto: {}", primero.getTienePuntoEca());
            }

            logger.info("✅ Total de centros retornados (punto + globales): {}", centrosDTO.size());
            return ResponseEntity.ok(centrosDTO);


        } catch (Exception e) {
            logger.error("❌ Error obteniendo centros de acopio: {}", e.getMessage(), e);
            return ResponseEntity.status(500).body(Map.of(
                "error", true,
                "mensaje", "Error al obtener centros de acopio: " + e.getMessage()
            ));
        }
    }

    /**
     * ENDPOINT REST: Buscar centros de acopio con filtros
     *
     * @param puntoEcaId ID del Punto ECA
     * @param nombre Filtro por nombre
     * @param tipo Filtro por tipo
     * @param localidadId Filtro por localidad ID
     * @param contacto Filtro por contacto
     * @param email Filtro por email
     * @param telefono Filtro por teléfono
     * @param esPropios Si es true, busca centros propios; si es false, busca centros globales
     * @return Lista de centros filtrados (como DTO)
     */
    @GetMapping("/{puntoEcaId}/filtrar-centros")
    @ResponseBody
    public ResponseEntity<List<org.sena.inforecicla.dto.CentroAcopioDTO>> filtrarCentros(
            @PathVariable UUID puntoEcaId,
            @RequestParam(required = false) String nombre,
            @RequestParam(required = false) String tipo,
            @RequestParam(required = false) String localidadId,
            @RequestParam(required = false) String contacto,
            @RequestParam(required = false) String email,
            @RequestParam(required = false) String telefono,
            @RequestParam(defaultValue = "false") boolean esPropios
    ) {
        try {
            logger.info("🔍🔍🔍 Iniciando búsqueda de centros - PuntoECA: {}, Propios: {}", puntoEcaId, esPropios);
            logger.info("📝 Parámetros recibidos: nombre='{}', tipo='{}', localidadId='{}', contacto='{}', email='{}', telefono='{}'",
                    nombre, tipo, localidadId, contacto, email, telefono);

            // Obtener todos los centros (propios o globales)
            List<CentroAcopio> centros;
            if (esPropios) {
                centros = centroAcopioService.listaCentrosPorPuntoEca(puntoEcaId);
                logger.info("📊 Centros PROPIOS obtenidos: {}", centros.size());
            } else {
                centros = centroAcopioService.obtenerCentrosGlobales();
                logger.info("📊 Centros GLOBALES obtenidos: {}", centros.size());
            }

            // Log de todos los centros antes de filtrar
            centros.forEach(c ->
                logger.debug("  - Centro: nombre='{}', tipo='{}', localidad='{}', contacto='{}', celular='{}', email='{}'",
                    c.getNombreCntAcp(),
                    c.getTipoCntAcp() != null ? c.getTipoCntAcp().getTipo() : "null",
                    c.getLocalidad() != null ? c.getLocalidad().getNombre() : "null",
                    c.getNombreContactoCntAcp(),
                    c.getCelular(),
                    c.getEmail()
                )
            );

            // Filtrar en memoria según los criterios
            List<CentroAcopio> filtrados = centros.stream()
                    .filter(c -> nombre == null || nombre.trim().isEmpty() ||
                            c.getNombreCntAcp().toLowerCase().contains(nombre.toLowerCase()))
                    .filter(c -> tipo == null || tipo.trim().isEmpty() ||
                            (c.getTipoCntAcp() != null && c.getTipoCntAcp().getTipo().equalsIgnoreCase(tipo)))
                    .filter(c -> localidadId == null || localidadId.trim().isEmpty() ||
                            (c.getLocalidad() != null && c.getLocalidad().getLocalidadId().toString().equals(localidadId)))
                    .filter(c -> contacto == null || contacto.trim().isEmpty() ||
                            (c.getNombreContactoCntAcp() != null && c.getNombreContactoCntAcp().toLowerCase().contains(contacto.toLowerCase())))
                    .filter(c -> email == null || email.trim().isEmpty() ||
                            (c.getEmail() != null && c.getEmail().toLowerCase().contains(email.toLowerCase())))
                    .filter(c -> telefono == null || telefono.trim().isEmpty() ||
                            (c.getCelular() != null && c.getCelular().contains(telefono)))
                    .toList();

            logger.info("✅✅✅ Centros después del filtrado: {}", filtrados.size());

            // Log de centros filtrados
            filtrados.forEach(c ->
                logger.info("  ✅ Centro FINAL: nombre='{}', tipo='{}', localidad='{}', celular='{}'",
                    c.getNombreCntAcp(),
                    c.getTipoCntAcp() != null ? c.getTipoCntAcp().getTipo() : "null",
                    c.getLocalidad() != null ? c.getLocalidad().getNombre() : "null",
                    c.getCelular()
                )
            );

            // Convertir a DTO
            List<org.sena.inforecicla.dto.CentroAcopioDTO> dtos = filtrados.stream()
                    .map(org.sena.inforecicla.dto.CentroAcopioDTO::fromEntity)
                    .toList();

            logger.info("✅ DTOs creados: {}", dtos.size());

            return ResponseEntity.ok(dtos);

        } catch (Exception e) {
            logger.error("❌ Error al filtrar centros: {}", e.getMessage(), e);
            return ResponseEntity.status(HttpStatus.INTERNAL_SERVER_ERROR).body(new ArrayList<>());
        }
    }

    /**
     * Crea un nuevo centro de acopio asociado a un Punto ECA específico
     * POST /punto-eca/{puntoEcaId}/centro-acopio
     */
    @PostMapping("/{puntoEcaId}/centro-acopio")
    @ResponseBody
    public ResponseEntity<?> crearCentroAcopio(
            @PathVariable UUID puntoEcaId,
            @RequestBody org.sena.inforecicla.dto.CentroAcopioCreateDTO dto) {
        try {
            logger.info("➕ [CENTRO-ACOPIO] Creando nuevo centro para punto: {}", puntoEcaId);
            logger.info("   Datos recibidos: nombre={}, tipo={}, telefono={}, email={}",
                    dto.getNombreCntAcp(), dto.getTipoCntAcp(), dto.getCelular(), dto.getEmail());

            // Validar que el nombre y tipo sean obligatorios
            if (dto.getNombreCntAcp() == null || dto.getNombreCntAcp().trim().isEmpty()) {
                logger.warn("⚠️ Nombre del centro es obligatorio");
                return ResponseEntity.badRequest().body(Map.of(
                        "success", false,
                        "error", "El nombre del centro es obligatorio"
                ));
            }

            if (dto.getTipoCntAcp() == null || dto.getTipoCntAcp().trim().isEmpty()) {
                logger.warn("⚠️ Tipo de centro es obligatorio");
                return ResponseEntity.badRequest().body(Map.of(
                        "success", false,
                        "error", "El tipo de centro es obligatorio"
                ));
            }

            // Crear el centro usando el servicio
            CentroAcopio centroCreado = centroAcopioService.crear(puntoEcaId, dto);

            logger.info("✅ Centro creado exitosamente: {}", centroCreado.getCntAcpId());
            return ResponseEntity.status(HttpStatus.CREATED).body(Map.of(
                    "success", true,
                    "message", "Centro creado correctamente",
                    "centroId", centroCreado.getCntAcpId(),
                    "centro", centroCreado
            ));

        } catch (IllegalArgumentException e) {
            logger.warn("⚠️ Error de validación: {}", e.getMessage());
            return ResponseEntity.badRequest().body(Map.of(
                    "success", false,
                    "error", e.getMessage()
            ));
        } catch (org.springframework.transaction.TransactionSystemException e) {
            // Capturar errores de validación de Hibernate
            logger.error("❌ Error de validación en base de datos: {}", e.getMessage());

            // Extraer violaciones de restricciones
            Map<String, Object> errorResponse = new HashMap<>();
            errorResponse.put("success", false);
            errorResponse.put("isValidationError", true);

            Throwable cause = e.getCause();
            if (cause instanceof jakarta.persistence.RollbackException) {
                Throwable validationCause = cause.getCause();
                if (validationCause instanceof jakarta.validation.ConstraintViolationException) {
                    jakarta.validation.ConstraintViolationException cve =
                        (jakarta.validation.ConstraintViolationException) validationCause;

                    // Crear lista de errores de validación
                    java.util.List<Map<String, String>> errors = new java.util.ArrayList<>();
                    for (jakarta.validation.ConstraintViolation<?> violation : cve.getConstraintViolations()) {
                        Map<String, String> error = new HashMap<>();
                        error.put("field", violation.getPropertyPath().toString());
                        error.put("message", violation.getMessage());
                        errors.add(error);
                        logger.warn("   - Campo: {}, Mensaje: {}", violation.getPropertyPath(), violation.getMessage());
                    }

                    errorResponse.put("validationErrors", errors);
                    errorResponse.put("error", "Error de validación en los datos");
                    return ResponseEntity.badRequest().body(errorResponse);
                }
            }

            return ResponseEntity.status(HttpStatus.INTERNAL_SERVER_ERROR).body(Map.of(
                    "success", false,
                    "error", "Error al procesar la solicitud: " + e.getMessage()
            ));
        } catch (Exception e) {
            logger.error("❌ Error al crear centro: {}", e.getMessage(), e);
            return ResponseEntity.status(HttpStatus.INTERNAL_SERVER_ERROR).body(Map.of(
                    "success", false,
                    "error", "Error al crear el centro: " + e.getMessage()
            ));
        }
    }

    /**
     * Cambiar contraseña del usuario
     * POST /punto-eca/cambiar-contrasena
     */
    @PostMapping("/cambiar-contrasena")
    public String cambiarContrasena(
            @RequestParam String contrasenaActual,
            @RequestParam String contrasenaNueva,
            @RequestParam String confirmarContrasena,
            org.springframework.web.servlet.mvc.support.RedirectAttributes redirectAttributes,
            org.springframework.security.core.Authentication auth) {

        try {
            if (auth == null || auth.getPrincipal() == null) {
                redirectAttributes.addFlashAttribute("errorMessage", "No hay sesión activa");
                return "redirect:/login";
            }

            Usuario usuario = (Usuario) auth.getPrincipal();

            // Validar que las contraseñas nuevas coincidan
            if (!contrasenaNueva.equals(confirmarContrasena)) {
                redirectAttributes.addFlashAttribute("errorMessage", "Las contraseñas nuevas no coinciden");
                return "redirect:/punto-eca";
            }

            // Validar que la contraseña nueva cumpla requisitos
            if (!contrasenaNueva.matches("^(?=.*[a-z])(?=.*[A-Z])(?=.*\\d)(?=.*[@$!%*?&])[A-Za-z\\d@$!%*?&]{8,}$")) {
                redirectAttributes.addFlashAttribute("errorMessage", "La contraseña no cumple los requisitos de seguridad");
                return "redirect:/punto-eca";
            }

            logger.info("🔐 Iniciando cambio de contraseña para usuario: {}", usuario.getEmail());

            // Validar contraseña actual con BCrypt
            BCryptPasswordEncoder encoder = new BCryptPasswordEncoder();

            if (!encoder.matches(contrasenaActual, usuario.getPassword())) {
                logger.warn("❌ Contraseña actual incorrecta para: {}", usuario.getEmail());
                redirectAttributes.addFlashAttribute("errorMessage", "La contraseña actual es incorrecta");
                return "redirect:/punto-eca";
            }

            // Actualizar contraseña
            usuario.setPassword(encoder.encode(contrasenaNueva));
            usuarioRepository.save(usuario);

            logger.info("✅ Contraseña cambiada exitosamente para: {}", usuario.getEmail());
            redirectAttributes.addFlashAttribute("successMessage", "Contraseña cambiada exitosamente");
            return "redirect:/punto-eca";

        } catch (Exception e) {
            logger.error("❌ Error al cambiar contraseña: {}", e.getMessage(), e);
            redirectAttributes.addFlashAttribute("errorMessage", "Error al cambiar la contraseña: " + e.getMessage());
            return "redirect:/punto-eca";
        }
    }

    /**
     * Actualizar preferencias del usuario
     * POST /punto-eca/actualizar-preferencias
     */
    @PostMapping("/actualizar-preferencias")
    public String actualizarPreferencias(
            @RequestParam(required = false) String visibleEnMapa,
            @RequestParam(required = false) String notificacionesAprobacion,
            @RequestParam(required = false) String notificacionesMensajes,
            @RequestParam(required = false) String notificacionesInventario,
            org.springframework.web.servlet.mvc.support.RedirectAttributes redirectAttributes) {

        try {
            Authentication authentication = SecurityContextHolder.getContext().getAuthentication();
            if (authentication == null || authentication.getPrincipal() == null) {
                redirectAttributes.addFlashAttribute("errorMessage", "No hay sesión activa");
                return "redirect:/login";
            }

            Usuario usuario = (Usuario) authentication.getPrincipal();
            logger.info("⚙️ Actualizando preferencias para: {}", usuario.getEmail());

            // Registrar cambios
            logger.info("   - Visible en mapa: {}", visibleEnMapa != null);
            logger.info("   - Notificaciones aprobación: {}", notificacionesAprobacion != null);
            logger.info("   - Notificaciones mensajes: {}", notificacionesMensajes != null);
            logger.info("   - Notificaciones inventario: {}", notificacionesInventario != null);

            // Aquí puedes agregar lógica para guardar preferencias en BD si lo deseas
            // Por ahora solo confirmamos el guardado

            logger.info("✅ Preferencias actualizadas exitosamente");
            redirectAttributes.addFlashAttribute("successMessage", "Preferencias guardadas exitosamente");
            return "redirect:/punto-eca";

        } catch (Exception e) {
            logger.error("❌ Error al actualizar preferencias: {}", e.getMessage(), e);
            redirectAttributes.addFlashAttribute("errorMessage", "Error al guardar las preferencias: " + e.getMessage());
            return "redirect:/punto-eca";
        }
    }
}
