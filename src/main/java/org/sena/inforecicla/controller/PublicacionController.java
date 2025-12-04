package org.sena.inforecicla.controller;

import jakarta.validation.Valid;
import lombok.RequiredArgsConstructor;
import org.sena.inforecicla.dto.publicacion.PublicacionRequestDTO;
import org.sena.inforecicla.dto.publicacion.PublicacionResponseDTO;
import org.sena.inforecicla.dto.publicacion.PublicacionUpdateDTO;
import org.sena.inforecicla.model.CategoriaPublicacion;
import org.sena.inforecicla.model.Usuario;
import org.sena.inforecicla.service.CategoriaPublicacionService;
import org.sena.inforecicla.service.PublicacionService;
import org.sena.inforecicla.service.UsuarioService;
import org.springframework.data.domain.Page;
import org.springframework.data.domain.PageRequest;
import org.springframework.data.domain.Pageable;
import org.springframework.http.HttpStatus;
import org.springframework.http.ResponseEntity;
import org.springframework.stereotype.Controller;
import org.springframework.ui.Model;
import org.springframework.validation.BindingResult;
import org.springframework.web.bind.annotation.*;
import org.springframework.web.servlet.mvc.support.RedirectAttributes;

import java.util.List;
import java.util.UUID;

@Controller
@RequestMapping("/publicaciones")
@RequiredArgsConstructor
public class PublicacionController {

    private final PublicacionService publicacionService;
    private final UsuarioService usuarioService;
    private final CategoriaPublicacionService categoriaPublicacionService;

    // ============ VISTAS ============

    // Layout principal con secciones
    @GetMapping("/{usuarioId}")
    public String publicacionLayout(@PathVariable UUID usuarioId,
                                    @RequestParam(required = false, defaultValue = "indexPublicacion") String seccion,
                                    Model model) {
        Usuario usuario = usuarioService.obtenerUsuarioPorId(usuarioId);
        model.addAttribute("usuario", usuario);
        model.addAttribute("seccion", seccion);
        return "publicacion-layout";
    }

    // Inicio con estadísticas
    @GetMapping("/{usuarioId}/inicio")
    public String inicioPublicaciones(@PathVariable UUID usuarioId, Model model) {
        Usuario usuario = usuarioService.obtenerUsuarioPorId(usuarioId);

        long totalPublicadas = publicacionService.contarPublicacionesPorUsuarioYEstado(usuarioId, org.sena.inforecicla.model.enums.EstadoPublicacion.Publicado);
        long totalBorradores = publicacionService.contarPublicacionesPorUsuarioYEstado(usuarioId, org.sena.inforecicla.model.enums.EstadoPublicacion.Borrador);
        long totalArchivadas = publicacionService.contarPublicacionesPorUsuarioYEstado(usuarioId, org.sena.inforecicla.model.enums.EstadoPublicacion.Archivado);
        long totalPublicaciones = publicacionService.contarPublicacionesPorUsuario(usuarioId);

        List<PublicacionResponseDTO> publicacionesRecientes = publicacionService.mostrarTodasLasPublicaciones()
                .stream()
                .limit(6)
                .toList();

        model.addAttribute("usuario", usuario);
        model.addAttribute("totalPublicadas", totalPublicadas);
        model.addAttribute("totalBorradores", totalBorradores);
        model.addAttribute("totalArchivadas", totalArchivadas);
        model.addAttribute("totalPublicaciones", totalPublicaciones);
        model.addAttribute("publicacionesRecientes", publicacionesRecientes);
        model.addAttribute("seccion", "indexPublicacion");

        return "publicacion-layout";
    }

    // Listar publicaciones
    @GetMapping("/{usuarioId}/listar")
    public String listarPublicaciones(@PathVariable UUID usuarioId,
                                      @RequestParam(required = false) String estado,
                                      @RequestParam(required = false) UUID categoriaId,
                                      @RequestParam(defaultValue = "0") int page,
                                      @RequestParam(defaultValue = "10") int size,
                                      Model model) {
        Usuario usuario = usuarioService.obtenerUsuarioPorId(usuarioId);

        List<PublicacionResponseDTO> publicaciones;
        if (estado != null && !estado.isEmpty()) {
            publicaciones = publicacionService.mostrarPublicacionesPorEstado(estado);
        } else {
            publicaciones = publicacionService.mostrarTodasLasPublicaciones();
        }

        List<CategoriaPublicacion> categorias = categoriaPublicacionService.obtenerTodas();

        // Implementación simple de paginación
        int start = page * size;
        int end = Math.min(start + size, publicaciones.size());
        List<PublicacionResponseDTO> paginatedList = publicaciones.subList(start, end);
        int totalPaginas = (int) Math.ceil((double) publicaciones.size() / size);

        model.addAttribute("usuario", usuario);
        model.addAttribute("publicaciones", paginatedList);
        model.addAttribute("categorias", categorias);
        model.addAttribute("paginaActual", page);
        model.addAttribute("totalPaginas", totalPaginas);
        model.addAttribute("totalElementos", publicaciones.size());
        model.addAttribute("seccion", "listPublicacion");

        return "publicacion-layout";
    }

    // Mostrar formulario de creación
    @GetMapping("/{usuarioId}/crear")
    public String mostrarFormularioCreacion(@PathVariable UUID usuarioId, Model model) {
        Usuario usuario = usuarioService.obtenerUsuarioPorId(usuarioId);
        List<CategoriaPublicacion> categorias = categoriaPublicacionService.obtenerTodas();

        model.addAttribute("usuario", usuario);
        model.addAttribute("categorias", categorias);
        model.addAttribute("publicacionRequest", new PublicacionRequestDTO());
        model.addAttribute("estadosPublicacion", org.sena.inforecicla.model.enums.EstadoPublicacion.values());
        model.addAttribute("seccion", "createPublicacion");

        return "publicacion-layout";
    }

    // Crear publicación (POST)
    @PostMapping("/{usuarioId}/crear")
    public String crearPublicacion(@PathVariable UUID usuarioId,
                                   @Valid @ModelAttribute("publicacionRequest") PublicacionRequestDTO requestDTO,
                                   BindingResult result,
                                   Model model,
                                   RedirectAttributes redirectAttributes) {
        Usuario usuario = usuarioService.obtenerUsuarioPorId(usuarioId);

        if (result.hasErrors()) {
            List<CategoriaPublicacion> categorias = categoriaPublicacionService.obtenerTodas();
            model.addAttribute("usuario", usuario);
            model.addAttribute("categorias", categorias);
            model.addAttribute("estadosPublicacion", org.sena.inforecicla.model.enums.EstadoPublicacion.values());
            model.addAttribute("seccion", "createPublicacion");
            return "publicacion-layout";
        }

        requestDTO.setUsuarioId(usuarioId);
        publicacionService.crearPublicacion(requestDTO);

        redirectAttributes.addFlashAttribute("success", "Publicación creada exitosamente");
        return "redirect:/publicaciones/" + usuarioId + "/listar";
    }

    // Mostrar detalle de publicación
    @GetMapping("/{usuarioId}/mostrar/{publicacionId}")
    public String mostrarPublicacion(@PathVariable UUID usuarioId,
                                     @PathVariable UUID publicacionId,
                                     Model model) {
        Usuario usuario = usuarioService.obtenerUsuarioPorId(usuarioId);
        PublicacionResponseDTO publicacionDTO = publicacionService.mostrarPublicacionPorId(publicacionId);

        model.addAttribute("usuario", usuario);
        model.addAttribute("publicacion", publicacionDTO);
        model.addAttribute("seccion", "showPublicacion");

        return "publicacion-layout";
    }

    // Mostrar formulario de edición
    @GetMapping("/{usuarioId}/editar/{publicacionId}")
    public String mostrarFormularioEdicion(@PathVariable UUID usuarioId,
                                           @PathVariable UUID publicacionId,
                                           Model model) {
        Usuario usuario = usuarioService.obtenerUsuarioPorId(usuarioId);
        PublicacionResponseDTO publicacionDTO = publicacionService.mostrarPublicacionPorId(publicacionId);
        List<CategoriaPublicacion> categorias = categoriaPublicacionService.obtenerTodas();

        // Convertir a UpdateDTO para el formulario
        PublicacionUpdateDTO updateDTO = PublicacionUpdateDTO.builder()
                .titulo(publicacionDTO.getTitulo())
                .contenido(publicacionDTO.getContenido())
                .nombre(publicacionDTO.getNombre())
                .descripcion(publicacionDTO.getDescripcion())
                .estado(publicacionDTO.getEstado())
                .categoriaPublicacionId(publicacionDTO.getCategoriaPublicacionId())
                .build();

        model.addAttribute("usuario", usuario);
        model.addAttribute("publicacionId", publicacionId);
        model.addAttribute("publicacionUpdate", updateDTO);
        model.addAttribute("publicacion", publicacionDTO);
        model.addAttribute("categorias", categorias);
        model.addAttribute("estadosPublicacion", org.sena.inforecicla.model.enums.EstadoPublicacion.values());
        model.addAttribute("seccion", "editPublicacion");

        return "publicacion-layout";
    }

    // Actualizar publicación (POST)
    @PostMapping("/{usuarioId}/actualizar/{publicacionId}")
    public String actualizarPublicacion(@PathVariable UUID usuarioId,
                                        @PathVariable UUID publicacionId,
                                        @Valid @ModelAttribute("publicacionUpdate") PublicacionUpdateDTO updateDTO,
                                        BindingResult result,
                                        Model model,
                                        RedirectAttributes redirectAttributes) {
        Usuario usuario = usuarioService.obtenerUsuarioPorId(usuarioId);

        if (result.hasErrors()) {
            PublicacionResponseDTO publicacionDTO = publicacionService.mostrarPublicacionPorId(publicacionId);
            List<CategoriaPublicacion> categorias = categoriaPublicacionService.obtenerTodas();

            model.addAttribute("usuario", usuario);
            model.addAttribute("publicacionId", publicacionId);
            model.addAttribute("publicacion", publicacionDTO);
            model.addAttribute("categorias", categorias);
            model.addAttribute("estadosPublicacion", org.sena.inforecicla.model.enums.EstadoPublicacion.values());
            model.addAttribute("seccion", "editPublicacion");
            return "publicacion-layout";
        }

        publicacionService.actualizarPublicacion(publicacionId, updateDTO);

        redirectAttributes.addFlashAttribute("success", "Publicación actualizada exitosamente");
        return "redirect:/publicaciones/" + usuarioId + "/mostrar/" + publicacionId;
    }

    // Eliminar publicación
    @PostMapping("/{usuarioId}/eliminar/{publicacionId}")
    public String eliminarPublicacion(@PathVariable UUID usuarioId,
                                      @PathVariable UUID publicacionId,
                                      RedirectAttributes redirectAttributes) {
        publicacionService.eliminarPublicacion(publicacionId);

        redirectAttributes.addFlashAttribute("success", "Publicación eliminada exitosamente");
        return "redirect:/publicaciones/" + usuarioId + "/listar";
    }

    // Cambiar estado de publicación
    @PostMapping("/{usuarioId}/cambiar-estado/{publicacionId}")
    public String cambiarEstado(@PathVariable UUID usuarioId,
                                @PathVariable UUID publicacionId,
                                @RequestParam String estado,
                                RedirectAttributes redirectAttributes) {
        publicacionService.cambiarEstadoPublicacion(publicacionId, estado);

        redirectAttributes.addFlashAttribute("success", "Estado cambiado a " + estado);
        return "redirect:/publicaciones/" + usuarioId + "/mostrar/" + publicacionId;
    }

    // ============ API REST ============

    @PostMapping("/api/crear")
    @ResponseBody
    public ResponseEntity<PublicacionResponseDTO> crearPublicacionApi(
            @Valid @RequestBody PublicacionRequestDTO requestDTO) {
        PublicacionResponseDTO responseDTO = publicacionService.crearPublicacion(requestDTO);
        return new ResponseEntity<>(responseDTO, HttpStatus.CREATED);
    }

    @GetMapping("/api/{id}")
    @ResponseBody
    public ResponseEntity<PublicacionResponseDTO> obtenerPublicacionApi(@PathVariable UUID id) {
        PublicacionResponseDTO responseDTO = publicacionService.mostrarPublicacionPorId(id);
        return ResponseEntity.ok(responseDTO);
    }

    @GetMapping("/api")
    @ResponseBody
    public ResponseEntity<List<PublicacionResponseDTO>> obtenerTodasLasPublicacionesApi() {
        List<PublicacionResponseDTO> publicaciones = publicacionService.mostrarTodasLasPublicaciones();
        return ResponseEntity.ok(publicaciones);
    }

    @GetMapping("/api/usuario/{usuarioId}")
    @ResponseBody
    public ResponseEntity<List<PublicacionResponseDTO>> obtenerPublicacionesPorUsuarioApi(
            @PathVariable UUID usuarioId) {
        List<PublicacionResponseDTO> publicaciones = publicacionService.mostrarPublicacionesPorUsuario(usuarioId);
        return ResponseEntity.ok(publicaciones);
    }

    @GetMapping("/api/estado/{estado}")
    @ResponseBody
    public ResponseEntity<List<PublicacionResponseDTO>> obtenerPublicacionesPorEstadoApi(
            @PathVariable String estado) {
        List<PublicacionResponseDTO> publicaciones = publicacionService.mostrarPublicacionesPorEstado(estado);
        return ResponseEntity.ok(publicaciones);
    }

    @GetMapping("/api/buscar")
    @ResponseBody
    public ResponseEntity<List<PublicacionResponseDTO>> buscarPublicacionesApi(
            @RequestParam String keyword) {
        List<PublicacionResponseDTO> publicaciones = publicacionService.buscarPublicaciones(keyword);
        return ResponseEntity.ok(publicaciones);
    }

    @PutMapping("/api/{id}")
    @ResponseBody
    public ResponseEntity<PublicacionResponseDTO> actualizarPublicacionApi(
            @PathVariable UUID id,
            @Valid @RequestBody PublicacionUpdateDTO updateDTO) {
        PublicacionResponseDTO responseDTO = publicacionService.actualizarPublicacion(id, updateDTO);
        return ResponseEntity.ok(responseDTO);
    }

    @PatchMapping("/api/{id}/estado")
    @ResponseBody
    public ResponseEntity<Void> cambiarEstadoPublicacionApi(
            @PathVariable UUID id,
            @RequestParam String estado) {
        publicacionService.cambiarEstadoPublicacion(id, estado);
        return ResponseEntity.noContent().build();
    }

    @DeleteMapping("/api/{id}")
    @ResponseBody
    public ResponseEntity<Void> eliminarPublicacionApi(@PathVariable UUID id) {
        publicacionService.eliminarPublicacion(id);
        return ResponseEntity.noContent().build();
    }

    @GetMapping("/api/usuario/{usuarioId}/contar")
    @ResponseBody
    public ResponseEntity<Long> contarPublicacionesPorUsuarioApi(@PathVariable UUID usuarioId) {
        long cantidad = publicacionService.contarPublicacionesPorUsuario(usuarioId);
        return ResponseEntity.ok(cantidad);
    }
}