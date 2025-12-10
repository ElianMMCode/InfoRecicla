# 🚀 Guía Rápida de Registro

## Inicio Rápido

### 1️⃣ Iniciar la Aplicación
```bash
cd /home/rorschard/Documents/Java/Inforecicla
mvn spring-boot:run
```

### 2️⃣ Acceder a la Página de Inicio
```
http://localhost:8080
```

### 3️⃣ Registrar como Ciudadano
**URL:** `http://localhost:8080/registro/ciudadano`

**Datos de ejemplo:**
```
Nombres:              Juan
Apellidos:            Pérez
Email:                juan@example.com
Celular:              3001234567
Contraseña:           TestPass123!
Confirmar Contraseña: TestPass123!
Localidad:            Seleccionar de la lista
Aceptar términos:     ✓
```

### 4️⃣ Registrar como Punto ECA
**URL:** `http://localhost:8080/registro/eca`

**Datos de ejemplo:**
```
Institución:         Centro Ambiental
Contacto:            Carlos López
Email:               carlos@eca.com
Teléfono:            3002345678
NIT/Documento:       123456789
Contraseña:          Admin@2024
Confirmar Contraseña: Admin@2024
Dirección:           Calle 10 # 20-30
Localidad:           Seleccionar de la lista
Ubicación:           Click en el mapa
Descripción:         (opcional)
Aceptar términos:    ✓
```

### 5️⃣ Iniciar Sesión
**URL:** `http://localhost:8080/login`

**Con usuario registrado:**
```
Email:       juan@example.com
Contraseña:  TestPass123!
```

---

## 🔗 Todas las Rutas de Registro

| Ruta | Descripción |
|------|-------------|
| `/` | Página de inicio |
| `/login` | Formulario de login |
| `/logout` | Cerrar sesión (POST) |
| `/registro/ciudadano` | Formulario ciudadano (GET/POST) |
| `/registro/eca` | Formulario punto ECA (GET/POST) |

---

## ✅ Requisitos de Contraseña

```
✅ Mínimo 8 caracteres
✅ Una mayúscula (A-Z)
✅ Una minúscula (a-z)
✅ Un número (0-9)
✅ Un símbolo (@$!%*?&)
```

**Ejemplos válidos:**
- `TestPass123!`
- `Admin@2024`
- `Usuario123!`
- `Punto.Eca456`

---

## 📱 Celular Válido

El celular debe:
```
✅ Iniciar con 3
✅ Tener 10 dígitos totales
```

**Formato:** `3XXXXXXXXX`

**Ejemplos válidos:**
- `3001234567`
- `3102345678`
- `3209999999`

---

## 📋 Campos Requeridos por Tipo

### Ciudadano (10 requeridos)
```
✅ Nombres
✅ Apellidos
✅ Email
✅ Celular
✅ Contraseña
✅ Confirmar contraseña
✅ Ciudad (predefinida)
✅ Localidad
✅ Aceptar términos
❌ Documento (opcional)
❌ Fecha nacimiento (opcional)
```

### Punto ECA (11 requeridos)
```
✅ Institución
✅ Contacto
✅ Email
✅ Teléfono
✅ Contraseña
✅ Confirmar contraseña
✅ Dirección
✅ Ciudad (predefinida)
✅ Localidad
✅ Ubicación (mapa)
✅ Aceptar términos
❌ NIT (opcional)
❌ Descripción (opcional)
```

---

## ❌ Errores Comunes y Soluciones

| Error | Causa | Solución |
|-------|-------|----------|
| "El email ya está registrado" | Email duplicado | Usar otro email |
| "El celular ya está registrado" | Celular duplicado | Usar otro celular |
| "Las contraseñas no coinciden" | No son iguales | Verificar y reingresar |
| "Localidad no encontrada" | No seleccionó | Seleccionar de lista |
| Validación contraseña | No cumple patrón | Ver requisitos arriba |
| "Debe incluir mayúscula..." | Contraseña débil | Agregar mayúscula, número, símbolo |

---

## 🗺️ Mapa Interactivo (ECA)

Para registrar un Punto ECA:

1. **Ver mapa de Bogotá**
   - Se abre automáticamente en el formulario
   
2. **Hacer clic en el mapa**
   - Ubicar el punto ECA en el mapa
   
3. **Se actualizan automáticamente**
   - Latitud y longitud en los campos
   
4. **Validación**
   - Must select location on map antes de enviar

---

## 🔐 Seguridad

### Contraseñas
- Se encriptan con **BCrypt** en la BD
- No se almacenan en texto plano
- Se validan en frontend y backend

### Email y Celular
- Son **únicos** en el sistema
- No permite duplicados
- Se validan antes de guardar

### Sesión
- Dura **30 minutos** de inactividad
- Se puede hacer logout manual
- Se invalida completamente

### CSRF
- Formularios protegidos contra CSRF
- Token incluido automáticamente
- Validado en backend

---

## 📊 Después del Registro

### Información Guardada
```
usuario_id:    UUID único
nombres:       Ingresado
apellidos:     Ingresado
email:         Ingresado (UNIQUE)
celular:       Ingresado (UNIQUE)
password:      Encriptado BCrypt
tipo_usuario:  Ciudadano o GestorECA
ciudad:        Bogotá
localidad_id:  De lista seleccionada
activo:        true (habilitado)
fecha_creacion: Automática
```

### Próximo Paso
```
1. Redirige a /login?registro=success
2. Muestra mensaje de éxito (2 segundos)
3. Puede iniciar sesión con email + contraseña
4. Ver nombre en navbar después de login
```

---

## 🧪 Verificar Registro en BD

Después de registrar, ejecutar:

```sql
-- Ver todos los usuarios
SELECT * FROM usuario ORDER BY fecha_creacion DESC;

-- Ver por tipo
SELECT tipo_usuario, COUNT(*) FROM usuario GROUP BY tipo_usuario;

-- Ver específico por email
SELECT * FROM usuario WHERE email = 'juan@example.com';
```

---

## 🎯 Checklist de Prueba

### Registro Ciudadano
- [ ] Acceso a /registro/ciudadano
- [ ] Llenar formulario completo
- [ ] Validaciones frontend funcionan
- [ ] Mensaje de éxito después de registrar
- [ ] Redirige a /login?registro=success
- [ ] Puede iniciar sesión
- [ ] Usuario aparece en navbar

### Registro Punto ECA
- [ ] Acceso a /registro/eca
- [ ] Mapa visible e interactivo
- [ ] Click en mapa actualiza coordenadas
- [ ] Llenar formulario completo
- [ ] Validaciones funcionan
- [ ] Mensaje de éxito
- [ ] Puede iniciar sesión
- [ ] Tipo GestorECA en BD

### Seguridad
- [ ] Email duplicado genera error
- [ ] Celular duplicado genera error
- [ ] Contraseña sin símbolo genera error
- [ ] Contraseñas diferentes generan error
- [ ] Localidad inválida genera error
- [ ] CSRF protection en formularios

---

## 🔄 Flujo Completo

```
Inicio (/login)
    ↓
Hacer clic en "Registrarse como Ciudadano"
    ↓
Completar formulario (/registro/ciudadano)
    ↓
Enviar formulario
    ↓
Backend valida datos
    ↓
Encripta contraseña
    ↓
Guarda usuario en BD
    ↓
Redirige a /login?registro=success
    ↓
Usuario ve mensaje de éxito
    ↓
Ingresa email y contraseña
    ↓
Backend valida credenciales
    ↓
Crea sesión
    ↓
Redirige a /
    ↓
Navbar muestra nombre del usuario
```

---

## 💡 Tips Útiles

1. **Email debe ser único** - No puede repetir en BD
2. **Celular debe ser único** - Formato 3XXXXXXXXX
3. **Contraseña fuerte** - Usar mayúscula + minúscula + número + símbolo
4. **Localidad debe existir** - Seleccionar de dropdown
5. **Para ECA, ubicación es crítica** - Hacer click en mapa
6. **Después de registrar, ir a /login** - Se redirige automáticamente

---

## 📞 Soporte

Si hay problemas:

1. Revisar consola de errores (F12 en navegador)
2. Revisar logs de Spring Boot
3. Verificar que la BD está corriendo
4. Confirmar que localidades existen en BD
5. Revisar contraseña cumple patrón

---

**¡Sistema de registro listo para usar! 🚀**

