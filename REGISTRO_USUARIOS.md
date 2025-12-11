# 📝 Sistema de Registro de Usuarios - InfoRecicla

## 🎯 Descripción General

Se ha implementado un sistema completo de registro de usuarios con dos tipos de cuentas:
1. **Ciudadano** - Usuarios regulares que pueden publicar y participar
2. **Punto ECA** - Instituciones que ofrecen servicios de reciclaje

---

## 📋 Flujo de Registro

```
Usuario accede a /registro/ciudadano o /registro/eca
           ↓
    Completa formulario
           ↓
    Valida datos (email, celular únicos, contraseña válida)
           ↓
    Crear usuario en BD
           ↓
    Encriptar contraseña con BCrypt
           ↓
    Guardar usuario
           ↓
    Redirige a /login?registro=success
           ↓
    Muestra mensaje de éxito
           ↓
    Usuario puede iniciar sesión
```

---

## 🔗 Rutas Disponibles

| Ruta | Método | Descripción |
|------|--------|-------------|
| `/registro/ciudadano` | GET | Formulario de registro ciudadano |
| `/registro/ciudadano` | POST | Procesar registro ciudadano |
| `/registro/eca` | GET | Formulario de registro punto ECA |
| `/registro/eca` | POST | Procesar registro punto ECA |

---

## 📁 Archivos Creados

### DTOs (Data Transfer Objects)
- `RegistroCiudadanoDTO.java` - DTO para registro de ciudadano
- `RegistroPuntoEcaDTO.java` - DTO para registro de punto ECA
- `UsuarioResponseDTO.java` - DTO para respuesta de registro exitoso

### Controladores
- `RegisterController.java` - Controlador de registro con 4 endpoints

### Servicios
- `UsuarioServiceImpl.java` - Implementación de servicios de usuario

### Plantillas HTML
- `registro-ciudadano.html` - Formulario de registro ciudadano
- `registro-eca.html` - Formulario de registro punto ECA

---

## 📝 Campos del Registro Ciudadano

| Campo | Tipo | Validación | Requerido |
|-------|------|-----------|-----------|
| Nombres | String | 3-30 caracteres | ✅ |
| Apellidos | String | 2-40 caracteres | ✅ |
| Email | Email | Único, formato válido | ✅ |
| Celular | String | 3XXXXXXXXX, único | ✅ |
| Contraseña | String | 8-60 chars, patrón complejo | ✅ |
| Confirmar Contraseña | String | Debe coincidir | ✅ |
| Tipo Documento | Enum | CC, CE, PA, NIT | ❌ |
| Número Documento | String | 6-20 caracteres | ❌ |
| Fecha Nacimiento | Date | YYYY-MM-DD | ❌ |
| Ciudad | String | Predefinida a Bogotá | ✅ |
| Localidad | Select | De lista de localidades | ✅ |

---

## 📝 Campos del Registro Punto ECA

| Campo | Tipo | Validación | Requerido |
|-------|------|-----------|-----------|
| Nombre Institución | String | 5-100 caracteres | ✅ |
| Nombre Contacto | String | 3-30 caracteres | ✅ |
| Email Contacto | Email | Único, formato válido | ✅ |
| Teléfono | String | 3XXXXXXXXX, único | ✅ |
| NIT/Documento | String | 6-20 caracteres | ❌ |
| Contraseña | String | 8-60 chars, patrón complejo | ✅ |
| Confirmar Contraseña | String | Debe coincidir | ✅ |
| Dirección | String | 10-100 caracteres | ✅ |
| Ciudad | String | Predefinida a Bogotá | ✅ |
| Localidad | Select | De lista de localidades | ✅ |
| Latitud | Double | -90 a 90 | ✅ |
| Longitud | Double | -180 a 180 | ✅ |
| Descripción | Text | Máx 500 caracteres | ❌ |

---

## 🔐 Requisitos de Contraseña

La contraseña debe cumplir todos estos requisitos:
- ✅ Mínimo 8 caracteres
- ✅ Al menos una mayúscula (A-Z)
- ✅ Al menos una minúscula (a-z)
- ✅ Al menos un número (0-9)
- ✅ Al menos un símbolo especial (@$!%*?&)

**Ejemplos válidos:**
- `TestPass123!`
- `Admin@2024`
- `Ciudadano123!`
- `ECA.Punto456`

---

## 🛡️ Validaciones Implementadas

### En el Frontend (Thymeleaf)
- ✅ Campos requeridos
- ✅ Formato de email válido
- ✅ Celular debe iniciar con 3 y tener 10 dígitos
- ✅ Las contraseñas deben coincidir
- ✅ Para Punto ECA: validación de ubicación en mapa

### En el Backend (UsuarioServiceImpl)
- ✅ Email único en la BD
- ✅ Celular único en la BD
- ✅ Contraseñas coinciden
- ✅ Localidad existe en la BD
- ✅ Contraseña encriptada con BCrypt antes de guardar

---

## 🗺️ Mapa Interactivo (Punto ECA)

El formulario de Punto ECA incluye un mapa interactivo (Leaflet) que permite:
- 🗺️ Ver el mapa de Bogotá
- 📍 Hacer clic para seleccionar ubicación
- 📊 Latitud y longitud se actualizan automáticamente
- ✅ Validación de que debe seleccionar una ubicación

---

## ✅ Campos Automáticos

Algunos campos se llenan automáticamente:

### Ciudadano
- `tipoUsuario` = `CIUDADANO`
- `activo` = `true`
- `ciudad` = `Bogotá`

### Punto ECA
- `tipoUsuario` = `GESTOR_ECA`
- `activo` = `true`
- `ciudad` = `Bogotá`

---

## 📊 Proceso de Guardado en BD

```java
Usuario usuario = new Usuario();
usuario.setNombres(dto.nombres());
usuario.setApellidos(dto.apellidos());
// ... otros campos ...
usuario.setPassword(passwordEncoder.encode(dto.password())); // Encriptar
usuario.setActivo(true);
Usuario usuarioGuardado = usuarioRepository.save(usuario); // Guardar
```

---

## 🧪 Cómo Probar el Registro

### 1. Iniciar la Aplicación
```bash
cd /home/rorschard/Documents/Java/Inforecicla
mvn spring-boot:run
```

### 2. Acceder a Formulario de Ciudadano
```
URL: http://localhost:8080/registro/ciudadano
```

**Llenar con:**
- Nombres: `Juan`
- Apellidos: `Pérez`
- Email: `juan@example.com`
- Celular: `3001234567`
- Contraseña: `TestPass123!`
- Confirmar: `TestPass123!`
- Localidad: Seleccionar una disponible
- Aceptar términos

**Resultado:** Redirige a `/login?registro=success`

### 3. Acceder a Formulario de Punto ECA
```
URL: http://localhost:8080/registro/eca
```

**Llenar con:**
- Institución: `Centro Ambiental San Felipe`
- Contacto: `Carlos López`
- Email: `carlos@eca.com`
- Teléfono: `3002345678`
- Contraseña: `Admin@2024`
- Confirmar: `Admin@2024`
- Dirección: `Calle 10 # 20 - 30`
- Localidad: Seleccionar una
- Click en mapa para ubicación
- Aceptar términos

**Resultado:** Redirige a `/login?registro=success`

### 4. Iniciar Sesión
```
URL: http://localhost:8080/login
Email: juan@example.com (o carlos@eca.com)
Contraseña: TestPass123! (o Admin@2024)
```

---

## 🚨 Mensajes de Error

El sistema muestra errores claros en cada caso:

| Error | Causa | Solución |
|-------|-------|----------|
| "El email ya está registrado" | Email duplicado | Usar otro email |
| "El celular ya está registrado" | Celular duplicado | Usar otro celular |
| "Las contraseñas no coinciden" | No son iguales | Verificar ambas |
| "Localidad no encontrada" | Selección inválida | Seleccionar de la lista |
| Validación de patrón de contraseña | No cumple requisitos | Ver requisitos arriba |

---

## 📱 Responsive Design

Ambos formularios son **completamente responsivos**:
- ✅ Móvil (320px)
- ✅ Tablet (768px)
- ✅ Desktop (1200px+)

---

## 🔗 Integración con Login

Después del registro exitoso:
1. Usuario redirigido a `/login?registro=success`
2. Se muestra mensaje verde: "¡Registro exitoso! Ahora puedes iniciar sesión"
3. Usuario puede ingresar sus credenciales
4. Después de login, redirige a `/`

---

## 📊 Información Guardada en BD

### Ciudadano Registrado
```
usuario_id: UUID generado
nombres: Ingresado
apellidos: Ingresado
email: Ingresado (único)
celular: Ingresado (único)
password: Encriptado con BCrypt
tipo_usuario: CIUDADANO
tipo_documento: Opcional
numero_documento: Opcional
fecha_nacimiento: Opcional
ciudad: Bogotá
localidad_id: FK a localidad
activo: true
fecha_creacion: NOW()
fecha_modificacion: NOW()
```

### Punto ECA Registrado
```
usuario_id: UUID generado
nombres: Nombre institución
apellidos: Nombre contacto
email: Email contacto
celular: Teléfono
password: Encriptado con BCrypt
tipo_usuario: GESTOR_ECA
tipo_documento: Opcional
numero_documento: NIT/Documento
ciudad: Bogotá
localidad_id: FK a localidad
latitud: Desde mapa
longitud: Desde mapa
biografia: Descripción
activo: true
fecha_creacion: NOW()
fecha_modificacion: NOW()
```

---

## 🔄 Flujo Completo de Usuario Nuevo

```
1. Usuario accede a InfoRecicla (/)
                    ↓
2. Hace clic en "Registrarse como Ciudadano" o "Registrar Punto ECA"
                    ↓
3. Completa el formulario correspondiente
                    ↓
4. Hace clic en "Registrarse" o "Registrar Punto ECA"
                    ↓
5. Backend valida todos los datos
                    ↓
6. Si hay error → muestra mensaje y mantiene en formulario
   Si es válido → continúa
                    ↓
7. Crea usuario en BD (con contraseña encriptada)
                    ↓
8. Redirige a /login?registro=success
                    ↓
9. Usuario ve mensaje de éxito
                    ↓
10. Ingresa email y contraseña
                    ↓
11. Inicia sesión exitosamente
                    ↓
12. Ve el navbar con su nombre
```

---

## ✅ Verificación Final

Para confirmar que todo funciona:

1. ✅ Compilación sin errores
2. ✅ Dos tipos de registro funcionan
3. ✅ Validaciones en frontend y backend
4. ✅ Contraseñas se encriptan
5. ✅ Emails y celulares únicos se validan
6. ✅ Mensajes de error claros
7. ✅ Redireccionamientos correctos
8. ✅ Integración con login
9. ✅ Formularios responsivos
10. ✅ Mapa interactivo para ECA

---

## 🎯 Próximas Mejoras

- [ ] Confirmación de email
- [ ] Validación de NIT en API externa
- [ ] Subida de documentos
- [ ] Aprobación de Puntos ECA por admin
- [ ] Recuperación de contraseña

El sistema de registro está **completamente funcional y listo para usar** 🚀

