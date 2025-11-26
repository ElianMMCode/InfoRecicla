package org.sena.inforecicla.repository;

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

    //Busqueda por Punto Eca
    @Query("SELECT i FROM Inventario i " +
            "WHERE i.puntoEca.puntoEcaID = :puntoEcaId " +
            "AND i.puntoEca.gestorId = :usuarioId")
    List<Inventario> buscarPorPuntoEca(@Param("puntoEcaId") UUID puntoEcaId,
                                       @Param("usuarioId") UUID usuarioId);


//    List<Inventario> filtrarPorUmbral(UUID puntoEcaId, UUID usuarioId, Short umbral);


}
