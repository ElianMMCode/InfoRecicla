package org.sena.inforecicla.config;

import org.sena.inforecicla.model.Localidad;
// import org.sena.inforecicla.model.PuntoECA; // Comentado temporalmente
import org.sena.inforecicla.model.Usuario;
import org.sena.inforecicla.model.enums.Estado;
import org.sena.inforecicla.model.enums.TipoDocumento;
import org.sena.inforecicla.model.enums.TipoUsuario;
import org.sena.inforecicla.repository.LocalidadRepository;
// import org.sena.inforecicla.repository.PuntoEcaRepository; // Comentado temporalmente
import org.sena.inforecicla.repository.UsuarioRepository;
import org.springframework.boot.CommandLineRunner;
import org.springframework.context.annotation.Bean;
import org.springframework.context.annotation.Configuration;
import org.springframework.security.crypto.password.PasswordEncoder;
import org.springframework.transaction.annotation.Transactional;

import java.util.Optional;

@Configuration
public class DataInitializer {

    private final UsuarioRepository usuarioRepository;
    private final LocalidadRepository localidadRepository;
    // Comentado temporalmente debido a problemas de Lombok
    // private final PuntoEcaRepository puntoEcaRepository;
    private final PasswordEncoder passwordEncoder;

    public DataInitializer(UsuarioRepository usuarioRepository,
                          LocalidadRepository localidadRepository,
                          // Comentado temporalmente
                          // PuntoEcaRepository puntoEcaRepository,
                          PasswordEncoder passwordEncoder) {
        this.usuarioRepository = usuarioRepository;
        this.localidadRepository = localidadRepository;
        // this.puntoEcaRepository = puntoEcaRepository;
        this.passwordEncoder = passwordEncoder;
    }

    @Bean
    @Transactional
    public CommandLineRunner initializeAdminUser() {
        return args -> {
            try {
                System.out.println("🔍 Verificando usuario admin...");

                // Verificar si el usuario admin ya existe
                Optional<Usuario> adminExistente = usuarioRepository.findByEmail("admin@inforecicla.com");

                if (adminExistente.isPresent()) {
                    System.out.println("✅ Usuario admin ya existe.");
                    System.out.println("   Email: admin@inforecicla.com");
                    System.out.println("   Contraseña: Admin123@");
                    return;
                }

                System.out.println("🔨 Creando usuario admin...");

                // Obtener o crear localidad Chapinero
                Localidad localidad = localidadRepository.findByNombreIgnoreCase("Chapinero")
                        .orElseGet(() -> {
                            System.out.println("   Creando localidad Chapinero...");
                            Localidad newLocalidad = new Localidad();
                            // NO asignar ID manualmente, dejar que JPA lo genere
                            newLocalidad.setNombre("Chapinero");
                            newLocalidad.setEstado(Estado.Activo);
                            return localidadRepository.saveAndFlush(newLocalidad);
                        });

                // Crear usuario admin con contraseña encriptada por Spring
                Usuario admin = new Usuario();
                // NO asignar ID manualmente, dejar que JPA lo genere automáticamente
                admin.setNombres("Admin");
                admin.setApellidos("Sistema");
                admin.setEmail("admin@inforecicla.com");
                admin.setCelular("3001234567");
                // La contraseña se encripta aquí con el PasswordEncoder de Spring
                // Contraseña que cumple con los requisitos: mayúscula, minúscula, número, símbolo especial
                admin.setPassword(passwordEncoder.encode("Admin123@"));
                admin.setTipoUsuario(TipoUsuario.Admin);
                admin.setTipoDocumento(TipoDocumento.CC);
                admin.setNumeroDocumento("1000000000");
                admin.setFechaNacimiento("1990-01-01");
                admin.setBiografia("Usuario administrador del sistema");
                admin.setActivo(true);
                admin.setLocalidad(localidad);
                admin.setCiudad("Bogotá");
                admin.setLatitud(4.7110);
                admin.setLongitud(-74.0721);
                admin.setEstado(Estado.Activo);

                System.out.println("   Guardando usuario en BD...");
                Usuario savedAdmin = usuarioRepository.saveAndFlush(admin);

                System.out.println("✅ Usuario Admin creado exitosamente");
                System.out.println("📧 Email: admin@inforecicla.com");
                System.out.println("🔐 Contraseña: Admin123@");
                System.out.println("🆔 ID generado: " + savedAdmin.getUsuarioId());

                // NOTA: La creación del Punto ECA se hace por separado usando el archivo SQL fix-admin-user.sql
                // debido a problemas con Lombok que impiden la compilación de los setters/getters
                System.out.println("ℹ️ Para crear el Punto ECA asociado, ejecutar el archivo fix-admin-user.sql en la base de datos");

                /* COMENTADO TEMPORALMENTE - Problemas con Lombok
                // Verificar si ya existe un Punto ECA para el admin
                Optional<PuntoECA> puntoExistente = puntoEcaRepository.findByUsuario_UsuarioId(savedAdmin.getUsuarioId());

                if (puntoExistente.isEmpty()) {
                    // Crear un Punto ECA de prueba para el admin
                    System.out.println("🔨 Creando Punto ECA de prueba para admin...");

                    PuntoECA puntoEcaAdmin = new PuntoECA();
                    puntoEcaAdmin.setNombrePunto("Punto ECA Demo Admin");
                    puntoEcaAdmin.setDescripcion("Punto ECA de prueba para el administrador del sistema");
                    puntoEcaAdmin.setUsuario(savedAdmin);

                    // Campos heredados de EntidadContacto (obligatorios)
                    puntoEcaAdmin.setEmail("punto.eca.admin@inforecicla.com");
                    puntoEcaAdmin.setCelular("3009876543");

                    // Campos heredados de EntidadLocalizacion
                    puntoEcaAdmin.setLocalidad(localidad);
                    puntoEcaAdmin.setCiudad("Bogotá");
                    puntoEcaAdmin.setLatitud(4.7110);
                    puntoEcaAdmin.setLongitud(-74.0721);

                    // Campos específicos del PuntoECA
                    puntoEcaAdmin.setDireccion("Calle 63 #11-50, Chapinero, Bogotá");
                    puntoEcaAdmin.setTelefonoPunto("6012345678");
                    puntoEcaAdmin.setHorarioAtencion("Lunes a Viernes: 8:00 AM - 6:00 PM");
                    puntoEcaAdmin.setSitioWeb("https://inforecicla.com");
                    puntoEcaAdmin.setEstado(Estado.Activo);

                    PuntoECA savedPuntoECA = puntoEcaRepository.saveAndFlush(puntoEcaAdmin);

                    System.out.println("✅ Punto ECA Demo creado exitosamente");
                    System.out.println("🏪 Nombre: " + savedPuntoECA.getNombrePunto());
                    System.out.println("📍 Ubicación: " + savedPuntoECA.getDireccion());
                    System.out.println("🆔 Punto ECA ID: " + savedPuntoECA.getPuntoEcaID());
                } else {
                    System.out.println("✅ Punto ECA para admin ya existe, saltando creación");
                }
                */

            } catch (Exception e) {
                System.err.println("❌ Error al crear el usuario admin: " + e.getMessage());
                e.printStackTrace();
                // No relanzar la excepción para que la aplicación continúe
            }
        };
    }
}




