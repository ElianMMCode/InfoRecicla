package org.sena.inforecicla.service;

import org.sena.inforecicla.dto.puntoEca.materiales.CategoriaMaterialesInvResponseDTO;
import org.sena.inforecicla.dto.puntoEca.materiales.MaterialInvResponseDTO;
import org.sena.inforecicla.dto.puntoEca.materiales.MaterialResponseDTO;
import org.sena.inforecicla.dto.puntoEca.materiales.TipoMaterialesInvResponseDTO;
import org.sena.inforecicla.exception.InventarioFoundExistException;

import java.util.List;
import java.util.Map;
import java.util.UUID;

public interface InventarioDetalleService {

//    Map<String, List<Object>> listaDetallesMaterialesInventario(UUID puntoId);

    List<CategoriaMaterialesInvResponseDTO> obtenerCategoriasDelPunto(UUID puntoId);

    List<TipoMaterialesInvResponseDTO> obtenerTiposDelPunto(UUID puntoId);

    List<MaterialResponseDTO> buscarMaterialNuevoFiltrandoInventario(UUID puntoId, String texto, String categoria, String tipo) throws InventarioFoundExistException;
    List<MaterialInvResponseDTO> buscarMaterialExistentesFiltrandoInventario(UUID puntoId, String texto, String categoria, String tipo) throws InventarioFoundExistException;
}
