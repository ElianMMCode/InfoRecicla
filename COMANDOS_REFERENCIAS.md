# ⚡ COMANDOS ÚTILES Y REFERENCIAS

## 🚀 Iniciando la Aplicación

### Compilar y ejecutar
```bash
cd /home/rorschard/Documents/Java/Inforecicla
mvn spring-boot:run
```

### Solo compilar (sin ejecutar)
```bash
mvn clean compile
```

### Compilar sin ejecutar pruebas
```bash
mvn clean compile -DskipTests
```

### Construir JAR ejecutable
```bash
mvn clean package -DskipTests
```

---

## 🌐 URLs de Acceso

### Página Principal
```
http://localhost:8080
http://localhost:8080/inicio
```

### Autenticación
```
http://localhost:8080/login              Formulario de login
http://localhost:8080/logout             Cerrar sesión
http://localhost:8080/registro/ciudadano Registro ciudadano
http://localhost:8080/registro/eca       Registro punto ECA
```

### Áreas Protegidas (requieren login)
```
http://localhost:8080/dashboard          Dashboard privado
http://localhost:8080/perfil             Perfil del usuario
http://localhost:8080/admin              Panel administrativo
```

---

## 🗄️ Base de Datos - SQL Útil

### Ver todos los usuarios
```sql
SELECT * FROM usuario ORDER BY fecha_creacion DESC;
```

### Ver usuarios por tipo
```sql
SELECT tipo_usuario, COUNT(*) as total FROM usuario GROUP BY tipo_usuario;
```

### Ver ciudadanos
```sql
SELECT usuario_id, nombres, apellidos, email, celular, activo 
FROM usuario WHERE tipo_usuario = 'Ciudadano';
```

### Ver Puntos ECA
```sql
SELECT usuario_id, nombres, apellidos, email, celular, latitud, longitud, activo 
FROM usuario WHERE tipo_usuario = 'GestorECA';
```

### Buscar usuario por email
```sql
SELECT * FROM usuario WHERE email = 'juan@example.com';
```

### Contar usuarios activos
```sql
SELECT COUNT(*) as usuarios_activos FROM usuario WHERE activo = true;
```

### Activar usuario
```sql
UPDATE usuario SET activo = true WHERE email = 'usuario@example.com';
```

### Desactivar usuario
```sql
UPDATE usuario SET activo = false WHERE email = 'usuario@example.com';
```

### Ver últimos 10 registros
```sql
SELECT usuario_id, nombres, apellidos, email, tipo_usuario, fecha_creacion 
FROM usuario ORDER BY fecha_creacion DESC LIMIT 10;
```

### Eliminar usuario de prueba
```sql
DELETE FROM usuario WHERE email = 'test@example.com';
```

---

## 🔑 Generar Contraseñas BCrypt

### Opción 1: Usar PasswordEncoderUtil
```bash
# Compilar primero
mvn clean compile

# Ejecutar utilidad
java -cp target/classes org.sena.inforecicla.util.PasswordEncoderUtil
```

Mostrará hashes para:
- TestPass123!
- Admin@2024
- Usuario123!
- Punto.Eca456

### Opción 2: Usar sitio web
```
https://bcrypt-generator.com/
```
Ingresa contraseña → copia el hash

### Opción 3: Usar en SQL
```sql
-- Actualizar contraseña existente
UPDATE usuario 
SET password = '$2a$10$TU_HASH_BCRYPT_AQUI' 
WHERE email = 'usuario@example.com';
```

---

## 🧪 Testing Manual

### Test 1: Registro Ciudadano
```
1. Acceder a: http://localhost:8080/registro/ciudadano
2. Llenar con:
   - Nombres: Juan
   - Apellidos: Pérez
   - Email: juan@test.com
   - Celular: 3001234567
   - Contraseña: TestPass123!
   - Confirmar: TestPass123!
   - Localidad: Seleccionar
3. Clic en "Registrarse"
4. Verificar redirección a /login?registro=success
5. Iniciar sesión con email/contraseña
```

### Test 2: Registro Punto ECA
```
1. Acceder a: http://localhost:8080/registro/eca
2. Llenar con:
   - Institución: Centro Ambiental
   - Contacto: Carlos López
   - Email: carlos@eca.com
   - Teléfono: 3002345678
   - Contraseña: Admin@2024
   - Confirmar: Admin@2024
   - Dirección: Calle 10 # 20-30
   - Localidad: Seleccionar
3. Click en mapa para ubicar
4. Clic en "Registrar"
5. Verificar redirección a /login?registro=success
6. Iniciar sesión
```

### Test 3: Validaciones
```
1. Intentar email duplicado → Error
2. Intentar celular duplicado → Error
3. Contraseñas no coinciden → Error
4. Contraseña sin símbolo → Error
5. Email inválido → Error
6. Localidad no seleccionada → Error
```

---

## 🔍 Debug y Logs

### Ver logs en consola
```bash
mvn spring-boot:run
```

### Log levels en application.properties
```properties
logging.level.org.springframework=INFO
logging.level.org.springframework.security=DEBUG
logging.level.org.sena.inforecicla=DEBUG
```

### Ver logs de Spring Security
```bash
mvn spring-boot:run -Dspring-boot.run.arguments="--logging.level.org.springframework.security=DEBUG"
```

---

## 📁 Archivos Importantes

### Archivos de Código
```
src/main/java/org/sena/inforecicla/
├── controller/LoginController.java
├── controller/RegisterController.java
├── service/impl/AuthenticationServiceImpl.java
├── service/impl/UsuarioServiceImpl.java
└── dto/usuario/
    ├── RegistroCiudadanoDTO.java
    ├── RegistroPuntoEcaDTO.java
    └── UsuarioResponseDTO.java
```

### Archivos de Vistas
```
src/main/resources/templates/
├── views/Auth/
│   ├── login.html
│   ├── registro-ciudadano.html
│   └── registro-eca.html
└── views/Inicio/
    └── inicio.html
```

### Archivos de Configuración
```
src/main/java/org/sena/inforecicla/config/SecurityConfig.java
src/main/resources/application.properties
pom.xml
```

### Documentación
```
REGISTRO_USUARIOS.md
GUIA_RAPIDA_REGISTRO.md
LOGIN_IMPLEMENTATION.md
FAQ_LOGIN.md
```

---

## 🛠️ Comandos Git (si aplica)

### Ver estado
```bash
git status
```

### Ver cambios
```bash
git diff
```

### Commit de cambios
```bash
git add .
git commit -m "Implementación de sistema de registro"
```

### Ver histórico
```bash
git log --oneline -10
```

---

## 📊 Estructura Base de Datos

### Tabla Usuario (simplificado)
```sql
CREATE TABLE usuario (
    usuario_id CHAR(36) PRIMARY KEY,
    nombres VARCHAR(30) NOT NULL,
    apellidos VARCHAR(40) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    celular VARCHAR(10) UNIQUE NOT NULL,
    password VARCHAR(60) NOT NULL,
    tipo_usuario VARCHAR(15) NOT NULL,
    tipo_documento VARCHAR(5),
    numero_documento VARCHAR(20) UNIQUE,
    fecha_nacimiento VARCHAR(10),
    ciudad VARCHAR(15),
    localidad_id CHAR(36),
    latitud DECIMAL(10,6),
    longitud DECIMAL(10,6),
    biografia VARCHAR(500),
    foto_perfil VARCHAR(255),
    activo BOOLEAN DEFAULT true,
    fecha_creacion DATETIME,
    fecha_modificacion DATETIME,
    FOREIGN KEY (localidad_id) REFERENCES localidad(localidad_id)
);
```

---

## ✅ Validaciones de Referencia

### Contraseña Válida
```
✅ Mínimo 8 caracteres
✅ Al menos una mayúscula
✅ Al menos una minúscula
✅ Al menos un número
✅ Al menos un símbolo (@$!%*?&)

Ejemplo: TestPass123!
```

### Celular Válido
```
✅ Comienza con 3
✅ Tiene exactamente 10 dígitos

Ejemplo: 3001234567
```

### Email Válido
```
✅ Formato estándar de email
✅ Único en la base de datos

Ejemplo: juan@example.com
```

---

## 🚨 Solución de Problemas Comunes

### "El email ya está registrado"
```
Solución: Usar otro email o limpiar BD
```

### "El celular ya está registrado"
```
Solución: Usar otro celular
```

### "Las contraseñas no coinciden"
```
Solución: Verificar que sean iguales en ambos campos
```

### "Localidad no encontrada"
```
Solución: Seleccionar una localidad de la lista desplegable
```

### "Puerto 8080 en uso"
```
Solución: 
1. Cambiar puerto en application.properties:
   server.port=8081
2. O matar proceso en puerto 8080:
   lsof -ti:8080 | xargs kill -9
```

### "Conexión a BD rechazada"
```
Solución:
1. Verificar que MariaDB está corriendo
2. Verificar credenciales en application.properties
3. Verificar que la BD existe
```

---

## 📈 Monitoreo de Aplicación

### Ver procesos Java
```bash
jps -l
```

### Ver puertos en uso
```bash
netstat -tuln | grep 8080
lsof -i :8080
```

### Ver logs en tiempo real
```bash
tail -f logs/application.log
```

---

## 💾 Backup y Restauración

### Exportar BD
```bash
mysqldump -u root -p nombre_bd > backup.sql
```

### Importar BD
```bash
mysql -u root -p nombre_bd < backup.sql
```

### Ejecutar script SQL
```bash
mysql -u root -p nombre_bd < verificar_registro.sql
```

---

## 🔧 Cambios Rápidos

### Cambiar tiempo de sesión
```properties
# application.properties
server.servlet.session.timeout=60m  # 60 minutos
```

### Cambiar puerto
```properties
# application.properties
server.port=8081
```

### Cambiar URL de BD
```properties
# application.properties
spring.datasource.url=jdbc:mariadb://host:3306/database
```

### Habilitar SQL logging
```properties
# application.properties
spring.jpa.show-sql=true
spring.jpa.properties.hibernate.format_sql=true
```

---

## 📚 Recursos Útiles

### Documentación del Proyecto
- `REGISTRO_USUARIOS.md` - Documentación técnica completa
- `GUIA_RAPIDA_REGISTRO.md` - Guía para usuario
- `LOGIN_IMPLEMENTATION.md` - Sistema de login
- `FAQ_LOGIN.md` - Preguntas frecuentes

### Documentación Externa
- Spring Boot: https://spring.io/projects/spring-boot
- Spring Security: https://spring.io/projects/spring-security
- Thymeleaf: https://www.thymeleaf.org/
- Bootstrap: https://getbootstrap.com/
- Leaflet Maps: https://leafletjs.com/

---

## 🎯 Checklist de Deployment

- [ ] Aplicación compila sin errores
- [ ] BD está creada y accesible
- [ ] Localidades existen en BD
- [ ] application.properties configurado correctamente
- [ ] Puerto 8080 no está en uso
- [ ] Contraseña de BD es segura
- [ ] HTTPS está habilitado (para producción)
- [ ] Logs están configurados
- [ ] Backup de BD existe
- [ ] Equipo de desarrollo notificado

---

**¡Todos los comandos y referencias que necesitas!** ✅

