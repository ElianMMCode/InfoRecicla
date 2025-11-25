package org.sena.inforecicla.config;

import org.springframework.context.annotation.Bean;
import org.springframework.context.annotation.Configuration;
import org.springframework.security.config.annotation.web.builders.HttpSecurity;
import org.springframework.security.config.annotation.web.configuration.EnableWebSecurity;
import org.springframework.security.config.annotation.web.configurers.AbstractHttpConfigurer;
import org.springframework.security.crypto.bcrypt.BCryptPasswordEncoder;
import org.springframework.security.crypto.password.PasswordEncoder;
import org.springframework.security.web.SecurityFilterChain;

@Configuration
@EnableWebSecurity
public class SecurityConfig {

    @Bean
    public PasswordEncoder passwordEncoder() {
        return new BCryptPasswordEncoder();
    }
    @Bean

    public SecurityFilterChain securityFilterChain(HttpSecurity http) throws Exception {

        http
                // Desactiva CSRF por ahora (para formularios simples / pruebas)
                .csrf(AbstractHttpConfigurer::disable)

                // Autoriza TODO sin autenticación
                .authorizeHttpRequests(auth -> auth
                        .anyRequest().permitAll()
                )

                // Desactiva el formulario de login
                .formLogin(AbstractHttpConfigurer::disable)

                // Desactiva auth básica (header Authorization)
                .httpBasic(AbstractHttpConfigurer::disable);

        return http.build();
    }
}