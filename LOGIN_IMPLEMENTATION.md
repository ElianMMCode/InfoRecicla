# Implementación del Sistema de Login - InfoRecicla

## ✅ Cambios Realizados

### 1. **Modelo Usuario** (`Usuario.java`)
- ✅ Extendido para implementar la interfaz `UserDetails` de Spring Security
- ✅ Agregado campo `activo` (Boolean) para controlar si el usuario está activo
- ✅ Implementados los métodos requeridos por `UserDetails`:
  - `getAuthorities()`: Retorna roles del usuario (vacío por defecto)
  - `getUsername()`: Retorna el email del usuario
  - `isAccountNonExpired()`: Retorna true
  - `isAccountNonLocked()`: Retorna true
  - `isCredentialsNonExpired()`: Retorna true
  - `isEnabled()`: Retorna el estado del campo `activo`

### 2. **UsuarioRepository** (`UsuarioRepository.java`)
- ✅ Agregado método `findByEmail(String email)`: Optional<Usuario>
- ✅ Agregado método `findByCelular(String celular)`: Optional<Usuario>
- Estos métodos son utilizados por el servicio de autenticación

### 3. **AuthenticationService** (`AuthenticationServiceImpl.java`)
- ✅ Nuevo servicio que implementa `UserDetailsService`
- ✅ Método `loadUserByUsername(String email)`: Carga usuario por email
- ✅ Método `loadUserByCelular(String celular)`: Carga usuario por celular (opcional)
- Validar que el usuario esté activo antes de retornar

### 4. **SecurityConfig** (`SecurityConfig.java`)
- ✅ Configurado el filtro de seguridad con:
  - **CSRF**: Habilitado con excepciones para rutas `/api/**`
  - **Rutas públicas**: `/`, `/inicio`, `/publicaciones`, `/mapa`, `/login`, `/registro/**`, archivos estáticos
  - **Rutas protegidas**: `/dashboard/**`, `/perfil/**`, `/admin/**`
  - **Form Login**: 
    - Página: `/login`
    - Parámetro usuario: `email` (en lugar de `username`)
    - Parámetro contraseña: `password`
    - Éxito: Redirige a `/`
    - Error: Redirige a `/login?error=true`
  - **Logout**: 
    - URL: `/logout`
    - Redirige a `/`
    - Invalida sesión

### 5. **LoginController** (`LoginController.java`)
- ✅ Ruta `GET /login`: Muestra formulario de login
- ✅ Ruta `GET /logout`: Maneja el logout (gestionado por Spring Security)
- ✅ Validación: Si ya está autenticado, redirige a `/`
- ✅ Manejo de errores: Parámetro `error` en URL

### 6. **Plantilla de Login** (`login.html`)
- ✅ Ubicación: `src/main/resources/templates/views/Auth/login.html`
- ✅ Formulario con:
  - Campo email (requerido)
  - Campo contraseña (requerido)
  - Checkbox "Recuérdame"
  - Links para registrar como Ciudadano o Punto ECA
- ✅ Estilos Bootstrap 5.3 con diseño responsivo
- ✅ Mensaje de error dinámico

### 7. **Página de Inicio** (`inicio.html`)
- ✅ Actualizada para mostrar:
  - **Usuario autenticado**: Nombre del usuario con dropdown (Perfil, Dashboard, Logout)
  - **Usuario no autenticado**: Botón "Acceder" con opciones de login y registro
- ✅ Utiliza `sec:authorize` de Spring Security

### 8. **InicioController** (`InicioController.java`)
- ✅ Actualizadas rutas para servir página de inicio en `/` e `/inicio`

## 🔧 Configuración en application.properties

Se recomienda agregar las siguientes propiedades (opcional):

```properties
# Seguridad
spring.security.user.name=admin
spring.security.user.password=admin123

# Sesión
server.servlet.session.timeout=30m
spring.session.store-type=none
```

## 🔐 Flujo de Autenticación

1. **Usuario accede a `/login`**
   - LoginController valida si ya está autenticado
   - Si no, muestra el formulario de login

2. **Usuario envía credenciales**
   - Formulario POST a `/login` con `email` y `password`
   - Spring Security procesa con `AuthenticationManager`
   - `AuthenticationServiceImpl.loadUserByUsername()` busca usuario por email
   - Se compara la contraseña encriptada (BCryptPasswordEncoder)

3. **Login exitoso**
   - Se crea sesión del usuario
   - Redirige a `/` (página de inicio)
   - El usuario ve su nombre en el navbar

4. **Login fallido**
   - Redirige a `/login?error=true`
   - Se muestra mensaje de error

5. **Logout**
   - Usuario hace click en "Cerrar sesión"
   - POST a `/logout`
   - Sesión se invalida
   - Redirige a `/`

## 🚀 Para probar el Login

1. **Crear un usuario de prueba en la base de datos**:
   ```sql
   INSERT INTO usuario (
       usuario_id, nombres, apellidos, password, tipo_usuario,
       celular, email, localidad_id, activo, fecha_creacion
   ) VALUES (
       UUID(), 'Juan', 'Pérez', '$2a$10$...(password encriptado)...', 'CIUDADANO',
       '3001234567', 'juan@example.com', 1, true, NOW()
   );
   ```

   > Para generar contraseña encriptada en BCrypt, puede usar:
   > - https://bcrypt-generator.com/
   > - O crear un test en Java

2. **Iniciar la aplicación**:
   ```bash
   mvn spring-boot:run
   ```

3. **Acceder a la página de login**:
   - URL: `http://localhost:8080/login`
   - Email: `juan@example.com`
   - Contraseña: (la contraseña en texto plano)

## 📋 Próximos Pasos Recomendados

1. **Implementar registro de usuario** (`/registro/ciudadano`, `/registro/eca`)
2. **Agregar roles y autoridades** para diferentes tipos de usuarios
3. **Implementar cambio de contraseña** (`/cambiar-contrasena`)
4. **Agregar recuperación de contraseña**
5. **Implementar validación de email**
6. **Agregar protección contra fuerza bruta**
7. **Implementar página de error 403** (acceso denegado)
8. **Agregar remember-me** para mantener sesión activa

## ⚠️ Notas Importantes

- El campo `password` en Usuario ya tenía validación con patrón que requiere mayúscula, minúscula, número y símbolo
- Las contraseñas deben tener mínimo 8 caracteres
- El email es único en la base de datos
- El campo `activo` controla si el usuario puede acceder a la plataforma

¡El sistema de login está listo para usar! 🎉

