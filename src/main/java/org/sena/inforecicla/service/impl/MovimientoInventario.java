package org.sena.inforecicla.service.impl;

import org.sena.inforecicla.exception.InventarioNotFoundException;
import org.sena.inforecicla.model.Inventario;

public interface MovimientoInventario<T>{

    void actualizarStockInventario(T movimiento, Inventario inventario) throws InventarioNotFoundException;

}
