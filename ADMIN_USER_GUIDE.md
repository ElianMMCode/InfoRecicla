# 👨‍💼 Guía: Usuario Admin del Sistema

## 📋 Información del Usuario Admin

**Email:** `admin@inforecicla.com`  
**Contraseña:** `Admin@123456`  
**Tipo:** Administrador  
**Estado:** Activo

### 🔑 Credenciales de Acceso

```
Usuario: admin@inforecicla.com
Contraseña: Admin@123456
```

---

## 🚀 Cómo se Crea el Usuario Admin

Hay **DOS formas** de crear el usuario administrador:

### Opción 1: Automática (Recomendada)

El usuario admin se crea **automáticamente** al iniciar la aplicación por primera vez gracias a la clase `DataInitializer.java`:

```
✅ La aplicación lo crea automáticamente al arrancar
✅ Verifica si ya existe antes de crearlo
✅ Configuración completa y lista para usar
✅ Sin necesidad de intervención manual
```

**Pasos:**
1. Compila y ejecuta la aplicación
2. La clase `DataInitializer` se ejecuta automáticamente
3. Busca la consola por el mensaje: `✅ Usuario Admin creado exitosamente`

### Opción 2: Manual (Usando SQL)

Si la creación automática falla, puedes usar el script SQL:

**Archivo:** `create_admin_user.sql`

**Pasos:**
1. Abre tu cliente de base de datos (MySQL Workbench, phpMyAdmin, etc.)
2. Conéctate a la base de datos `inforecicla`
3. Ejecuta el script `create_admin_user.sql`
4. Verifica que el usuario se haya creado correctamente

---

## 🛡️ Características del Usuario Admin

| Característica | Valor |
|---|---|
| **Tipo de Usuario** | Admin |
| **Acceso** | Total a todo el sistema |
| **Activo** | Sí (true) |
| **Requiere Validación** | No |
| **Puede Gestionar Usuarios** | Sí |
| **Puede Acceder a Dashboard** | Sí |
| **Puede Acceder a Admin Panel** | Sí |

---

## 🔐 Seguridad

### Contraseña Encriptada

- **Algoritmo:** BCrypt
- **Fortaleza:** 60 caracteres
- **Requisitos Cumplidos:**
  - ✅ Mayúsculas (A)
  - ✅ Minúsculas (dmin)
  - ✅ Números (123456)
  - ✅ Caracteres especiales (@)

### Hash BCrypt de la Contraseña

```
$2a$10$slYQmyNdGzin7olVN3DOCeK3kQ8PfzQG5Sy3EQq/vY2zCKTLu7l5m
```

---

## 📍 Información de Localización

| Campo | Valor |
|---|---|
| **Ciudad** | Bogotá |
| **Localidad** | Chapinero |
| **Latitud** | 4.7110 |
| **Longitud** | -74.0721 |

---

## 🔧 Cómo Cambiar la Contraseña del Admin

### Opción 1: A través de la Base de Datos

```sql
-- Generar nuevo hash BCrypt (ejemplo con "NewPassword@123")
UPDATE usuario 
SET password = '$2a$10$nuevohashbcrypt...'
WHERE email = 'admin@inforecicla.com';
```

### Opción 2: A través de la Aplicación

1. Inicia sesión como admin
2. Ve a tu perfil/configuración
3. Selecciona "Cambiar contraseña"
4. Ingresa la nueva contraseña
5. Confirma los cambios

---

## ⚠️ Importante

- **NO compartas** la contraseña del admin con usuarios no autorizados
- **CAMBIA la contraseña** inmediatamente después del primer acceso
- **MANTÉN segura** la información de acceso
- **REVISA logs** de acceso regularmente

---

## 🐛 Solución de Problemas

### ❌ El usuario admin no se creó automáticamente

**Solución:**
1. Verifica que `DataInitializer.java` esté en `src/main/java/org/sena/inforecicla/config/`
2. Comprueba que la anotación `@Configuration` está presente
3. Usa el script SQL manual: `create_admin_user.sql`
4. Revisa los logs de la aplicación para errores

### ❌ No puedo iniciar sesión con el usuario admin

**Soluciones:**
1. Verifica que el email sea exacto: `admin@inforecicla.com`
2. Verifica que la contraseña sea: `Admin@123456`
3. Confirma que `activo = true` en la base de datos
4. Limpia cookies/caché del navegador
5. Reinicia la aplicación

### ❌ Base de datos no tiene la tabla `localidad`

**Solución:**
1. Asegúrate de que las migraciones de Flyway se ejecutaron
2. Crea manualmente la localidad antes de ejecutar `DataInitializer`
3. O usa el script SQL que incluye la creación de localidad

---

## 📧 Contacto

Si tienes problemas con el usuario admin, por favor:
1. Revisa los logs de la aplicación
2. Verifica la conectividad a la base de datos
3. Confirma que todos los repositorios están correctamente inyectados

