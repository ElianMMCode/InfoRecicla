package org.sena.inforecicla.config;

import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.context.annotation.Bean;
import org.springframework.context.annotation.Configuration;
import org.springframework.security.authentication.AuthenticationManager;
import org.springframework.security.config.annotation.authentication.configuration.AuthenticationConfiguration;
import org.springframework.security.config.annotation.web.builders.HttpSecurity;
import org.springframework.security.config.annotation.web.configurers.AbstractHttpConfigurer;
import org.springframework.security.config.http.SessionCreationPolicy;
import org.springframework.security.core.Authentication;
import org.springframework.security.core.GrantedAuthority;
import org.springframework.security.crypto.bcrypt.BCryptPasswordEncoder;
import org.springframework.security.crypto.password.PasswordEncoder;
import org.springframework.security.web.SecurityFilterChain;
import org.springframework.security.web.authentication.AuthenticationSuccessHandler;
import org.springframework.security.web.header.writers.StaticHeadersWriter;
import org.springframework.web.servlet.config.annotation.CorsRegistry;
import org.springframework.web.servlet.config.annotation.WebMvcConfigurer;
import org.sena.inforecicla.service.impl.AuthenticationServiceImpl;
import jakarta.servlet.http.HttpServletRequest;
import jakarta.servlet.http.HttpServletResponse;
import java.io.IOException;
import java.util.Collection;

@Configuration
public class SecurityConfig {

    @Autowired
    private AuthenticationServiceImpl authenticationService;

    private static class CustomAuthSuccessHandler implements AuthenticationSuccessHandler {
        @Override
        public void onAuthenticationSuccess(HttpServletRequest request,
                                            HttpServletResponse response,
                                            Authentication authentication) throws IOException {
            Collection<? extends GrantedAuthority> authorities = authentication.getAuthorities();
            String targetUrl = "/";

            boolean isGestorEca = authorities.stream()
                    .anyMatch(a -> a.getAuthority().equals("ROLE_GestorECA"));

            if (isGestorEca) {
                targetUrl = "/punto-eca";
            }

            response.sendRedirect(request.getContextPath() + targetUrl);
        }
    }

    @Bean
    public SecurityFilterChain securityFilterChain(HttpSecurity http) throws Exception {
        http
                .csrf(AbstractHttpConfigurer::disable)
                .authorizeHttpRequests(auth -> auth
                        .requestMatchers("/", "/inicio", "/publicaciones", "/mapa").permitAll()
                        .requestMatchers("/mapa/api/**").permitAll()  // ✅ Permitir todos los endpoints de mapa públicamente
                        .requestMatchers("/login", "/registro/**").permitAll()
                        .requestMatchers("/test-registros").permitAll()
                        .requestMatchers("/css/**", "/js/**", "/imagenes/**", "/static/**").permitAll()
                        .requestMatchers("/webjars/**").permitAll()
                        .requestMatchers("/resources/**").permitAll()
                        .requestMatchers("/error/**", "/error").permitAll()
                        .requestMatchers("/favicon.ico").permitAll()
                        .requestMatchers("/.well-known/**").permitAll()
                        .requestMatchers("/actuator/**").permitAll()
                        .requestMatchers("/diagnostico", "/test-public").permitAll()
                        .requestMatchers("/usuarios/**").hasRole("ADMIN")
                        .requestMatchers("/configuracion/**").hasRole("ADMIN")
                        .requestMatchers("/puntos-eca/**", "/punto-eca/**").hasAnyRole("ADMIN", "GestorECA")
                        .requestMatchers("/api/public/**").permitAll()
                        .requestMatchers("/api/admin/**").hasRole("ADMIN")
                        .requestMatchers("/api/gestor/**").hasAnyRole("ADMIN", "GestorECA")
                        .requestMatchers("/api/user/**").authenticated()
                        .requestMatchers("/dashboard/**", "/dashboard", "/perfil/**").authenticated()
                        .requestMatchers("/notificaciones/**").authenticated()
                        .requestMatchers("/test-auth", "/test-dashboard").authenticated()
                        .anyRequest().authenticated()
                )
                .formLogin(form -> form
                        .loginPage("/login")
                        .loginProcessingUrl("/login")
                        .usernameParameter("email")
                        .passwordParameter("password")
                        .successHandler(new CustomAuthSuccessHandler())
                        .failureUrl("/login?error=true")
                        .permitAll()
                )
                .logout(logout -> logout
                        .logoutUrl("/logout")
                        .logoutSuccessUrl("/")
                        .invalidateHttpSession(true)
                        .clearAuthentication(true)
                        .permitAll()
                )
                .sessionManagement(session -> session
                        .sessionCreationPolicy(SessionCreationPolicy.IF_REQUIRED)
                        .maximumSessions(1)
                        .maxSessionsPreventsLogin(false)
                )
                .headers(headers -> headers
                        .frameOptions(frameOptionsConfig -> frameOptionsConfig.deny())
                        .addHeaderWriter(new StaticHeadersWriter("X-Content-Type-Options", "nosniff"))
                        .addHeaderWriter(new StaticHeadersWriter("X-XSS-Protection", "1; mode=block"))
                )
                .httpBasic(AbstractHttpConfigurer::disable)
                .authenticationProvider(authenticationProvider());

        return http.build();
    }

    @Bean
    public org.springframework.security.authentication.AuthenticationProvider authenticationProvider() {
        org.springframework.security.authentication.dao.DaoAuthenticationProvider authProvider =
            new org.springframework.security.authentication.dao.DaoAuthenticationProvider();
        authProvider.setUserDetailsService(authenticationService);
        authProvider.setPasswordEncoder(passwordEncoder());
        return authProvider;
    }

    @Bean
    public PasswordEncoder passwordEncoder() {
        return new BCryptPasswordEncoder();
    }

    @Bean
    public AuthenticationManager authenticationManager(AuthenticationConfiguration authConfig) throws Exception {
        return authConfig.getAuthenticationManager();
    }

    @Bean
    public WebMvcConfigurer corsConfigurer() {
        return new WebMvcConfigurer() {
            @Override
            public void addCorsMappings(CorsRegistry registry) {
                registry.addMapping("/**")
                        .allowedOriginPatterns("http://localhost:*", "http://127.0.0.1:*")
                        .allowedMethods("GET", "POST", "PUT", "DELETE", "OPTIONS", "PATCH")
                        .allowedHeaders("*")
                        .exposedHeaders("Authorization", "Content-Type")
                        .allowCredentials(true)
                        .maxAge(3600);
            }
        };
    }
}


