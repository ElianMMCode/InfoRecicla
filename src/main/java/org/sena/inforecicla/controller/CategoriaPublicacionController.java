package org.sena.inforecicla.controller;

import jakarta.validation.Valid;
import org.sena.inforecicla.dto.categoriaPublicacion.CategoriaPublicacionRequestDTO;
import org.sena.inforecicla.dto.categoriaPublicacion.CategoriaPublicacionResponseDTO;
import org.sena.inforecicla.dto.categoriaPublicacion.CategoriaPublicacionUpdateDTO;
import org.sena.inforecicla.service.CategoriaPublicacionService;
import lombok.AllArgsConstructor;
import org.sena.inforecicla.model.CategoriaPublicacion;
import org.sena.inforecicla.repository.CategoriaPublicacionRepository;
import org.sena.inforecicla.service.PublicacionService;
import org.sena.inforecicla.dto.publicacion.PublicacionResponseDTO;
import org.springframework.http.HttpStatus;
import org.springframework.http.ResponseEntity;
import org.springframework.stereotype.Controller;
import org.springframework.ui.Model;
import org.springframework.web.bind.annotation.*;
import org.springframework.validation.BindingResult;
import org.springframework.web.servlet.mvc.support.RedirectAttributes;

import java.util.*;
import java.util.stream.Collectors;
import java.util.UUID;

@Controller
@AllArgsConstructor
@RequestMapping("/categoria-publicacion")
public class CategoriaPublicacionController {

    private final CategoriaPublicacionService categoriaPublicacionService;
    private final CategoriaPublicacionRepository categoriaRepository;
    private final PublicacionService publicacionService;

    // ---------------------- VISTAS (MVC) ----------------------

    @GetMapping({"/index", "/"})
    public String index(@RequestParam(required = false, defaultValue = "index") String seccion, Model model) {
        List<CategoriaPublicacion> all = categoriaRepository.findAll();
        long totalCategorias = all.size();
        long categoriasActivas = categoriaRepository.countByEstado("ACTIVO");

        model.addAttribute("totalCategorias", totalCategorias);
        model.addAttribute("categoriasActivas", categoriasActivas);
        model.addAttribute("categoriasInactivas", totalCategorias - categoriasActivas);
        model.addAttribute("categoriasDestacadas", 0);
        model.addAttribute("categoriasRecientes", Math.min(5, all.size()));
        model.addAttribute("seccion", seccion);

        List<Map<String, Object>> recientes = all.stream().limit(5).map(this::mapCategoriaToView).collect(Collectors.toList());
        model.addAttribute("categorias", recientes);

        return "views/CategoriaPublicacion/categoriaPublicacion-layout";
    }

    @GetMapping("/list")
    public String list(@RequestParam(required = false, defaultValue = "1") int page,
                       @RequestParam(required = false, defaultValue = "10") int size,
                       Model model) {
        List<CategoriaPublicacion> all = categoriaRepository.findAll();
        List<Map<String, Object>> categorias = all.stream().map(this::mapCategoriaToView).collect(Collectors.toList());

        model.addAttribute("categorias", categorias);
        model.addAttribute("paginaActual", page);
        model.addAttribute("totalPaginas", 1);
        model.addAttribute("totalCategorias", categorias.size());
        model.addAttribute("seccion", "list");

        return "views/CategoriaPublicacion/categoriaPublicacion-layout";
    }

    @GetMapping("/show/{id}")
    public String show(@PathVariable UUID id, Model model) {
        CategoriaPublicacion cat = categoriaRepository.findById(id).orElse(null);
        if (cat == null) {
            model.addAttribute("categoria", null);
            model.addAttribute("publicaciones", Collections.emptyList());
            return "views/CategoriaPublicacion/categoriaPublicacion-layout";
        }

        Map<String, Object> categoriaView = mapCategoriaToView(cat);
        model.addAttribute("categoria", categoriaView);

        List<PublicacionResponseDTO> pubs = publicacionService.obtenerPublicacionesPorCategoria(id);
        List<Map<String, Object>> publicaciones = pubs.stream().map(dto -> {
            Map<String, Object> m = new HashMap<>();
            m.put("titulo", dto.titulo());
            m.put("resumen", dto.contenido() != null ? (dto.contenido().length() > 120 ? dto.contenido().substring(0, 120) + "..." : dto.contenido()) : "");
            m.put("createdAt", dto.fechaCreacion());
            m.put("estado", dto.estado());
            m.put("id", dto.publicacionId());
            return m;
        }).collect(Collectors.toList());

        model.addAttribute("publicaciones", publicaciones);
        model.addAttribute("totalPublicaciones", publicaciones.size());
        model.addAttribute("publicacionesActivas", publicaciones.stream().filter(p -> "PUBLICADO".equals(p.get("estado"))).count());

        return "views/CategoriaPublicacion/categoriaPublicacion-layout";
    }

    @GetMapping("/create")
    public String create(Model model) {
        model.addAttribute("seccion", "create");
        model.addAttribute("categoriaRequestDTO", new CategoriaPublicacionRequestDTO());
        return "views/CategoriaPublicacion/categoriaPublicacion-layout";
    }

    @GetMapping("/edit/{id}")
    public String edit(@PathVariable UUID id, Model model) {
        CategoriaPublicacion cat = categoriaRepository.findById(id).orElse(null);
        model.addAttribute("seccion", "edit");
        model.addAttribute("categoria", cat != null ? mapCategoriaToView(cat) : null);

        // preparar DTO para el formulario
        if (cat != null) {
            CategoriaPublicacionUpdateDTO dto = new CategoriaPublicacionUpdateDTO();
            dto.setNombre(cat.getNombre());
            dto.setDescripcion(cat.getDescripcion());
            model.addAttribute("categoriaRequestDTO", dto);
        } else {
            model.addAttribute("categoriaRequestDTO", new CategoriaPublicacionUpdateDTO());
        }

        return "views/CategoriaPublicacion/categoriaPublicacion-layout";
    }

    // POST - almacenar nueva categoría (form)
    @PostMapping("/store")
    public String store(@Valid CategoriaPublicacionRequestDTO requestDTO, BindingResult bindingResult, Model model, RedirectAttributes redirectAttributes) {
        if (bindingResult.hasErrors()) {
            model.addAttribute("seccion", "create");
            model.addAttribute("categoriaRequestDTO", requestDTO);
            return "views/CategoriaPublicacion/categoriaPublicacion-layout";
        }

        categoriaPublicacionService.crearCategoria(requestDTO);
        // intentar recuperar entidad para redirigir a su detalle
        var opt = categoriaRepository.findByNombre(requestDTO.getNombre());
        if (opt.isPresent()) {
            redirectAttributes.addFlashAttribute("success", "Categoría creada correctamente");
            return "redirect:/categoria-publicacion/show/" + opt.get().getCategoriaPublicacionId();
        }

        redirectAttributes.addFlashAttribute("success", "Categoría creada");
        return "redirect:/categoria-publicacion/list";
    }

    // POST - actualizar categoría (form)
    @PostMapping("/update/{id}")
    public String update(@PathVariable UUID id, @Valid CategoriaPublicacionUpdateDTO updateDTO, BindingResult bindingResult, Model model, RedirectAttributes redirectAttributes) {
        if (bindingResult.hasErrors()) {
            model.addAttribute("seccion", "edit");
            model.addAttribute("categoriaRequestDTO", updateDTO);
            model.addAttribute("categoria", mapCategoriaToView(categoriaRepository.findById(id).orElse(null)));
            return "views/CategoriaPublicacion/categoriaPublicacion-layout";
        }

        categoriaPublicacionService.actualizarCategoria(id, updateDTO);
        redirectAttributes.addFlashAttribute("success", "Categoría actualizada");
        return "redirect:/categoria-publicacion/show/" + id;
    }

    // POST - eliminar categoría (form)
    @PostMapping("/delete/{id}")
    public String delete(@PathVariable UUID id, RedirectAttributes redirectAttributes) {
        categoriaPublicacionService.eliminarCategoria(id);
        redirectAttributes.addFlashAttribute("success", "Categoría eliminada");
        return "redirect:/categoria-publicacion/list";
    }

    private Map<String, Object> mapCategoriaToView(CategoriaPublicacion cat) {
        Map<String, Object> m = new HashMap<>();
        m.put("id", cat.getCategoriaPublicacionId());
        m.put("nombre", cat.getNombre());
        m.put("descripcion", cat.getDescripcion());
        m.put("estadoCategoria", cat.getEstado() != null ? cat.getEstado().name() : "ACTIVO");
        m.put("createdAt", cat.getFechaCreacion());
        m.put("updatedAt", cat.getFechaActualizacion());
        return m;
    }

    // ---------------------- APIS REST (se mantienen) ----------------------

    @PostMapping(path = "/api/categorias-publicacion")
    @ResponseBody
    public ResponseEntity<CategoriaPublicacionResponseDTO> crearCategoriaApi(
            @Valid @RequestBody CategoriaPublicacionRequestDTO requestDTO) {
        CategoriaPublicacionResponseDTO responseDTO = categoriaPublicacionService.crearCategoria(requestDTO);
        return new ResponseEntity<>(responseDTO, HttpStatus.CREATED);
    }

    @GetMapping(path = "/api/categorias-publicacion/{id}")
    @ResponseBody
    public ResponseEntity<CategoriaPublicacionResponseDTO> obtenerCategoriaApi(@PathVariable UUID id) {
        CategoriaPublicacionResponseDTO responseDTO = categoriaPublicacionService.obtenerCategoriaPorId(id);
        return ResponseEntity.ok(responseDTO);
    }

    @GetMapping(path = "/api/categorias-publicacion")
    @ResponseBody
    public ResponseEntity<List<CategoriaPublicacionResponseDTO>> obtenerTodasLasCategoriasApi() {
        List<CategoriaPublicacionResponseDTO> categorias = categoriaPublicacionService.obtenerTodasLasCategorias();
        return ResponseEntity.ok(categorias);
    }

    @GetMapping(path = "/api/categorias-publicacion/activas")
    @ResponseBody
    public ResponseEntity<List<CategoriaPublicacionResponseDTO>> obtenerCategoriasActivasApi() {
        List<CategoriaPublicacionResponseDTO> categorias = categoriaPublicacionService.obtenerCategoriasActivas();
        return ResponseEntity.ok(categorias);
    }

    /* COMENTADO: Endpoint que utiliza orden - no se usa actualmente
    @GetMapping(path = "/api/categorias-publicacion/ordenadas")
    @ResponseBody
    public ResponseEntity<List<CategoriaPublicacionResponseDTO>> obtenerCategoriasOrdenadasApi() {
        List<CategoriaPublicacionResponseDTO> categorias = categoriaPublicacionService.obtenerCategoriasOrdenadas();
        return ResponseEntity.ok(categorias);
    }
    */

    @GetMapping(path = "/api/categorias-publicacion/buscar")
    @ResponseBody
    public ResponseEntity<List<CategoriaPublicacionResponseDTO>> buscarCategoriasApi(
            @RequestParam String keyword) {
        List<CategoriaPublicacionResponseDTO> categorias = categoriaPublicacionService.buscarCategorias(keyword);
        return ResponseEntity.ok(categorias);
    }

    @PutMapping(path = "/api/categorias-publicacion/{id}")
    @ResponseBody
    public ResponseEntity<CategoriaPublicacionResponseDTO> actualizarCategoriaApi(
            @PathVariable UUID id,
            @Valid @RequestBody CategoriaPublicacionUpdateDTO updateDTO) {
        CategoriaPublicacionResponseDTO responseDTO = categoriaPublicacionService.actualizarCategoria(id, updateDTO);
        return ResponseEntity.ok(responseDTO);
    }

    @PatchMapping(path = "/api/categorias-publicacion/{id}/estado")
    @ResponseBody
    public ResponseEntity<Void> cambiarEstadoCategoriaApi(
            @PathVariable UUID id,
            @RequestParam String estado) {
        categoriaPublicacionService.cambiarEstadoCategoria(id, estado);
        return ResponseEntity.noContent().build();
    }

    @DeleteMapping(path = "/api/categorias-publicacion/{id}")
    @ResponseBody
    public ResponseEntity<Void> eliminarCategoriaApi(@PathVariable UUID id) {
        categoriaPublicacionService.eliminarCategoria(id);
        return ResponseEntity.noContent().build();
    }

    @GetMapping(path = "/api/categorias-publicacion/contar/activas")
    @ResponseBody
    public ResponseEntity<Long> contarCategoriasActivasApi() {
        long cantidad = categoriaPublicacionService.contarCategoriasActivas();
        return ResponseEntity.ok(cantidad);
    }
}
