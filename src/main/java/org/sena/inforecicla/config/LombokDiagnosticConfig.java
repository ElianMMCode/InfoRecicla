package org.sena.inforecicla.config;

import org.springframework.context.annotation.Configuration;

/**
 * Configuración temporal mientras se resuelven los problemas de Lombok
 * Esta clase ayuda a diagnosticar el problema y proporciona información de configuración
 */
@Configuration
public class LombokDiagnosticConfig {

    // Esta clase existe solo para verificar que Spring Boot funcione correctamente
    // y para proporcionar un punto de diagnóstico

    static {
        System.out.println("🔧 LombokDiagnosticConfig cargado");
        System.out.println("📋 Java Version: " + System.getProperty("java.version"));
        System.out.println("📋 Lombok debería estar procesando anotaciones...");
    }
}
