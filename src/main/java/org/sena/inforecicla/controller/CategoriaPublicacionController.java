package org.sena.inforecicla.controller;

import lombok.RequiredArgsConstructor;
import org.sena.inforecicla.model.CategoriaPublicacion;
import org.sena.inforecicla.model.Usuario;
import org.sena.inforecicla.service.CategoriaPublicacionService;
import org.sena.inforecicla.service.PublicacionService;
import org.sena.inforecicla.service.UsuarioService;
import org.springframework.stereotype.Controller;
import org.springframework.ui.Model;
import org.springframework.web.bind.annotation.*;
import org.springframework.web.servlet.mvc.support.RedirectAttributes;

import java.util.UUID;

@Controller
@RequestMapping("/categorias")
@RequiredArgsConstructor
public class CategoriaPublicacionController {

    private final CategoriaPublicacionService categoriaPublicacionService;
    private final UsuarioService usuarioService;
    private final PublicacionService publicacionService;

    // Layout principal
    @GetMapping("/{usuarioId}")
    public String categoriaLayout(@PathVariable UUID usuarioId,
                                  @RequestParam(required = false, defaultValue = "indexCategoriaPublicacion") String seccion,
                                  Model model) {
        Usuario usuario = usuarioService.obtenerUsuarioPorId(usuarioId);
        model.addAttribute("usuario", usuario);
        model.addAttribute("seccion", seccion);
        return "categoriaPublicacion-layout";
    }

    // Inicio con estadísticas
    @GetMapping("/{usuarioId}/inicio")
    public String inicioCategorias(@PathVariable UUID usuarioId, Model model) {
        Usuario usuario = usuarioService.obtenerUsuarioPorId(usuarioId);

        // Obtener todas las categorías
        var categorias = categoriaPublicacionService.obtenerTodas();
        long totalCategorias = categorias.size();
        long categoriasActivas = categorias.stream()
                .filter(c -> "ACTIVO".equals(c.getEstado().name()))
                .count();

        // Obtener publicaciones asociadas (simplificado)
        var publicaciones = publicacionService.mostrarTodasLasPublicaciones();
        long totalPublicacionesAsociadas = publicaciones.size();

        // Obtener categorías recientes (últimas 6)
        var categoriasRecientes = categorias.stream()
                .limit(6)
                .toList();

        model.addAttribute("usuario", usuario);
        model.addAttribute("totalCategorias", totalCategorias);
        model.addAttribute("categoriasActivas", categoriasActivas);
        model.addAttribute("totalPublicacionesAsociadas", totalPublicacionesAsociadas);
        model.addAttribute("categoriasRecientes", categoriasRecientes);
        model.addAttribute("seccion", "indexCategoriaPublicacion");

        return "categoriaPublicacion-layout";
    }

    // Listar categorías
    @GetMapping("/{usuarioId}/listar")
    public String listarCategorias(@PathVariable UUID usuarioId,
                                   @RequestParam(required = false) String estado,
                                   @RequestParam(required = false) String busqueda,
                                   @RequestParam(defaultValue = "0") int page,
                                   @RequestParam(defaultValue = "10") int size,
                                   Model model) {
        Usuario usuario = usuarioService.obtenerUsuarioPorId(usuarioId);

        var todasCategorias = categoriaPublicacionService.obtenerTodas();

        // Filtrar por estado si se proporciona
        var categoriasFiltradas = todasCategorias.stream()
                .filter(c -> estado == null || estado.isEmpty() || c.getEstado().name().equals(estado))
                .filter(c -> busqueda == null || busqueda.isEmpty() ||
                        c.getNombre().toLowerCase().contains(busqueda.toLowerCase()) ||
                        c.getDescripcion().toLowerCase().contains(busqueda.toLowerCase()))
                .toList();

        // Implementación simple de paginación
        int start = page * size;
        int end = Math.min(start + size, categoriasFiltradas.size());
        var categoriasPaginadas = categoriasFiltradas.subList(start, end);
        int totalPaginas = (int) Math.ceil((double) categoriasFiltradas.size() / size);

        model.addAttribute("usuario", usuario);
        model.addAttribute("categorias", categoriasPaginadas);
        model.addAttribute("paginaActual", page);
        model.addAttribute("totalPaginas", totalPaginas);
        model.addAttribute("totalElementos", categoriasFiltradas.size());
        model.addAttribute("seccion", "listCategoriaPublicacion");

        return "categoriaPublicacion-layout";
    }

    // Mostrar formulario de creación
    @GetMapping("/{usuarioId}/crear")
    public String mostrarFormularioCreacion(@PathVariable UUID usuarioId, Model model) {
        Usuario usuario = usuarioService.obtenerUsuarioPorId(usuarioId);

        model.addAttribute("usuario", usuario);
        model.addAttribute("categoria", new CategoriaPublicacion());
        model.addAttribute("seccion", "createCategoriaPublicacion");

        return "categoriaPublicacion-layout";
    }

    // Crear categoría (POST)
    @PostMapping("/{usuarioId}/crear")
    public String crearCategoria(@PathVariable UUID usuarioId,
                                 @ModelAttribute CategoriaPublicacion categoria,
                                 RedirectAttributes redirectAttributes) {
        Usuario usuario = usuarioService.obtenerUsuarioPorId(usuarioId);

        // Establecer estado por defecto si no viene
        if (categoria.getEstado() == null) {
            categoria.setEstado(org.sena.inforecicla.model.enums.Estado.ACTIVO);
        }

        categoriaPublicacionService.crearCategoria(categoria);

        redirectAttributes.addFlashAttribute("success", "Categoría creada exitosamente");
        return "redirect:/categorias/" + usuarioId + "/listar";
    }

    // Mostrar detalle de categoría
    @GetMapping("/{usuarioId}/mostrar/{categoriaId}")
    public String mostrarCategoria(@PathVariable UUID usuarioId,
                                   @PathVariable UUID categoriaId,
                                   Model model) {
        Usuario usuario = usuarioService.obtenerUsuarioPorId(usuarioId);
        CategoriaPublicacion categoria = categoriaPublicacionService.obtenerPorId(categoriaId);

        // Contar publicaciones por estado en esta categoría
        var publicaciones = categoria.getPublicaciones();
        long publicacionesPublicadas = publicaciones != null ?
                publicaciones.stream()
                        .filter(p -> p.getEstado() == org.sena.inforecicla.model.enums.EstadoPublicacion.PUBLICADO)
                        .count() : 0;

        long publicacionesBorradores = publicaciones != null ?
                publicaciones.stream()
                        .filter(p -> p.getEstado() == org.sena.inforecicla.model.enums.EstadoPublicacion.BORRADOR)
                        .count() : 0;

        model.addAttribute("usuario", usuario);
        model.addAttribute("categoria", categoria);
        model.addAttribute("publicacionesPublicadas", publicacionesPublicadas);
        model.addAttribute("publicacionesBorradores", publicacionesBorradores);
        model.addAttribute("seccion", "showCategoriaPublicacion");

        return "categoriaPublicacion-layout";
    }

    // Mostrar formulario de edición
    @GetMapping("/{usuarioId}/editar/{categoriaId}")
    public String mostrarFormularioEdicion(@PathVariable UUID usuarioId,
                                           @PathVariable UUID categoriaId,
                                           Model model) {
        Usuario usuario = usuarioService.obtenerUsuarioPorId(usuarioId);
        CategoriaPublicacion categoria = categoriaPublicacionService.obtenerPorId(categoriaId);

        model.addAttribute("usuario", usuario);
        model.addAttribute("categoria", categoria);
        model.addAttribute("seccion", "editCategoriaPublicacion");

        return "categoriaPublicacion-layout";
    }

    // Actualizar categoría (POST)
    @PostMapping("/{usuarioId}/actualizar/{categoriaId}")
    public String actualizarCategoria(@PathVariable UUID usuarioId,
                                      @PathVariable UUID categoriaId,
                                      @ModelAttribute CategoriaPublicacion categoria,
                                      RedirectAttributes redirectAttributes) {
        categoriaPublicacionService.actualizarCategoria(categoriaId, categoria);

        redirectAttributes.addFlashAttribute("success", "Categoría actualizada exitosamente");
        return "redirect:/categorias/" + usuarioId + "/mostrar/" + categoriaId;
    }

    // Eliminar categoría
    @DeleteMapping("/{categoriaId}")
    public String eliminarCategoria(@PathVariable UUID categoriaId,
                                    @RequestParam UUID usuarioId,
                                    RedirectAttributes redirectAttributes) {
        categoriaPublicacionService.eliminarCategoria(categoriaId);

        redirectAttributes.addFlashAttribute("success", "Categoría eliminada exitosamente");
        return "redirect:/categorias/" + usuarioId + "/listar";
    }
}