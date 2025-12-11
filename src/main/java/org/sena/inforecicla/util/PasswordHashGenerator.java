package org.sena.inforecicla.util;

import org.springframework.security.crypto.bcrypt.BCryptPasswordEncoder;
import org.springframework.security.crypto.password.PasswordEncoder;

/**
 * Utilidad para generar hashes BCrypt para usuarios admin
 * Úsala en herramientas online o en un main temporal
 */
public class PasswordHashGenerator {

    public static void main(String[] args) {
        PasswordEncoder encoder = new BCryptPasswordEncoder();

        // Contraseña actual
        String currentPassword = "Admin@123456";
        String currentHash = encoder.encode(currentPassword);

        System.out.println("═════════════════════════════════════════════");
        System.out.println("GENERADOR DE HASH BCRYPT");
        System.out.println("═════════════════════════════════════════════");
        System.out.println("Contraseña: " + currentPassword);
        System.out.println("Hash BCrypt: " + currentHash);
        System.out.println("═════════════════════════════════════════════");

        // Ejemplos de otras contraseñas comunes
        System.out.println("\n📝 EJEMPLOS DE OTRAS CONTRASEÑAS:");
        System.out.println("───────────────────────────────────────────");

        String[] passwords = {
            "NewPassword@123",
            "SecurePass@2024",
            "AdminPass@456"
        };

        for (String password : passwords) {
            String hash = encoder.encode(password);
            System.out.println("Contraseña: " + password);
            System.out.println("Hash: " + hash);
            System.out.println("Verificación: " + encoder.matches(password, hash));
            System.out.println();
        }
    }
}

