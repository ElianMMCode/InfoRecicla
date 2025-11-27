package org.sena.inforecicla.dto.usuario;

import org.sena.inforecicla.model.enums.TipoDocumento;
import org.sena.inforecicla.model.enums.TipoUsuario;

/**
 * Ejemplo de uso del UsuarioGestorRequestDTO
 * Este ejemplo muestra cómo crear un objeto DTO para registrar un gestor ECA con su punto ECA
 */
public class UsuarioGestorRequestDTOExample {

    public static UsuarioGestorRequestDTO crearEjemploCompleto() {
        return new UsuarioGestorRequestDTO(
                // Datos del Usuario
                "Juan Carlos",
                "Pérez González",
                "MiPassword123@",
                TipoUsuario.GestorECA,
                TipoDocumento.CC,
                "1234567890",
                "1990-05-15",
                "3001234567",
                "juan.perez@example.com",
                "Bogotá",
                "USAQUEN",
                "https://example.com/foto-perfil.jpg",
                "Gestor experimentado en manejo de residuos y educación ambiental",

                // Datos del Punto ECA
                "EcoPunto Usaquén",
                "Centro de acopio especializado en residuos electrónicos y materiales reciclables",
                "6017891234",
                "3109876543",
                "ecopunto.usaquen@example.com",
                "Calle 127 # 15-30",
                "Bogotá",
                "USAQUEN",
                "4.678123,-74.045678",
                "https://example.com/logo-punto.jpg",
                "https://example.com/foto-punto.jpg",
                "https://ecopunto-usaquen.com",
                "Lunes a Viernes: 8:00 AM - 6:00 PM, Sábados: 9:00 AM - 4:00 PM"
        );
    }

    public static UsuarioGestorRequestDTO crearEjemploMinimo() {
        return new UsuarioGestorRequestDTO(
                // Datos del Usuario (campos obligatorios)
                "María",
                "López",
                "Password456@",
                TipoUsuario.GestorECA,
                TipoDocumento.CC,
                "9876543210",
                "1985-10-20",
                "3002345678",
                "maria.lopez@example.com",
                "Bogotá",
                "CHAPINERO",
                null, // fotoPerfil opcional
                null, // biografia opcional

                // Datos del Punto ECA (campos obligatorios)
                "EcoPunto Chapinero",
                null, // descripcionPunto opcional
                null, // telefonoPunto opcional
                "3003456789",
                "ecopunto.chapinero@example.com",
                null, // direccionPunto opcional
                "Bogotá",
                "CHAPINERO",
                null, // coordenadasPunto opcional
                null, // logoUrlPunto opcional
                null, // fotoUrlPunto opcional
                null, // sitioWebPunto opcional
                null  // horarioAtencionPunto opcional
        );
    }
}
