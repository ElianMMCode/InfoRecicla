# 🚀 START GUIDE - PRIMEROS PASOS

## 1️⃣ Compilar y Ejecutar (2 minutos)

```bash
cd /home/rorschard/Documents/Java/Inforecicla
mvn spring-boot:run
```

Espera a que veas:
```
Started InforeciclaApplication in X seconds
```

---

## 2️⃣ Acceder a la Aplicación (1 minuto)

Abre tu navegador:
```
http://localhost:8080
```

Deberías ver la página de inicio con dos botones:
- **Registrarse como Ciudadano**
- **Registrar Punto ECA**

---

## 3️⃣ Registrar un Usuario (5 minutos)

### Opción A: Registro Ciudadano

```
URL: http://localhost:8080/registro/ciudadano

Llenar con:
┌─────────────────────────────────┐
│ Nombres:        Juan            │
│ Apellidos:      Pérez           │
│ Email:          juan@test.com   │
│ Celular:        3001234567      │
│ Contraseña:     TestPass123!    │
│ Confirmar:      TestPass123!    │
│ Localidad:      [Seleccionar]   │
│ Aceptar T&C:    ✓               │
└─────────────────────────────────┘

Clic en: "Registrarse como Ciudadano"
```

**Resultado:** Se redirige a `/login?registro=success`

### Opción B: Registro Punto ECA

```
URL: http://localhost:8080/registro/eca

Llenar con:
┌─────────────────────────────────┐
│ Institución:    Centro Eco      │
│ Contacto:       Carlos López    │
│ Email:          carlos@eca.com  │
│ Teléfono:       3002345678      │
│ Contraseña:     Admin@2024      │
│ Confirmar:      Admin@2024      │
│ Dirección:      Calle 10 #20    │
│ Localidad:      [Seleccionar]   │
│ Ubicación:      Click en mapa   │
└─────────────────────────────────┘

Clic en: "Registrar Punto ECA"
```

**Resultado:** Se redirige a `/login?registro=success`

---

## 4️⃣ Iniciar Sesión (2 minutos)

```
URL: http://localhost:8080/login

Ingresar:
┌─────────────────────────────────┐
│ Email:          juan@test.com   │
│ Contraseña:     TestPass123!    │
│ Recuérdame:     □               │
└─────────────────────────────────┘

Clic en: "Iniciar Sesión"
```

**Resultado:** Se redirige a `/` y ves tu nombre en el navbar

---

## 5️⃣ Verificar Funcionamiento

### En la página principal (`/`)

Deberías ver:
- ✅ Navbar actualizado con tu nombre
- ✅ Menú desplegable con opciones
- ✅ Botón "Cerrar sesión"

### Para logout

Haz clic en tu nombre (navbar) → "Cerrar sesión"

**Resultado:** Redirige a `/` sin estar autenticado

---

## 🔑 Requisitos de Contraseña

```
⚠️ IMPORTANTE - La contraseña DEBE cumplir TODO esto:

✅ Mínimo 8 caracteres
✅ Al menos UNA mayúscula
✅ Al menos UNA minúscula
✅ Al menos UN número
✅ Al menos UN símbolo (@$!%*?&)

EJEMPLOS VÁLIDOS:
✓ TestPass123!
✓ Admin@2024
✓ Usuario123!
✓ Punto.Eca456

EJEMPLOS INVÁLIDOS:
✗ password        (sin mayúscula, número, símbolo)
✗ TestPass        (sin número ni símbolo)
✗ Test@2024       (sin minúscula)
✗ Test@Aa        (muy corta)
```

---

## 📱 Celular Válido

```
FORMATO REQUERIDO:
┌──────────────┐
│ 3XXXXXXXXX   │
│ 10 dígitos   │
│ Comienza con 3│
└──────────────┘

EJEMPLOS VÁLIDOS:
✓ 3001234567
✓ 3102345678
✓ 3209999999

EJEMPLOS INVÁLIDOS:
✗ 2001234567    (no comienza con 3)
✗ 30012345      (muy corto)
✗ 300123456789  (muy largo)
```

---

## ❌ Si Algo Falla

### Error: "El email ya está registrado"
```
✅ Solución: Usa otro email en el registro
   Ejemplo: juan2@test.com, juan3@test.com, etc.
```

### Error: "El celular ya está registrado"
```
✅ Solución: Usa otro celular
   Ejemplo: 3002345678 (en lugar de 3001234567)
```

### Error: "Las contraseñas no coinciden"
```
✅ Solución: Verifica que sean iguales en ambos campos
   Asegúrate de no tener espacios al final
```

### Error: "Debe incluir mayúscula, minúscula, número y símbolo"
```
✅ Solución: Usa contraseña más fuerte
   Ejemplo: TestPass123! (válida)
```

### Error: "Localidad no encontrada"
```
✅ Solución: Selecciona una localidad de la lista desplegable
   No dejes en blanco
```

### Aplicación no inicia
```
✅ Verificar que el puerto 8080 esté libre:
   lsof -i :8080
   
✅ Si está en uso, cambiar en application.properties:
   server.port=8081
```

---

## 📚 Documentación por Tipo de Usuario

### 👨‍💼 Solo quiero usar el sistema
→ Lee **GUIA_RAPIDA_REGISTRO.md**

### 👨‍💻 Necesito entender el código
→ Lee **IMPLEMENTACION_COMPLETA.md**

### 🧪 Soy tester/QA
→ Lee **GUIA_RAPIDA_REGISTRO.md** + **COMANDOS_REFERENCIAS.md**

### 📊 Necesito administrar la BD
→ Lee **COMANDOS_REFERENCIAS.md**

### ❓ Tengo una pregunta
→ Busca en **FAQ_LOGIN.md**

---

## 🎯 Checklist Rápido

Después de ejecutar, verifica:

- [ ] Aplicación inicia sin errores
- [ ] Puedo acceder a `http://localhost:8080`
- [ ] Puedo registrarme como ciudadano
- [ ] Puedo registrarme como punto ECA
- [ ] Puedo iniciar sesión
- [ ] Veo mi nombre en el navbar
- [ ] Puedo hacer logout
- [ ] Después de logout, no veo mi nombre

---

## ⏱️ Tiempo Total

```
Compilar y ejecutar:   2 minutos
Registrar usuario:     5 minutos
Iniciar sesión:        2 minutos
Verificar:             1 minuto
────────────────────────────────
TOTAL:               10 minutos
```

---

## 🚀 ¡Listo!

Ya tienes el sistema de autenticación y registro completamente funcional.

### Próximos pasos:
1. Explorar la UI
2. Probar diferentes tipos de registro
3. Revisar documentación si necesitas cambios
4. Implementar funcionalidades adicionales (según necesidad)

---

## 📞 ¿Necesitas Ayuda?

Cada documento tiene sección de FAQ y troubleshooting:

- **GUIA_RAPIDA_REGISTRO.md** - Errores comunes
- **FAQ_LOGIN.md** - Preguntas frecuentes  
- **COMANDOS_REFERENCIAS.md** - Solución de problemas

---

**¡A disfrutar el sistema!** 🎉

