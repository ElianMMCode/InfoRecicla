package org.sena.inforecicla.service;

import org.springframework.data.domain.Page;

import java.util.UUID;

public interface VentaInventarioService {
    Page ventasDelPunto(UUID puntoEcaId, int page, int size);
}
