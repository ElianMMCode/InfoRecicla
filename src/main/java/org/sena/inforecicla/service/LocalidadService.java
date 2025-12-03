package org.sena.inforecicla.service;

import org.sena.inforecicla.model.Localidad;

import java.util.List;
import java.util.Optional;
import java.util.UUID;

public interface LocalidadService {

    List<Localidad> listadoLocalidades();

    Optional<Localidad> buscarPorNombre(String nombre);

    Optional<Localidad> buscarPorId(UUID id);
}
