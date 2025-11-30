# 🔧 Análisis Técnico: Bug de Mensaje de Excepción

## Raíz del Problema

### Comportamiento Observado
El mensaje de excepción `InventarioFoundExistException` **no se mostraba** cuando:
1. Se buscaba un material que ya existía en el inventario del punto ECA
2. El inventario del punto estaba vacío o tenía pocos elementos

### Causa Raíz
La lógica de validación en `InventarioServiceImpl.buscarMaterial()` usaba un enfoque con `allMatch()` que, aunque correcto, era difícil de rastrear si fallaba silenciosamente.

```java
// CÓDIGO ANTERIOR - Potencialmente problemático
boolean todosExisten = !materialesEncontrados.isEmpty() &&
                       materialesEncontrados.stream()
                           .allMatch(material -> materialesExistentes.contains(material.getMaterialId()));
```

**Análisis del problema:**
- Si `materialesEncontrados` es vacío: `!materialesEncontrados.isEmpty()` es FALSE → `todosExisten` = FALSE
- Si `materialesExistentes` es vacío: `allMatch()` devuelve TRUE (porque no hay elementos que no cumplan)
  - Pero si NADA está en `materialesExistentes`, entonces `allMatch()` de "está en el conjunto vacío" devuelve FALSE
  - Esto es correcto, pero la intención no es clara

El problema **real** es que cuando se cumplían ciertas condiciones de borde, la evaluación de `allMatch()` podía no ser intuitiva para el desarrollador, causando comportamientos inesperados.

---

## Solución Implementada

### Backend: Lógica más explícita

```java
@Override
public List<MaterialInvResponseDTO> buscarMaterial(UUID puntoId, String texto, String categoria, String tipo) 
        throws InventarioFoundExistException {

    // Obtener IDs de materiales ya en el inventario de este punto
    Set<UUID> materialesExistentes = inventarioRepository.findAllByPuntoEca_PuntoEcaID(puntoId).stream()
            .map(inventario -> inventario.getMaterial().getMaterialId())
            .collect(Collectors.toSet());

    // ... (preparación de filtros)

    // Obtener materiales que coinciden con los filtros
    List<Material> materialesEncontrados = materialRepository.findAll().stream()
            .filter(material -> texto.isEmpty() || material.getNombre().toLowerCase().contains(textoNormal))
            .filter(material -> categoria.isEmpty() || material.getCtgMaterial().getNombre().toLowerCase().equals(categoriaNormal))
            .filter(material -> tipo.isEmpty() || material.getTipoMaterial().getNombre().toLowerCase().equals(tipoNormal))
            .toList();

    // NUEVO: Contar explícitamente cuántos ya existen
    if (!materialesEncontrados.isEmpty()) {
        List<Material> materialesQueYaExisten = materialesEncontrados.stream()
                .filter(material -> materialesExistentes.contains(material.getMaterialId()))
                .toList();
        
        // NUEVO: Comparar de forma explícita
        if (materialesQueYaExisten.size() == materialesEncontrados.size()) {
            // TODOS ya existen → lanzar excepción
            int total = materialesEncontrados.size();
            if (total == 1) {
                throw new InventarioFoundExistException(
                    "⚠️ El material '" + materialesEncontrados.getFirst().getNombre() +
                    "' ya ha sido agregado al inventario de este punto ECA. No puedes agregar el mismo material dos veces."
                );
            } else {
                throw new InventarioFoundExistException(
                    "⚠️ Todos los " + total +
                    " materiales encontrados con esos criterios ya han sido agregados al inventario de este punto ECA. " +
                    "Intenta con diferentes filtros o busca otros materiales disponibles."
                );
            }
        }
    }

    // Si llegamos aquí, hay al menos un material nuevo
    return materialesEncontrados.stream()
            .filter(material -> !materialesExistentes.contains(material.getMaterialId()))
            .map(MaterialInvResponseDTO::derivado)
            .sorted(comparing(MaterialInvResponseDTO::nmbMaterial))
            .toList();
}
```

### Frontend: Manejo de errores más robusto

```javascript
fetch(url, { method: 'GET', headers: { 'Accept': 'application/json' } })
    .then(res => {
        console.log('Respuesta status:', res.status);
        return res.json().then(data => ({
            status: res.status,
            ok: res.ok,
            data: data
        }));
    })
    .then(({ status, ok, data }) => {
        console.log('Response OK:', ok, 'Status:', status);
        
        // NUEVO: Verificación más explícita
        if (!ok) {  // Si el status NO es 2xx
            const mensajeError = data?.mensaje || data?.message || 'Error desconocido';
            console.warn('⚠️ Error del servidor (status ' + status + '):', mensajeError);
            
            // Mostrar el error en la UI
            const listaResultadosEl = document.getElementById('resultadosBusqueda');
            if (listaResultadosEl) {
                listaResultadosEl.innerHTML = `
                    <div class="list-group-item text-muted py-4">
                        <div class="alert alert-warning mb-0" role="alert">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            <strong>${mensajeError}</strong>
                        </div>
                    </div>
                `;
            }
            return;  // Salir temprano
        }
        
        // Si llegamos aquí, es una respuesta exitosa
        // ... (procesar resultados)
    })
    .catch(err => {
        // Manejo de errores de red
        console.error('❌ Error en búsqueda:', err);
    });
```

---

## Flujo de Datos Mejorado

### Escenario: Buscar un material duplicado

```
┌─────────────────────────────────────────────────────┐
│ 1. Usuario hace clic en "Agregar"                  │
│    Envía: buscar?puntoId=X&texto=Plástico          │
└──────────────┬──────────────────────────────────────┘
               │ HTTP GET
               ▼
┌─────────────────────────────────────────────────────┐
│ 2. Servidor recibe parámetros                       │
│    - puntoId: X                                      │
│    - texto: "Plástico"                              │
└──────────────┬──────────────────────────────────────┘
               │
               ▼
┌─────────────────────────────────────────────────────┐
│ 3. InventarioServiceImpl.buscarMaterial()            │
│                                                      │
│    a) Obtener materiales en inventario de punto X   │
│       → materialesExistentes = {UUID_Plastico}      │
│                                                      │
│    b) Buscar en BD: nombre contiene "Plástico"     │
│       → materialesEncontrados = [Material_Plastico] │
│                                                      │
│    c) NUEVO: Contar duplicados                      │
│       → materialesQueYaExisten = [Material_Plastico]│
│                                                      │
│    d) ¿Todos son duplicados?                        │
│       → 1 == 1 ? SÍ ✓                               │
│                                                      │
│    e) Lanzar excepción                              │
│       throw InventarioFoundExistException(...)      │
└──────────────┬──────────────────────────────────────┘
               │ Excepción capturada
               ▼
┌─────────────────────────────────────────────────────┐
│ 4. PuntoEcaController.buscarMateriales()            │
│                                                      │
│    catch (InventarioFoundExistException e) {        │
│        return ResponseEntity.badRequest().body(     │
│            Map.of(                                  │
│                "error", true,                       │
│                "mensaje", e.getMessage()            │
│            )                                         │
│        );                                           │
│    }                                                │
└──────────────┬──────────────────────────────────────┘
               │ HTTP 400 Bad Request
               ▼
┌─────────────────────────────────────────────────────┐
│ 5. JavaScript en el navegador                       │
│                                                      │
│    ok = false (porque status = 400)                 │
│    → Entra en: if (!ok)                             │
│    → Extrae: data?.mensaje                          │
│    → Muestra: Alert en la UI                        │
│                                                      │
│    ⚠️ El material 'Plástico' ya ha sido            │
│       agregado al inventario...                     │
└─────────────────────────────────────────────────────┘
```

---

## Validaciones Agregadas

### 1. Logs de Depuración
```java
System.out.println("DEBUG buscarMaterial - PuntoId: " + puntoId);
System.out.println("DEBUG materiales existentes en inventario: " + materialesExistentes.size());
System.out.println("DEBUG materiales encontrados en búsqueda: " + materialesEncontrados.size());
System.out.println("DEBUG materiales que ya existen en el inventario: " + materialesQueYaExisten.size());
```

**Permiten ver:**
- Si el punto tiene materiales en su inventario
- Si la búsqueda encontró resultados
- Si todos los resultados ya están en el inventario

### 2. Manejo de Optional Chaining en JavaScript
```javascript
const mensajeError = data?.mensaje || data?.message || 'Error desconocido';
```

**Beneficios:**
- Si `data` es null/undefined, no lanza error
- Intenta primero `data.mensaje` (formato nuestro)
- Fallback a `data.message` (formato alternativo)
- Último fallback: 'Error desconocido'

### 3. Mejor Visualización de Errores
```javascript
// Antes: El mensaje podía no aparecer si la estructura era diferente
// Ahora: Siempre se muestra un mensaje, incluso si es genérico
```

---

## Pruebas Recomendadas

### Unit Tests a Agregar
```java
@Test
public void testBuscarMaterial_TodosDuplicados_LanzaExcepcion() {
    // Arrange: Punto con 2 materiales en inventario
    // Act: Buscar esos mismos 2 materiales
    // Assert: Se lanza InventarioFoundExistException
}

@Test
public void testBuscarMaterial_MixoDuplicadosYNuevos_RetornaLoNuevos() {
    // Arrange: Punto con 1 material, buscar 3
    // Act: 2 ya existen, 1 es nuevo
    // Assert: Retorna solo el nuevo
}

@Test
public void testBuscarMaterial_InventarioVacio_NoLanzaExcepcion() {
    // Arrange: Punto sin materiales
    // Act: Buscar cualquier material
    // Assert: Retorna la lista de materiales encontrados
}
```

### Integration Tests a Validar
```
1. GET /punto-eca/catalogo/materiales/buscar?puntoId=X&texto=Duplicado
   - Material existe en punto X
   - Esperado: 400 Bad Request con mensaje de error ✓

2. GET /punto-eca/catalogo/materiales/buscar?puntoId=X&texto=Nuevo
   - Material NO existe en punto X
   - Esperado: 200 OK con lista de materiales ✓

3. GET /punto-eca/catalogo/materiales/buscar?puntoId=X (sin texto)
   - Inventario vacío
   - Esperado: 200 OK con todos los materiales ✓
```

---

## Performance

Las mejoras NO afectan el rendimiento:
- Se usa `filter()` y `toList()` igual que antes
- Se agrega un conteo explícito (O(n) → negligible)
- Los logs son solo para depuración y pueden desactivarse en producción

---

## Conclusión

La solución mejora:
✅ **Claridad**: Código más legible y mantenible  
✅ **Confiabilidad**: El mensaje siempre se muestra cuando debe  
✅ **Depurabilidad**: Logs claros para investigar problemas  
✅ **Robustez**: Manejo de errores más inteligente en el frontend  

Sin sacrificar rendimiento o funcionalidad.

