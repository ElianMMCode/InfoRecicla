package org.sena.inforecicla.controller;

import jakarta.validation.Valid;
import lombok.RequiredArgsConstructor;
import org.sena.inforecicla.dto.publicacion.PublicacionRequestDTO;
import org.sena.inforecicla.dto.publicacion.PublicacionResponseDTO;
import org.sena.inforecicla.dto.publicacion.PublicacionUpdateDTO;
import org.sena.inforecicla.service.PublicacionService;
import org.springframework.http.HttpStatus;
import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.*;

import java.util.List;
import java.util.UUID;

@RestController
@RequestMapping("/api/publicaciones")
@RequiredArgsConstructor
public class PublicacionController {

    private final PublicacionService publicacionService;

    @PostMapping
    public ResponseEntity<PublicacionResponseDTO> crearPublicacion(
            @Valid @RequestBody PublicacionRequestDTO requestDTO) {
        PublicacionResponseDTO responseDTO = publicacionService.crearPublicacion(requestDTO);
        return new ResponseEntity<>(responseDTO, HttpStatus.CREATED);
    }

    @GetMapping("/{id}")
    public ResponseEntity<PublicacionResponseDTO> obtenerPublicacion(@PathVariable UUID id) {
        PublicacionResponseDTO responseDTO = publicacionService.mostrarPublicacionPorId(id);
        return ResponseEntity.ok(responseDTO);
    }

    @GetMapping
    public ResponseEntity<List<PublicacionResponseDTO>> obtenerTodasLasPublicaciones() {
        List<PublicacionResponseDTO> publicaciones = publicacionService.mostrarTodasLasPublicaciones();
        return ResponseEntity.ok(publicaciones);
    }

    @GetMapping("/usuario/{usuarioId}")
    public ResponseEntity<List<PublicacionResponseDTO>> obtenerPublicacionesPorUsuario(
            @PathVariable UUID usuarioId) {
        List<PublicacionResponseDTO> publicaciones = publicacionService.mostrarPublicacionesPorUsuario(usuarioId);
        return ResponseEntity.ok(publicaciones);
    }

    @GetMapping("/estado/{estado}")
    public ResponseEntity<List<PublicacionResponseDTO>> obtenerPublicacionesPorEstado(
            @PathVariable String estado) {
        List<PublicacionResponseDTO> publicaciones = publicacionService.mostrarPublicacionesPorEstado(estado);
        return ResponseEntity.ok(publicaciones);
    }

    @GetMapping("/buscar")
    public ResponseEntity<List<PublicacionResponseDTO>> buscarPublicaciones(
            @RequestParam String keyword) {
        List<PublicacionResponseDTO> publicaciones = publicacionService.buscarPublicaciones(keyword);
        return ResponseEntity.ok(publicaciones);
    }

    @PutMapping("/{id}")
    public ResponseEntity<PublicacionResponseDTO> actualizarPublicacion(
            @PathVariable UUID id,
            @Valid @RequestBody PublicacionUpdateDTO updateDTO) {
        PublicacionResponseDTO responseDTO = publicacionService.actualizarPublicacion(id, updateDTO);
        return ResponseEntity.ok(responseDTO);
    }

    @PatchMapping("/{id}/estado")
    public ResponseEntity<Void> cambiarEstadoPublicacion(
            @PathVariable UUID id,
            @RequestParam String estado) {
        publicacionService.cambiarEstadoPublicacion(id, estado);
        return ResponseEntity.noContent().build();
    }

    @DeleteMapping("/{id}")
    public ResponseEntity<Void> eliminarPublicacion(@PathVariable UUID id) {
        publicacionService.eliminarPublicacion(id);
        return ResponseEntity.noContent().build();
    }

    @GetMapping("/usuario/{usuarioId}/contar")
    public ResponseEntity<Long> contarPublicacionesPorUsuario(@PathVariable UUID usuarioId) {
        long cantidad = publicacionService.contarPublicacionesPorUsuario(usuarioId);
        return ResponseEntity.ok(cantidad);
    }
}