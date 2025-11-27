package org.sena.inforecicla.controller;

import lombok.RequiredArgsConstructor;
import org.sena.inforecicla.dto.usuario.UsuarioGestorRequestDTO;
import org.sena.inforecicla.dto.usuario.UsuarioGestorResponseDTO;
import org.sena.inforecicla.service.GestorEcaService;
import org.springframework.http.HttpStatus;
import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.*;

import java.util.UUID;

@RestController
@RequestMapping("/api/gestores-eca")
@RequiredArgsConstructor
public class GestorEcaController {

    private final GestorEcaService gestorEcaService;

    @PostMapping
    public ResponseEntity<UsuarioGestorResponseDTO> crearGestorConPuntoEca(
            @RequestBody UsuarioGestorRequestDTO gestorRequestDTO) {

        UsuarioGestorResponseDTO response = gestorEcaService.crearGestorConPuntoEca(gestorRequestDTO);

        return new ResponseEntity<>(response, HttpStatus.CREATED);
    }

    @GetMapping("/{usuarioId}")
    public ResponseEntity<UsuarioGestorResponseDTO> buscarGestorConPuntoEca(@PathVariable UUID usuarioId) {
        UsuarioGestorResponseDTO response = gestorEcaService.buscarGestorPuntoEca(usuarioId);
        return ResponseEntity.ok(response);
    }
}
