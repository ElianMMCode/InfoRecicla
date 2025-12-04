package org.sena.inforecicla.service.impl;

import lombok.RequiredArgsConstructor;
import org.sena.inforecicla.model.Localidad;
import org.sena.inforecicla.repository.LocalidadRepository;
import org.sena.inforecicla.service.LocalidadService;
import org.springframework.stereotype.Service;

import java.util.Comparator;
import java.util.List;

@Service
@RequiredArgsConstructor
public class LocalidadServiceImpl implements LocalidadService {

    private final LocalidadRepository localidadRepository;

    @Override
    public List<Localidad> listadoLocalidades() {
        return localidadRepository.findAllActivos()
                .stream()
                .sorted(Comparator.comparing(Localidad::getNombre))
                .toList();
    }
}
