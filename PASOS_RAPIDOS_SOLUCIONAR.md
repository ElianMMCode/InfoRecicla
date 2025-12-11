# ⚡ GUÍA RÁPIDA - PASOS PARA SOLUCIONAR (3 minutos)

## 🔴 PROBLEMA ACTUAL

```
Spring Security bloquea /mapa/api/puntos-eca (requiere autenticación)
JavaScript no puede cargar datos → SyntaxError: <!DOCTYPE no es JSON
```

## 🟢 SOLUCIÓN

Agregar **una línea** en `SecurityConfig.java` para permitir acceso público.

---

## 📋 PASOS EXACTOS

### PASO 1: Abrir archivo

```
Archivo: src/main/java/org/sena/inforecicla/config/SecurityConfig.java
```

### PASO 2: Buscar línea

Buscar esta línea (aproximadamente línea 59):
```java
.requestMatchers("/", "/inicio", "/publicaciones", "/mapa").permitAll()
```

### PASO 3: Agregar debajo

Agregue esta línea exactamente después:
```java
.requestMatchers("/mapa/api/**").permitAll()
```

### Resultado debe verse así:
```java
.requestMatchers("/", "/inicio", "/publicaciones", "/mapa").permitAll()
.requestMatchers("/mapa/api/**").permitAll()  // ← NUEVA LÍNEA
.requestMatchers("/login", "/registro/**").permitAll()
```

### PASO 4: Compilar

```bash
cd /home/rorschard/Documents/Java/Inforecicla
mvn clean compile
```

Esperar hasta que termine (sin errores)

### PASO 5: Detener app

En terminal donde corre Spring:
```
Ctrl + C
```

### PASO 6: Reiniciar

```bash
mvn spring-boot:run
```

Esperar a ver:
```
Tomcat started on port(s): 8080
```

### PASO 7: Refrescar navegador

```
http://localhost:8080/mapa
```

Presionar: `F5`

### PASO 8: Verificar

Abrir console del navegador: `F12`

Buscar estos logs (en verde ✅):
```
✅ Mapa Leaflet creado
📍 Cargando puntos ECA...
📡 Response status: 200
✅ [N] puntos ECA cargados
```

---

## ✅ SI FUNCIONÓ

- ✅ Mapa aparece
- ✅ Puntos verdes visibles
- ✅ Sidebar con lista
- ✅ No hay errores rojos
- ✅ Búsqueda funciona

## ❌ SI SIGUE FALLANDO

```bash
# Verificar que el cambio fue compilado
grep -n "mapa/api" src/main/java/org/sena/inforecicla/config/SecurityConfig.java

# Debe mostrar la línea con permitAll()
```

Si no muestra la línea, la compilación no incluyó el cambio.
Prueba de nuevo desde PASO 1.

---

## 🔑 LO IMPORTANTE

**Una sola línea hace la diferencia**:

```java
.requestMatchers("/mapa/api/**").permitAll()
```

Esta línea dice: "Permitir acceso público a `/mapa/api/**`"

Sin esta línea → Spring Security bloquea → HTML de login devuelto → JSON error  
Con esta línea → Acceso público → JSON válido → Mapa funciona ✅

---

**Duración**: ~5 minutos  
**Dificultad**: ⭐ Muy fácil  
**Resultado**: 100% funcional

