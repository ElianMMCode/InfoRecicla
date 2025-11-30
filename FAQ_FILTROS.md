# ❓ FAQ - Preguntas Frecuentes sobre la Barra de Búsqueda y Filtros

## 🔍 Preguntas Generales

### P1: ¿Cómo funciona la búsqueda?
**R:** La búsqueda es **en tiempo real**. Mientras escribes en el campo de búsqueda, el sistema filtra automáticamente los materiales que contengan el texto que escribiste. No es sensible a mayúsculas/minúsculas.

```
Ejemplo:
- Escribes: "plás"
- Se muestran: "Plástico", "Plástico PET", "Plástico LDPE"
- Se ocultan: "Papel", "Vidrio", "Aluminio"
```

---

### P2: ¿Qué pasa si dejo el campo de búsqueda vacío?
**R:** Se mostrarán todos los materiales (sin filtro de búsqueda). Si tienes filtros avanzados activos, seguirá aplicándolos.

```
Búsqueda vacía + Filtro Estado = "Crítico"
= Mostrará TODOS los materiales en estado crítico
```

---

### P3: ¿Puedo combinar múltiples filtros?
**R:** **Sí**, es recomendado. Todos los filtros se aplican con lógica AND (deben cumplir TODOS).

```
Búsqueda: "Plástico"
+ Estado: "Alerta"  
+ Stock: "Alto"
+ Ocupación: "50-75%"

= Solo plásticos que CUMPLAN LOS 4 CRITERIOS
```

---

### P4: ¿Qué significa "Mostrando 5 de 20"?
**R:** 
- **5** = Materiales que coinciden con tu búsqueda/filtros
- **20** = Total de materiales en el inventario

```
Si tienes 20 materiales y buscas "Papel":
Mostrando 3 de 20  ← Hay 3 papeles, el resto se oculta
```

---

## 🎚️ Preguntas sobre Filtros

### P5: ¿Qué diferencia hay entre "Ocupación" y "Stock"?
**R:** 
- **Ocupación** = Porcentaje (relativo a la capacidad)
- **Stock** = Categoría absoluta (bajo/medio/alto)

```
Material A: 50kg de 100kg = 50% Ocupación = Stock "Medio"
Material B: 50kg de 500kg = 10% Ocupación = Stock "Bajo"

Mismo stock absoluto, diferentes ocupaciones
```

---

### P6: ¿Cómo se calculan los rangos de Stock?
**R:** Se basan en el porcentaje de ocupación:

```
Vacío:   0% - 10%  (Casi sin stock)
Bajo:   10% - 33%  (Menos de 1/3)
Medio:  33% - 66%  (Entre 1/3 y 2/3)
Alto:   66% - 100% (Más de 2/3)
```

---

### P7: ¿Qué colores significan en las tarjetas?
**R:** Los colores indican el estado de ocupación:

```
🟢 Verde  = OK        (< Umbral de Alerta)
🟡 Amarillo = Alerta  (> Umbral de Alerta, < Umbral Crítico)
🔴 Rojo   = Crítico   (> Umbral Crítico)
```

---

### P8: ¿Puedo filtrar por rango de precios?
**R:** No en esta versión. Los filtros actuales son:
- Estado
- Unidad de Medida
- Ocupación
- Stock

Si necesitas filtrar por precio, será una mejora futura.

---

## 🐛 Problemas y Soluciones

### Problema 1: Los filtros no funcionan
**Síntomas:** Selecciono un filtro y nada cambia

**Soluciones:**
1. Verifica que Bootstrap esté cargado (inspecciona F12)
2. Comprueba que no haya errores en la consola (F12 > Console)
3. Recarga la página (Ctrl+F5 para caché limpia)
4. Prueba un navegador diferente

```javascript
// En la consola, verifica:
console.log(document.querySelectorAll('.tarjeta-material').length);
// Debe mostrar el número de materiales
```

---

### Problema 2: La búsqueda es muy lenta
**Síntomas:** Lag al escribir, interface se congela

**Causas:**
- Hay muchos materiales (>1000)
- Navegador lento
- Mucha carga del sistema

**Soluciones:**
1. **Temporal:** Limita la búsqueda (ej: muestra 100 de 5000)
2. **Definitivo:** Implementar búsqueda en Backend (ver EJEMPLO_BACKEND_FILTROS.java)

---

### Problema 3: Las tarjetas desaparecen al filtrar
**Síntomas:** Busco "Papel" y desaparece todo

**Causas Posibles:**
- No hay materiales con ese nombre
- Hay un error en la búsqueda

**Comprobación:**
```
1. Abre las herramientas del navegador (F12)
2. Ve a Console
3. Escribe: document.querySelectorAll('[data-nombre-material]').length
4. Te mostrará cuántos materiales hay
```

---

### Problema 4: El botón "Limpiar" no funciona
**Síntomas:** Hago clic y nada pasa

**Solución:**
```javascript
// Verifica en la consola que exista el elemento:
document.getElementById('btnLimpiarBusqueda')
// Debe retornar el elemento del DOM
```

---

### Problema 5: Los estilos CSS no se aplican
**Síntomas:** Los colores/bordes de las tarjetas no se ven

**Soluciones:**
1. Limpia caché: Ctrl+Shift+Del (selecciona Imágenes en caché, Cookies, etc)
2. Recarga: Ctrl+Shift+R
3. Abre DevTools (F12) y verifica si hay errores CSS

---

### Problema 6: Mensaje "No se encontraron materiales" falso
**Síntomas:** Hay materiales pero muestra el mensaje

**Causas:**
- Los datos no coinciden exactamente
- Hay espacios en blanco extra
- Mayúscula/minúscula no coincide

**Nota:** La búsqueda ignora mayúsculas, pero verifica espacios

```
Busca: "Plás"
NO encuentra: "Plástico " (con espacio al final)
```

---

## 📱 Preguntas sobre Responsividad

### P9: ¿Funciona en móvil?
**R:** **Sí**, está optimizado para todos los dispositivos:

```
Móvil (< 576px):      1 columna de tarjetas, filtros apilados
Tablet (576-992px):   2 columnas, filtros en 2 filas
Desktop (> 992px):    3 columnas, filtros en 1 fila
```

---

### P10: ¿Por qué se ve diferente en mi teléfono?
**R:** Bootstrap adapta la interfaz al tamaño de pantalla:
- Búsqueda siempre 100% ancho
- Filtros se reorganizan automáticamente
- Tarjetas se apilan en menos columnas

Es el comportamiento esperado. 📱✅

---

## 🚀 Preguntas sobre Próximos Pasos

### P11: ¿Cómo agrego más filtros?
**R:** Requiere cambios en:

1. **HTML:** Agregar nuevo `<select>` en el panel de filtros
2. **JavaScript:** Agregar validación en `aplicarFiltros()`
3. **Atributos:** Agregar `data-` a las tarjetas

```html
<!-- Ejemplo: Filtro por Proveedor -->
<div class="col-md-6 col-lg-3">
    <select id="filtroProveedor" class="form-select form-select-sm filtro-select">
        <option value="">Todos los proveedores</option>
        <option value="proveedor1">Proveedor 1</option>
        <option value="proveedor2">Proveedor 2</option>
    </select>
</div>
```

---

### P12: ¿Cómo conecto esto al Backend?
**R:** Ver archivo `EJEMPLO_BACKEND_FILTROS.java` que incluye:

1. Controlador con endpoint `/punto-eca/{id}/materiales/buscar`
2. Servicio con lógica de filtrado
3. DTO para retornar JSON
4. Ejemplo de AJAX para el cliente

---

### P13: ¿Puedo guardar mis filtros favoritos?
**R:** No en esta versión, pero puedes:

1. **LocalStorage:** Guardar filtros en el navegador (cliente)
2. **Backend:** Guardar preferencias en BD (servidor)

---

## 💾 Preguntas sobre Datos

### P14: ¿Dónde se almacenan los datos de los materiales?
**R:** En memoria del navegador (cliente).
- Se cargan cuando la página se abre
- Se refrescan si recarga la página
- NO se guardan localmente

Para persistencia, se necesita Backend.

---

### P15: ¿Puedo exportar los resultados filtrados?
**R:** No en esta versión, pero es fácil de agregar:

```javascript
// Ejemplo: Exportar a CSV
function exportarResultados() {
    const materiales = document.querySelectorAll('.tarjeta-material:not([style*="display: none"])');
    // ... generar CSV
}
```

---

## 🔒 Preguntas sobre Seguridad

### P16: ¿Es seguro el filtrado en el cliente?
**R:** Para búsqueda simple, sí. Pero ten en cuenta:

**Ventajas:**
- ✅ No requiere servidor
- ✅ Muy rápido
- ✅ Funciona sin conexión

**Desventajas:**
- ❌ Visible el código en el navegador
- ❌ Posible ver datos ocultos (ver página HTML)
- ❌ No es auditado

**Recomendación:** Para datos sensibles, implementar en Backend.

---

### P17: ¿Puedo ver datos que no debería ver?
**R:** 
- En la página HTML: Sí (F12 > Elements)
- En la lógica JavaScript: Sí (F12 > Sources)

Si los datos son confidenciales, **deben filtrarse en el servidor**.

---

## 📊 Preguntas sobre Rendimiento

### P18: ¿Cuántos materiales puede manejar?
**R:** Depende de tu navegador y dispositivo:

```
Móvil moderno:   100-500 materiales ✓
Móvil antiguo:    50-100 materiales ⚠️
Desktop:         1000+ materiales ✓
```

Si tienes más de 1000, usa Backend con paginación.

---

### P19: ¿Por qué se congela con muchos materiales?
**R:** El navegador está recorriendo todas las tarjetas para filtrar.

**Solución:**
```javascript
// Implementar debouncing (agregar delay)
let timerFiltro;
busquedaInput.addEventListener('input', function() {
    clearTimeout(timerFiltro);
    timerFiltro = setTimeout(aplicarFiltros, 300); // 300ms delay
});
```

---

## 🎓 Preguntas de Aprendizaje

### P20: ¿Dónde está el código JavaScript?
**R:** Al final del archivo `section-materiales.html`:

```
Línea ~400: <!-- Script para manejar el modal de detalles y filtros -->
```

Dentro del `<script>` encontrarás:
1. Lógica del modal
2. Manejo de eventos
3. Función `aplicarFiltros()`
4. Estilos CSS generados dinámicamente

---

### P21: ¿Cómo agregar console.log para debuggear?
**R:** Abre las herramientas del navegador (F12) y ve a Console:

```javascript
// Ejemplo: Ver qué tarjetas se muestran
document.querySelectorAll('[style*="display"]:not([style*="display: none"])').forEach(t => {
    console.log(t.getAttribute('data-nombre-material'));
});
```

---

## 📞 ¿No encuentras tu pregunta?

Si tu pregunta no está aquí:

1. **Abre DevTools** (F12) y mira la Console
2. **Copia el error** completo
3. **Busca el error** en StackOverflow o Google
4. **Revisa el código** en `section-materiales.html`

---

## ✅ Checklist de Instalación

Si todo funciona, deberías ver:

- ✅ Campo de búsqueda visible
- ✅ Botón "Limpiar" funciona
- ✅ "Filtros Avanzados" se expande
- ✅ Filtros cambian el contenido
- ✅ Badge "Mostrando X de Y" aparece
- ✅ Tarjetas tienen borde coloreado
- ✅ No hay errores en Console (F12)

Si falta algo, revisa los pasos de instalación en `RESUMEN_CAMBIOS.md`

---

**¡Espero haber resuelto tus dudas! 🎉**

