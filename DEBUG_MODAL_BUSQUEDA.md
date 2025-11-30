# 🔍 GUÍA DE DEPURACIÓN: MODAL DE BÚSQUEDA Y FILTROS

## ✅ Lo que he arreglado:

He cambiado el event listener del botón "Aplicar Filtros" de un listener directo a un **event delegation listener global**. Esto asegura que funcione incluso si el elemento se carga después del script.

---

## 🧪 CÓMO PROBAR QUE AHORA FUNCIONA:

### **Paso 1: Abre DevTools**
- Presiona `F12` en tu navegador
- Ve a la pestaña **Console**

### **Paso 2: Navega a la página**
- Abre la página con los Puntos ECA → Materiales
- Expande el acordeón "Materiales en Inventario"
- Expande "Búsqueda y Filtros"

### **Paso 3: Verifica los logs iniciales**
En la **Console** deberías ver:
```
📌 Script inline de búsqueda ejecutándose...
✓ Elementos encontrados: { ... }
✓ Handler APLICAR FILTROS vinculado (event delegation)
📌 Script inline completado ✓
```

Si **NO ves** estos logs, recarga la página (Ctrl+R).

---

## 🧪 AHORA PRUEBA A HACER CLICK EN "APLICAR":

### **Paso 1: Llenar un filtro (opcional)**
- Escribe algo en "Buscar Material" O
- Selecciona una categoría O
- Selecciona un tipo, etc.

### **Paso 2: Presiona "Aplicar"**

### **Paso 3: Revisa la Console**

**Deberías ver esta secuencia de logs:**

```
🔎 Click en APLICAR filtros (Inventario)
Filtros inventario aplicados: { texto: "", categoria: "", tipo: "", estado: "", unidad: "", ocupacion: "", stock: "" }
Valores individuales: busquedaMaterial= [valor] filtroCategoria= [valor]
puntoId obtenido: [UUID-del-punto-eca]
🚀 Enviando petición GET a: /punto-eca/catalogo/materiales/buscar?puntoId=...
✓ Respuesta recibida - Status: 200
📦 Datos recibidos: [...array de materiales...]
Response OK: true Status: 200
✓ Array de materiales recibido, cantidad: [número]
```

---

## ❌ SI VES ERROR "puntoId no disponible":

Significa que el input hidden `#agregarPuntoId` NO tiene valor. 

**Solución:**
```javascript
// En la Console, ejecuta:
document.getElementById('agregarPuntoId').value
```

Si devuelve vacío o `undefined`, el problema está en la plantilla Thymeleaf. Verifica que el usuario esté autenticado.

---

## ❌ SI VES ERROR DE CONEXIÓN:

Significa que el backend NO está devolviendo la respuesta correctamente.

**Verifica:**

1. **En DevTools → Network**:
   - Busca la petición GET a `/punto-eca/catalogo/materiales/buscar`
   - Mira el **Status Code** (debe ser 200)
   - Mira el **Response** (debe ser un array JSON)

2. **Ejemplo de Response correcto:**
```json
[
  {
    "materialId": "uuid-del-material",
    "nmbMaterial": "Nombre del Material",
    "dscMaterial": "Descripción",
    "nmbCategoria": "Categoría",
    "dscCategoria": "Desc categoría",
    "nmbTipo": "Tipo",
    "dscTipo": "Desc tipo"
  }
]
```

3. **Si el Status es 400 o 500**:
   - Mira el **Response** para ver el mensaje de error
   - Ese mensaje aparecerá en el modal

---

## 🔍 VERSIÓN COMPLETA DEL TEST EN CONSOLA:

Copia y pega esto en la **Console** para simular un click:

```javascript
// Simular click en el botón (opcional, para testing manual)
document.getElementById('btnAplicarFiltros').click();
```

---

## 📊 CHECKLIST DE DEPURACIÓN:

- [ ] ¿Se ve el log "🔎 Click en APLICAR filtros"?
- [ ] ¿Se ve el log "🚀 Enviando petición GET"?
- [ ] ¿La URL contiene `puntoId=` con un UUID válido?
- [ ] ¿En Network ves la petición GET a `/punto-eca/catalogo/materiales/buscar`?
- [ ] ¿La respuesta es Status 200?
- [ ] ¿El Response es un array JSON válido?
- [ ] ¿Se abre el modal con los resultados?

---

## 💡 PRÓXIMAS ACCIONES:

1. **Ejecuta un test** siguiendo los pasos arriba
2. **Captura los logs** de la Console
3. **Captura la petición** en Network (Request y Response)
4. **Comparte conmigo los logs** y el Response

Con eso podré identificar exactamente dónde está el problema.

---

## 🎯 RESUMEN DEL FLUJO:

```
Usuario selecciona filtros
        ↓
Usuario presiona "Aplicar"
        ↓
[EVENT DELEGATION DISPARA] document.addEventListener('click', ...)
        ↓
Se recopilan filtros de los inputs
        ↓
Se construye URL con parámetros GET
        ↓
Se envía fetch GET a /punto-eca/catalogo/materiales/buscar
        ↓
Backend devuelve array de materiales
        ↓
Frontend renderiza lista en modal
        ↓
Usuario selecciona material
```
