package org.sena.inforecicla.service;

import org.sena.inforecicla.dto.puntoEca.inventario.InventarioRequestDTO;
import org.sena.inforecicla.dto.puntoEca.inventario.InventarioResponseDTO;
import org.sena.inforecicla.dto.puntoEca.inventario.InventarioUpdateDTO;
import org.sena.inforecicla.exception.InventarioNotFoundException;
import org.sena.inforecicla.exception.MaterialNotFoundException;
import org.sena.inforecicla.exception.PuntoEcaNotFoundException;
import org.sena.inforecicla.model.enums.Alerta;

import java.util.List;
import java.util.UUID;

public interface InventarioService {

    List<InventarioResponseDTO> mostrarInventarioPuntoEca(UUID puntoId);

    InventarioResponseDTO actualizarInventario(UUID inventarioId, InventarioUpdateDTO invUpdate) throws InventarioNotFoundException;

    void guardarInventario(InventarioRequestDTO dto) throws PuntoEcaNotFoundException;

    List<InventarioResponseDTO> filtraInventario(UUID gestorId, String texto, String categoria, String tipo, Alerta alerta, String unidad, String ocupacion);

    void eliminarInventario(UUID inventarioId) throws InventarioNotFoundException;
}
