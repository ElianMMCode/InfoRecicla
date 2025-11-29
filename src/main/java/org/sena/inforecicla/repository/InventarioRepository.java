package org.sena.inforecicla.repository;

import org.sena.inforecicla.dto.puntoEca.InventarioResponseDTO;
import org.sena.inforecicla.model.Inventario;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.data.jpa.repository.Query;
import org.springframework.data.repository.query.Param;

import java.util.List;
import java.util.UUID;

public interface InventarioRepository extends JpaRepository<Inventario, UUID> {

    //Busqueda por Material en los inventarios
    @Query("select i from Inventario i " +
            "where i.puntoEca.puntoEcaID = :puntoEcaId " +
            "and i.puntoEca.gestorId = :gestorId and i.material = :materialId"
                )
    List<Inventario> buscarPorMaterial(@Param("puntoEcaId") UUID puntoEcaId,
                                       @Param("gestorId") UUID gestorId,
                                       @Param("materialId") UUID materialId);

    //Búsqueda por Punto Eca
    List<Inventario> findAllByPuntoEca_PuntoEcaID(UUID puntoEcaId);
//    List<Inventario> filtrarPorUmbral(UUID puntoEcaId, UUID usuarioId, Short umbral);
}
