package org.sena.inforecicla.repository;

import org.sena.inforecicla.model.CategoriaPublicacion;
import org.sena.inforecicla.model.Etiquetas;

import java.util.List;
import java.util.Optional;
import java.util.UUID;

public interface EtiquetasRepository extends BaseRepository <Etiquetas, UUID>{

    // Buscar etiqueta por nombre (útil para autocompletado o validación)
    Optional<Etiquetas> findByNombre(String nombre);
    // Buscar por nombre parcial
    List<Etiquetas> findByNombreContainingIgnoreCase(String nombre);
}
