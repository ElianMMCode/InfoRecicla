package org.sena.inforecicla.service;

import org.sena.inforecicla.dto.puntoEca.inventario.InventarioGuardarDTO;
import org.sena.inforecicla.dto.puntoEca.inventario.InventarioResponseDTO;
import org.sena.inforecicla.dto.puntoEca.inventario.InventarioUpdateDTO;
import org.sena.inforecicla.dto.puntoEca.materiales.CategoriaMaterialesInvResponseDTO;
import org.sena.inforecicla.dto.puntoEca.materiales.MaterialInvResponseDTO;
import org.sena.inforecicla.dto.puntoEca.materiales.TipoMaterialesInvResponseDTO;
import org.sena.inforecicla.exception.InventarioFoundExistException;
import org.sena.inforecicla.exception.InventarioNotFoundException;
import org.sena.inforecicla.exception.MaterialNotFoundException;
import org.sena.inforecicla.exception.PuntoEcaNotFoundException;

import java.util.List;
import java.util.UUID;

public interface InventarioService {

    List<InventarioResponseDTO> mostrarInventarioPuntoEca(UUID puntoId);

    InventarioResponseDTO actualizarInventario(UUID inventarioId, InventarioUpdateDTO invUpdate) throws InventarioNotFoundException;

    List<TipoMaterialesInvResponseDTO> listarTiposMateriales();

    List<CategoriaMaterialesInvResponseDTO> listarCategoriasMateriales();

    List<MaterialInvResponseDTO> buscarMaterial(UUID inventarioId, String texto, String categoria, String tipo) throws InventarioFoundExistException;

    void guardarInventario(InventarioGuardarDTO dto) throws MaterialNotFoundException, PuntoEcaNotFoundException;
}
