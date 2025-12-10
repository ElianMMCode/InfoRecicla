# ❓ Preguntas Frecuentes - Sistema de Login

## General

### ¿Dónde está la página de login?
- **URL**: `http://localhost:8080/login`
- **Archivo**: `src/main/resources/templates/views/Auth/login.html`

### ¿Qué archivos fueron modificados?
Corre este comando para ver los cambios:
```bash
git status
```

O revisa el documento `LOGIN_IMPLEMENTATION.md`

---

## Autenticación

### ¿Por qué usa email en lugar de username?
Porque el modelo `Usuario` ya tenía email como atributo único. Se configuró en `SecurityConfig.java`:
```java
.usernameParameter("email")  // Email como username
```

### ¿Cómo cambio el parámetro a usar teléfono?
En `SecurityConfig.java`:
```java
.usernameParameter("celular")  // Cambiar a celular

// Y en AuthenticationServiceImpl:
Usuario usuario = usuarioRepository.findByCelular(celular)...
```

### ¿Se pueden usar ambos (email y celular)?
Sí, requeriría crear un `AuthenticationProvider` personalizado. Contacta si necesitas esto.

---

## Contraseñas

### Mi contraseña no funciona
1. Verifica que cumpla el patrón:
   - Al menos una mayúscula
   - Al menos una minúscula
   - Al menos un número
   - Al menos un símbolo especial (@$!%*?&)
   - Mínimo 8 caracteres

2. Ejemplos válidos:
   - `TestPass123!`
   - `Admin@2024`
   - `Usuario123!`

### ¿Cómo genero un hash BCrypt?
Opción 1: Ejecutar utilidad Java
```bash
java -cp target/classes org.sena.inforecicla.util.PasswordEncoderUtil
```

Opción 2: Usar sitio web
- https://bcrypt-generator.com/

Opción 3: Script SQL
```sql
-- Inserta en la BD directo con contraseña encriptada
UPDATE usuario 
SET password = '$2a$10$...(hash)...' 
WHERE email = 'usuario@example.com';
```

### ¿Puedo cambiar la validación de contraseña?
Sí, en `Usuario.java`:
```java
@Pattern(
    regexp = "^(?=.*[a-z])(?=.*[A-Z])(?=.*\\d)(?=.*[@$!%*?&]).+$",
    message = "Debe incluir mayúscula, minúscula, número y símbolo"
)
private String password;
```

---

## Sesiones y Seguridad

### ¿Cuánto tiempo dura la sesión?
Por defecto: **30 minutos**

Para cambiar, edita `application.properties`:
```properties
server.servlet.session.timeout=60  # 60 minutos
```

### ¿Cómo logout al usuario después de cierto tiempo?
Ya está configurado. La sesión expira automáticamente después del timeout.

### ¿Cómo agrego "Remember me"?
En `SecurityConfig.java`, agrega:
```java
.rememberMe(remember -> remember
    .rememberMeParameter("rememberMe")
    .tokenValiditySeconds(86400)  // 1 día
)
```

Y en `login.html`:
```html
<input type="checkbox" name="rememberMe"> Recuérdame
```

### ¿Por qué no aparece CSRF token en el formulario?
Thymeleaf lo agrega automáticamente cuando usas:
```html
<form method="POST" th:action="@{/login}">
```

Si usas AJAX, agrégalo manualmente:
```javascript
const token = document.querySelector('input[name="_csrf"]').value;
```

---

## Rutas y Acceso

### ¿Cómo protejo una ruta?
En `SecurityConfig.java`, agrega:
```java
.authorizeHttpRequests(auth -> auth
    .requestMatchers("/mi-ruta").authenticated()  // Requiere login
    .requestMatchers("/publica").permitAll()      // Pública
)
```

### ¿Cómo uso autorización por rol?
Primero, agrega roles al usuario. Luego en SecurityConfig:
```java
.requestMatchers("/admin/**").hasRole("ADMIN")
.requestMatchers("/gestor/**").hasRole("GESTOR")
```

### ¿Qué rutas son públicas por defecto?
```
/                    // Inicio
/inicio              // Inicio (alternativo)
/publicaciones       // Publicaciones
/mapa                // Mapa ECA
/login               // Formulario login
/registro/**         // Rutas de registro
/css/**, /js/**, ... // Archivos estáticos
```

---

## Errores Comunes

### Error: "Usuario no encontrado"
- Verifica que el email existe en la BD
- Revisa que el email esté escrito correctamente
- Asegúrate de que `activo = true` en la BD

### Error: "Contraseña incorrecta"
- Verifica que ingresaste la contraseña correctamente
- Recuerda que es case-sensitive
- Asegúrate de usar la contraseña en **texto plano**, no el hash

### Error: CSRF token inválido
- El token expiró, recarga la página
- Si usas AJAX, incluye el token en headers:
```javascript
headers: {
    'X-CSRF-TOKEN': token
}
```

### Error: 403 Forbidden
- No estás autenticado
- Tu usuario no tiene permisos para esa ruta
- Verifica el campo `activo` en la BD

### Error: 404 - Vista no encontrada
- El archivo `login.html` no está en la ruta correcta
- Debe estar en: `src/main/resources/templates/views/Auth/login.html`

---

## Base de Datos

### ¿Necesito agregar la columna `activo`?
Sí, si tu tabla `usuario` no la tiene:

```sql
ALTER TABLE usuario ADD COLUMN activo BOOLEAN DEFAULT true;
```

### ¿Cómo verifico qué usuarios están en la BD?
```sql
SELECT usuario_id, nombres, email, celular, activo FROM usuario;
```

### ¿Cómo desactivo un usuario?
```sql
UPDATE usuario SET activo = false WHERE email = 'usuario@example.com';
```

---

## Testing

### ¿Cómo pruebo el login?
1. Crea usuario de prueba en BD
2. Inicia la aplicación: `mvn spring-boot:run`
3. Ve a `http://localhost:8080/login`
4. Ingresa email y contraseña
5. Verifica que veas tu nombre en el navbar

### ¿Cómo hago pruebas automatizadas?
Crea un test:
```java
@SpringBootTest
@AutoConfigureMockMvc
class LoginControllerTest {
    
    @Autowired
    MockMvc mockMvc;
    
    @Test
    void testLogin() throws Exception {
        mockMvc.perform(post("/login")
            .param("email", "juan@example.com")
            .param("password", "TestPass123!"))
            .andExpect(redirectedUrl("/"));
    }
}
```

---

## Personalización

### ¿Cómo cambio el diseño del formulario de login?
Edita `login.html` en:
`src/main/resources/templates/views/Auth/login.html`

El archivo usa Bootstrap 5, puedes cambiar colores, tamaños, etc.

### ¿Cómo cambio el mensaje de bienvenida?
En `inicio.html`, busca:
```html
<h1 class="display-5 fw-bold lh-sm">Bienvenido a InfoRecicla</h1>
```

### ¿Cómo agrego logo personalizado?
En `login.html`, busca:
```html
<img src="/imagenes/logo.png" th:src="@{/imagenes/logo.png}" alt="Logo">
```

Coloca tu logo en `src/main/resources/static/imagenes/logo.png`

---

## Integración

### ¿Cómo integro con mi controlador de usuario?
En tu controlador, inyecta:
```java
@Autowired
private AuthenticationServiceImpl authService;

@GetMapping("/mi-perfil")
public String perfil(Principal principal, Model model) {
    String email = principal.getName();
    // Cargar datos del usuario
    return "perfil";
}
```

### ¿Cómo obtengo el usuario actual?
En controlador:
```java
@GetMapping("/datos-usuario")
public String datos(Principal principal) {
    String email = principal.getName();  // Email del usuario
    return email;
}
```

En Thymeleaf:
```html
<span sec:authentication="name"></span>
<span sec:authentication="principal.nombres"></span>
```

---

## Producción

### ¿Qué debo hacer antes de ir a producción?
- [ ] Cambiar contraseñas de prueba
- [ ] Habilitar HTTPS
- [ ] Usar variables de entorno para secretos
- [ ] Aumentar timeout según necesidad
- [ ] Implementar rate limiting
- [ ] Agregar logs de seguridad
- [ ] Hacer testing de seguridad
- [ ] Configurar CORS si es necesario

### ¿Cómo agrego HTTPS?
En `application.properties`:
```properties
server.ssl.key-store=classpath:keystore.p12
server.ssl.key-store-password=tu_password
server.ssl.keyStoreType=PKCS12
server.port=8443
```

---

## Soporte

¿No encuentras tu pregunta aquí?
- Revisa `LOGIN_IMPLEMENTATION.md` para documentación completa
- Revisa `README_LOGIN.md` para instrucciones paso a paso
- Verifica los logs: `target/logs/`

---

**¡Espero que el sistema de login funcione perfecto! 🚀**

