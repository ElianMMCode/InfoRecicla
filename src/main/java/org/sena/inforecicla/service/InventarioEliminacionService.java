package org.sena.inforecicla.service;

import org.sena.inforecicla.exception.InventarioNotFoundException;

import java.util.UUID;

public interface InventarioEliminacionService {

    void eliminarInventarioConMovimientos(UUID inventarioId, UUID puntoId) throws InventarioNotFoundException;
}

