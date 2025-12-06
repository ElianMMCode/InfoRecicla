package org.sena.inforecicla.controller;

import lombok.AllArgsConstructor;
import org.sena.inforecicla.dto.publicacion.PublicacionResponseDTO;
import org.sena.inforecicla.dto.usuario.UsuarioResponseDTO;
import org.sena.inforecicla.service.*;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;
import org.springframework.data.domain.Page;
import org.springframework.data.domain.PageRequest;
import org.springframework.data.domain.Pageable;
import org.springframework.stereotype.Controller;
import org.springframework.ui.Model;
import org.springframework.web.bind.annotation.*;

import java.util.*;

@Controller
@AllArgsConstructor
@RequestMapping("/publicacion")
public class PublicacionController {

    private static final Logger logger = LoggerFactory.getLogger(PublicacionController.class);

    // ✅ Services
    private final PublicacionService publicacionService;
    private final CategoriaPublicacionService categoriaService;
    private final UsuarioService usuarioService;
    private final EstadisticaService estadisticaService;

    // Vista principal con usuarioId
    @GetMapping("/{admin}/{usuarioId}")
    public String mostrarPublicaciones(
            @PathVariable String admin,
            @PathVariable UUID usuarioId,
            @RequestParam(defaultValue = "indexPublicacion") String seccion,
            @RequestParam(required = false) UUID publicacionId,
            @RequestParam(required = false) String filtro,
            @RequestParam(required = false) UUID categoriaId,
            @RequestParam(defaultValue = "1") int pagina,
            @RequestParam(defaultValue = "10") int tamano,
            Model model) {

        // Obtener usuario con validación del servicio
        UsuarioResponseDTO usuario = null;
        try {
            if (usuarioService == null) {
                logger.error("UsuarioService no está disponible.");
            } else {
                usuario = usuarioService.buscarUsuarioPorId(usuarioId);
            }
        } catch (Exception e) {
            logger.error("Error al obtener usuario por id {}: {}", usuarioId, e.getMessage());
        }

        // Agregar datos básicos siempre
        model.addAttribute("usuario", usuario);
        model.addAttribute("admin", admin);
        model.addAttribute("seccion", seccion);

        // Cargar datos condicionales según la sección
        switch (seccion) {
            case "listPublicacion" -> cargarSeccionListado(usuario, model, filtro, categoriaId, pagina, tamano);
            case "showPublicacion" -> cargarSeccionDetalle(model, publicacionId);
            case "editPublicacion" -> cargarSeccionEdicion(model, publicacionId, usuario);
            case "createPublicacion" -> cargarSeccionCreacion(model, usuario);
            default -> cargarSeccionDashboard(usuario, model);
        }

        // La plantilla existente es templates/views/Admin/Publicaciones/publicacion-layout.html
        return "views/Admin/Publicaciones/publicacion-layout";
    }

    // Métodos auxiliares para cargar secciones (valida servicios y evita NPEs)
    private void cargarSeccionListado(UsuarioResponseDTO usuario, Model model,
                                     String filtro, UUID categoriaId,
                                     int pagina, int tamano) {
        // Usar parámetros para evitar advertencias
        model.addAttribute("filtro", filtro);
        model.addAttribute("categoriaId", categoriaId);
        model.addAttribute("usuario", usuario);

        // Preparar paginación
        Pageable pageable = PageRequest.of(Math.max(0, pagina - 1), Math.max(1, tamano));

        // Validación y llamada real al servicio de publicaciones
        if (publicacionService == null) {
            logger.warn("PublicacionService no disponible, usando valores por defecto para listado.");
            model.addAttribute("publicaciones", Collections.emptyList());
            model.addAttribute("totalPages", 0);
            model.addAttribute("totalElements", 0);
        } else {
            try {
                Page<PublicacionResponseDTO> page = publicacionService.filtrarPublicaciones(filtro, null, categoriaId, pageable);
                model.addAttribute("publicaciones", page.getContent());
                model.addAttribute("totalPages", page.getTotalPages());
                model.addAttribute("totalElements", page.getTotalElements());
            } catch (Exception e) {
                logger.error("Error al cargar listado de publicaciones: {}", e.getMessage());
                model.addAttribute("publicaciones", Collections.emptyList());
                model.addAttribute("totalPages", 0);
                model.addAttribute("totalElements", 0);
            }
        }

        if (categoriaService == null) {
            logger.warn("CategoriaPublicacionService no disponible, categorias vacías.");
            model.addAttribute("categorias", Collections.emptyList());
        } else {
            try {
                model.addAttribute("categorias", categoriaService.obtenerTodasLasCategorias());
            } catch (Exception e) {
                logger.error("Error al cargar categorias: {}", e.getMessage());
                model.addAttribute("categorias", Collections.emptyList());
            }
        }

        model.addAttribute("pagina", pagina);
        model.addAttribute("size", tamano);
    }

    private void cargarSeccionDetalle(Model model, UUID publicacionId) {
        // Usar publicacionId en el modelo
        model.addAttribute("publicacionId", publicacionId);

        if (publicacionService == null) {
            logger.warn("PublicacionService no disponible, detalle no cargado.");
            model.addAttribute("publicacion", null);
            return;
        }

        try {
            if (publicacionId == null) {
                model.addAttribute("publicacion", null);
            } else {
                PublicacionResponseDTO dto = publicacionService.obtenerPublicacionPorId(publicacionId);
                model.addAttribute("publicacion", dto);
            }
        } catch (Exception e) {
            logger.error("Error al cargar detalle de publicación: {}", e.getMessage());
            model.addAttribute("publicacion", null);
        }
    }

    private void cargarSeccionEdicion(Model model, UUID publicacionId, UsuarioResponseDTO usuario) {
        model.addAttribute("publicacionId", publicacionId);
        model.addAttribute("usuario", usuario);

        if (publicacionService == null) {
            logger.warn("PublicacionService no disponible, edición sin datos de publicación.");
            model.addAttribute("publicacion", null);
        } else {
            try {
                if (publicacionId == null) {
                    model.addAttribute("publicacion", null);
                } else {
                    PublicacionResponseDTO dto = publicacionService.obtenerPublicacionPorId(publicacionId);
                    model.addAttribute("publicacion", dto);
                }
            } catch (Exception e) {
                logger.error("Error al cargar publicación para edición: {}", e.getMessage());
                model.addAttribute("publicacion", null);
            }
        }

        if (categoriaService == null) {
            logger.warn("CategoriaPublicacionService no disponible, categorias vacías en edición.");
            model.addAttribute("categorias", Collections.emptyList());
        } else {
            try {
                model.addAttribute("categorias", categoriaService.obtenerTodasLasCategorias());
            } catch (Exception e) {
                logger.error("Error al cargar categorias para edición: {}", e.getMessage());
                model.addAttribute("categorias", Collections.emptyList());
            }
        }
    }

    private void cargarSeccionCreacion(Model model, UsuarioResponseDTO usuario) {
        model.addAttribute("usuario", usuario);

        if (categoriaService == null) {
            logger.warn("CategoriaPublicacionService no disponible, categorias vacías en creación.");
            model.addAttribute("categorias", Collections.emptyList());
        } else {
            try {
                model.addAttribute("categorias", categoriaService.obtenerTodasLasCategorias());
            } catch (Exception e) {
                logger.error("Error al cargar categorias para creación: {}", e.getMessage());
                model.addAttribute("categorias", Collections.emptyList());
            }
        }
    }

    private void cargarSeccionDashboard(UsuarioResponseDTO usuario, Model model) {
        // Usar usuario en el modelo
        model.addAttribute("usuario", usuario);

        if (estadisticaService == null) {
            logger.warn("EstadisticaService no disponible, estadísticas vacías en dashboard.");
            model.addAttribute("estadisticas", Collections.emptyMap());
        } else {
            try {
                if (usuario != null) {
                    model.addAttribute("estadisticas", estadisticaService.obtenerEstadisticasPorUsuario(usuario.usuarioId()));
                } else {
                    model.addAttribute("estadisticas", estadisticaService.obtenerEstadisticasPublicaciones());
                }
            } catch (Exception e) {
                logger.error("Error al cargar estadísticas: {}", e.getMessage());
                model.addAttribute("estadisticas", Collections.emptyMap());
            }
        }
    }

    // ============================================================================
    // NUEVOS ENDPOINTS PARA RUTAS SIMPLES (/publicacion/index, /publicacion/list, etc.)
    // ============================================================================

    @GetMapping("/index")
    public String index(Model model) {
        model.addAttribute("seccion", "index");
        cargarSeccionDashboard(null, model);
        return "views/Publicacion/publicacion-layout";
    }

    @GetMapping("/list")
    public String list(
            @RequestParam(required = false) String filtro,
            @RequestParam(required = false) UUID categoriaId,
            @RequestParam(defaultValue = "1") int pagina,
            @RequestParam(defaultValue = "10") int tamano,
            Model model) {
        model.addAttribute("seccion", "list");
        cargarSeccionListado(null, model, filtro, categoriaId, pagina, tamano);
        return "views/Publicacion/publicacion-layout";
    }

    @GetMapping("/create")
    public String create(Model model) {
        model.addAttribute("seccion", "create");
        cargarSeccionCreacion(model, null);
        return "views/Publicacion/publicacion-layout";
    }

    @GetMapping("/edit/{id}")
    public String edit(@PathVariable UUID id, Model model) {
        model.addAttribute("seccion", "edit");
        cargarSeccionEdicion(model, id, null);
        return "views/Publicacion/publicacion-layout";
    }

    @GetMapping("/show/{id}")
    public String show(@PathVariable UUID id, Model model) {
        model.addAttribute("seccion", "show");
        cargarSeccionDetalle(model, id);
        return "views/Publicacion/publicacion-layout";
    }

    // ============================================================================
    // ENDPOINTS COMENTADOS - No se utilizan por ahora
    // ============================================================================

    /* COMENTADO: Endpoint de Mis Publicaciones
    @GetMapping("/mias")
    public String mias(Model model) {
        model.addAttribute("seccion", "mias");
        return "views/Publicacion/publicacion-layout";
    }
    */

    /* COMENTADO: Endpoint de Borradores
    @GetMapping("/borradores")
    public String borradores(Model model) {
        model.addAttribute("seccion", "borradores");
        return "views/Publicacion/publicacion-layout";
    }
    */

    /* COMENTADO: Endpoint de Reportes
    @GetMapping("/reportes")
    public String reportes(Model model) {
        model.addAttribute("seccion", "reportes");
        return "views/Publicacion/publicacion-layout";
    }
    */

    /* COMENTADO: Endpoint de Configuración
    @GetMapping("/configuracion")
    public String configuracion(Model model) {
        model.addAttribute("seccion", "configuracion");
        return "views/Publicacion/publicacion-layout";
    }
    */

    // Resto del código del controller se mantiene igual...
    // Solo cambia las referencias de publicacionServiceImpl a publicacionService
}