package org.sena.inforecicla.dto.puntoEca.materiales;

import org.sena.inforecicla.model.CategoriaMaterial;
import org.sena.inforecicla.model.TipoMaterial;

import java.util.UUID;

public class MaterialesInvResponseDTO {

    //Material
    UUID materialId;
    String nmbMaterial;
    String dscMaterial;
    String imagenUrl;

    //Categoria Material
    CategoriaMaterial ctgMaterial;
    String nmbCategoria;
    String dscCategoria;

    //Tipo Material
    TipoMaterial tpMaterial;
    String nmbTipo;
    String dscTipo;

}
