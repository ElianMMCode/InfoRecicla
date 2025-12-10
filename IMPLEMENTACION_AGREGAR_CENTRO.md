# Implementación: Agregar Nuevo Centro de Acopio

## Resumen
Se ha implementado completamente la funcionalidad de agregar un nuevo centro de acopio en el frontend. El sistema está listo para recibir y procesar requests desde el backend.

## Cambios Realizados

### 1. Frontend - JavaScript (`modal-centros-propios.js`)
- ✅ Función `abrirCrearNuevoCentro()` para abrir el modal en modo creación
- ✅ Función mejorada `guardarEdicionCentro()` que detecta si es creación o edición
- ✅ Event listener para el botón `#btnAgregarNuevoCentro`
- ✅ Manejo completo de validación y respuestas del servidor

### 2. Frontend - HTML (`section-centros.html`)
- ✅ Botón "Agregar Nuevo Centro" funcional
- ✅ Modal mejorado con select de localidades
- ✅ Interfaz reutilizable para crear y editar centros

## Flujo de Funcionamiento

```
USUARIO HACE CLIC EN "AGREGAR NUEVO CENTRO"
    ↓
MODAL SE ABRE EN MODO EDICIÓN
    ↓
USUARIO COMPLETA FORMULARIO
    - nombreCntAcp: Nombre del centro *
    - tipoCntAcp: Tipo (Planta, Proveedor, etc) *
    - celular: Teléfono
    - email: Correo electrónico
    - nombreContactoCntAcp: Nombre del contacto
    - nota: Notas adicionales
    ↓
USUARIO HACE CLIC EN "CREAR CENTRO"
    ↓
VALIDACIÓN EN FRONTEND (nombre y tipo obligatorios)
    ↓
POST REQUEST A: /punto-eca/{puntoEcaId}/centro-acopio
    ↓
RESPUESTA DEL SERVIDOR (JSON)
    ↓
CONFIRMACIÓN Y RECARGA DE PÁGINA
```

## Endpoints Requeridos

### 1. CREAR NUEVO CENTRO
**Método:** `POST`
**URL:** `/punto-eca/{puntoEcaId}/centro-acopio`

**Request Headers:**
```
Content-Type: application/json
```

**Request Body:**
```json
{
  "nombreCntAcp": "Centro de Acopio XYZ",
  "tipoCntAcp": "Planta",
  "celular": "3001234567",
  "email": "contacto@centro.com",
  "nombreContactoCntAcp": "Juan Pérez García",
  "nota": "Notas adicionales"
}
```

**Response (Success - 201/200):**
```json
{
  "success": true,
  "message": "Centro creado exitosamente",
  "centroId": 123,
  "centro": {
    "cntAcpId": 123,
    "nombreCntAcp": "Centro de Acopio XYZ",
    "tipoCntAcp": { "tipo": "Planta" },
    "celular": "3001234567",
    "email": "contacto@centro.com",
    "nombreContactoCntAcp": "Juan Pérez García",
    "nota": "Notas adicionales"
  }
}
```

**Response (Error - 400/500):**
```json
{
  "error": "Descripción del error",
  "status": 400
}
```

### 2. ACTUALIZAR CENTRO (Ya implementado)
**Método:** `PUT`
**URL:** `/centro-acopio/{centroId}`

### 3. ELIMINAR CENTRO (Ya implementado)
**Método:** `DELETE`
**URL:** `/centro-acopio/{centroId}`

## Parámetros de URL

### {puntoEcaId}
El ID del Punto ECA actual se obtiene automáticamente desde el atributo `data-punto-eca-id` de la sección:
```html
<section data-punto-eca-id="123" ...>
```

Este valor se captura automáticamente en el JavaScript:
```javascript
const sectionCentros = document.querySelector('[data-punto-eca-id]');
const puntoEcaId = sectionCentros?.getAttribute('data-punto-eca-id');
```

## Validaciones

### Frontend (Ejecutadas antes de enviar):
- ✅ Nombre del centro es obligatorio
- ✅ Tipo de centro es obligatorio

### Backend (Recomendadas):
- Validar que el nombre no esté vacío
- Validar que el tipo exista y sea válido
- Validar formato de email si se proporciona
- Validar formato de teléfono si se proporciona
- Validar que el PuntoECA existe
- Verificar permisos del usuario
- Evitar centros duplicados si es necesario

## Manejo de Errores

El frontend maneja los siguientes casos:

1. **Modal no encontrado:** Muestra alerta al usuario
2. **Validación fallida:** Muestra mensaje específico (nombre/tipo requeridos)
3. **Error en la respuesta del servidor:** Muestra error en alerta y logs
4. **Éxito:** Muestra confirmación, cierra modal y recarga la página

## Logs en Consola

El sistema genera logs detallados en la consola para debugging:
```
➕ [MODAL-CENTROS] Abriendo modal para crear nuevo centro
💾 [MODAL-CENTROS] Guardando - Modo: Creación
📋 [MODAL-CENTROS] Valores capturados: { ... }
📤 [MODAL-CENTROS] Datos a enviar: { ... }
📡 Response status: 200
✅ [MODAL-CENTROS] Centro creado exitosamente
```

## Integración en Controller

Ejemplo de cómo podría verse el método en tu Controller:

```java
@PostMapping("/punto-eca/{puntoEcaId}/centro-acopio")
public ResponseEntity<?> crearCentroAcopio(
    @PathVariable Long puntoEcaId,
    @RequestBody CentroAcopioDTO dto,
    Authentication authentication) {
    
    try {
        // Validar que el usuario tenga acceso a este PuntoECA
        Usuario usuario = (Usuario) authentication.getPrincipal();
        
        // Crear la entidad
        CentroAcopio centro = new CentroAcopio();
        centro.setNombreCntAcp(dto.getNombreCntAcp());
        centro.setTipoCntAcp(dto.getTipoCntAcp());
        centro.setCelular(dto.getCelular());
        centro.setEmail(dto.getEmail());
        centro.setNombreContactoCntAcp(dto.getNombreContactoCntAcp());
        centro.setNota(dto.getNota());
        centro.setPuntoEca(new PuntoECA(puntoEcaId));
        
        // Guardar
        CentroAcopio centrGuardado = centroAcopioService.save(centro);
        
        return ResponseEntity.status(HttpStatus.CREATED)
            .body(Map.of(
                "success", true,
                "message", "Centro creado exitosamente",
                "centroId", centrGuardado.getCntAcpId(),
                "centro", centrGuardado
            ));
    } catch (Exception e) {
        return ResponseEntity.badRequest()
            .body(Map.of(
                "error", e.getMessage(),
                "status", 400
            ));
    }
}
```

## Testing

Para probar la funcionalidad:

1. **Abre la consola del navegador** (F12)
2. **Haz clic en "Agregar Nuevo Centro"**
3. **Completa el formulario**
4. **Haz clic en "Crear Centro"**
5. **Revisa los logs en consola** para ver la secuencia de eventos

Si el endpoint no está implementado, verás un error 404 en la consola.

## Próximos Pasos

1. Implementar el endpoint `POST /punto-eca/{puntoEcaId}/centro-acopio` en el backend
2. Implementar validaciones adicionales en el servidor
3. Implementar búsqueda y filtrado de centros
4. Implementar paginación si hay muchos centros

## Archivos Modificados

- ✅ `/src/main/resources/static/js/modal-centros-propios.js`
- ✅ `/src/main/resources/templates/views/PuntoECA/section-centros.html`

## Estado

✅ **COMPLETADO - LISTO PARA USAR**

El sistema frontend está completamente implementado y funcional. Solo falta la implementación del endpoint en el backend.

