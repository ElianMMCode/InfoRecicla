package org.sena.inforecicla.util;

import org.springframework.security.crypto.bcrypt.BCryptPasswordEncoder;

/**
 * Utilidad para generar contraseñas encriptadas en BCrypt
 * Úsalo para crear contraseñas de prueba en la base de datos
 */
public class PasswordEncoderUtil {

    public static void main(String[] args) {
        BCryptPasswordEncoder encoder = new BCryptPasswordEncoder();

        // Ejemplo: generar hash para contraseña "TestPass123!"
        String password = "TestPass123!";
        String encodedPassword = encoder.encode(password);

        System.out.println("Contraseña original: " + password);
        System.out.println("Contraseña encriptada: " + encodedPassword);

        // Verificar que funciona
        boolean matches = encoder.matches(password, encodedPassword);
        System.out.println("¿Contraseña válida?: " + matches);

        // Ejemplos adicionales
        System.out.println("\n--- Más ejemplos ---");
        String[] passwords = {
                "Admin@2024",
                "Usuario123!",
                "Punto.Eca456"
        };

        for (String pass : passwords) {
            System.out.println(pass + " -> " + encoder.encode(pass));
        }
    }
}

