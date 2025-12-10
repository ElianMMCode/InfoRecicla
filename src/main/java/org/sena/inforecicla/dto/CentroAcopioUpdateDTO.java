package org.sena.inforecicla.dto;

import lombok.AllArgsConstructor;
import lombok.Data;
import lombok.NoArgsConstructor;

/**
 * DTO para actualizar un Centro de Acopio
 */
@Data
@NoArgsConstructor
@AllArgsConstructor
public class CentroAcopioUpdateDTO {
    private String nombreCntAcp;
    private String tipoCntAcp;
    private String celular;
    private String email;
    private String nombreContactoCntAcp;
    private String nota;
}

