package org.sena.inforecicla.dto.publicacion;

import lombok.Builder;

import java.util.UUID;

@Builder
public record EtiquetaResponseDTO(
        UUID id,
        String nombre,
        String color
) {}