package org.sena.inforecicla.service.impl;

import lombok.RequiredArgsConstructor;
import org.sena.inforecicla.exception.InventarioNotFoundException;
import org.sena.inforecicla.model.Inventario;
import org.sena.inforecicla.model.enums.Estado;
import org.sena.inforecicla.repository.InventarioRepository;
import org.sena.inforecicla.service.CompraInventarioService;
import org.sena.inforecicla.service.InventarioEliminacionService;
import org.sena.inforecicla.service.VentaInventarioService;
import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;

import java.util.UUID;

@Service
@RequiredArgsConstructor
public class InventarioEliminacionServiceImpl implements InventarioEliminacionService {

    private final InventarioRepository inventarioRepository;
    private final CompraInventarioService compraInventarioService;
    private final VentaInventarioService ventaInventarioService;

    @Override
    @Transactional
    public void eliminarInventarioConMovimientos(UUID inventarioId, UUID puntoId) throws InventarioNotFoundException {

        Inventario inventario = inventarioRepository.findById(inventarioId)
                .orElseThrow(() -> new InventarioNotFoundException("Inventario no encontrado con ID: " + inventarioId));

        inventario.setEstado(Estado.Inactivo);

        // Marca compras asociadas como inactivas
        compraInventarioService.eliminarComprasAsociadasInventario(puntoId, inventarioId);

        // Marca ventas asociadas como inactivas
        ventaInventarioService.eliminarVentasAsociadasInventario(puntoId, inventarioId);

        inventarioRepository.save(inventario);
    }
}

