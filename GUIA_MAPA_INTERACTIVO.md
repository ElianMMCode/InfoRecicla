# 🗺️ GUÍA DE USO - VISTA DE MAPA INTERACTIVO DE PUNTOS ECA

## 📋 Descripción General

Se ha implementado una vista de mapa interactivo que permite a todos los usuarios (sin requerimientos de autenticación específicos) visualizar los puntos ECA registrados en la base de datos, con su ubicación geográfica en un mapa y una lista lateral sincronizada.

## 🎯 Características Principales

✅ **Mapa Interactivo**: Visualización de puntos ECA usando Leaflet.js  
✅ **Lista Lateral**: Listado completo de puntos ECA sincronizado con el mapa  
✅ **Búsqueda**: Filtrar puntos por nombre o localidad  
✅ **Sincronización**: Click en lista actualiza mapa y viceversa  
✅ **Información Completa**: Nombre, dirección, teléfono, email, horarios  
✅ **Responsive**: Funciona en escritorio, tablet y mobile  
✅ **Agrupación Automática**: Marcadores se agrupan cuando hay muchos puntos  

## 📁 Archivos Creados

### Backend
```
src/main/java/org/sena/inforecicla/
├── controller/
│   └── MapaController.java          (Nuevo controlador de mapa)
├── service/
│   ├── PuntoEcaService.java         (Actualizado con nuevos métodos)
│   └── impl/PuntoEcaServiceImpl.java (Actualizado con implementación)
└── dto/puntoEca/
    └── PuntoEcaMapDTO.java          (Nuevo DTO para mapa)
```

### Frontend
```
src/main/resources/
├── templates/views/Mapa/
│   └── mapa-interactivo.html        (Plantilla HTML del mapa)
└── static/
    ├── js/Mapa/
    │   └── mapa-interactivo.js      (Lógica JavaScript del mapa)
    └── css/Mapa/
        └── mapa-interactivo.css     (Estilos del mapa)
```

## 🚀 Cómo Acceder

### Ruta Principal
```
GET /mapa
```
Accede a esta URL desde tu navegador:
```
http://localhost:8080/mapa
```

## 🔌 Endpoints de API

### 1. Obtener todos los puntos ECA (JSON)
```
GET /mapa/api/puntos-eca
```
**Respuesta**: Lista de PuntoEcaMapDTO en formato JSON
```json
[
  {
    "puntoEcaID": "uuid",
    "nombrePunto": "Punto ECA Centro",
    "latitud": 4.7110,
    "longitud": -74.0721,
    "direccion": "Carrera 10 #23-45",
    "ciudad": "Bogotá",
    "localidadNombre": "Chapinero",
    "celular": "3005551234",
    "email": "info@puntoeca.com",
    "descripcion": "Descripción del punto",
    "horarioAtencion": "Lunes a Viernes 8am-5pm"
  }
]
```

### 2. Obtener un punto específico por ID
```
GET /mapa/api/puntos-eca/{puntoEcaId}
```
**Parámetro**: `puntoEcaId` (UUID)  
**Respuesta**: PuntoEcaMapDTO individual

### 3. Buscar puntos por nombre
```
GET /mapa/api/puntos-eca/buscar?termino=Centro
```
**Parámetro**: `termino` (String)  
**Respuesta**: Lista filtrada de PuntoEcaMapDTO

## 🎨 Características del Mapa

### Lado Izquierdo - Mapa
- **Mapa interactivo** con zoom y pan
- **Marcadores personalizados** con icono de hoja
- **Popups informativos** al hacer click en marcadores
- **Agrupación de marcadores** (MarkerCluster) cuando hay muchos puntos
- **Controles flotantes**:
  - 📍 **Centrar**: Centra el mapa en Bogotá
  - 🔄 **Recargar**: Recarga todos los puntos desde el servidor
  - 📋 **Lista** (solo mobile): Muestra/oculta el sidebar

### Lado Derecho - Sidebar
- **Encabezado** con título "Puntos ECA"
- **Buscador** para filtrar puntos por nombre o localidad
- **Contador** mostrando puntos mostrados vs. total
- **Lista de tarjetas** con información de cada punto:
  - Nombre del punto
  - Localidad
  - Dirección
  - Teléfono (clickeable)
  - Email (clickeable)
  - Horario de atención
- **Sincronización**: Click en tarjeta destaca en mapa

## 💻 Uso desde JavaScript

Si necesitas acceder al mapa desde otro script:

```javascript
// La clase MapaInteractivo está disponible globalmente
// Se inicializa automáticamente al cargar la página

// Para interactuar:
// - Buscar: document.getElementById('inputBusqueda').value = 'Chapinero'
// - Recargar: document.getElementById('btnRecargar').click()
// - Centrar: document.getElementById('btnCentrar').click()
```

## 🔐 Permisos de Acceso

- ✅ **Usuarios no autenticados**: Pueden ver el mapa
- ✅ **Usuarios autenticados**: Pueden ver el mapa
- ✅ **Administradores**: Pueden ver el mapa
- ✅ **Ciudadanos**: Pueden ver el mapa
- ✅ **Gestores ECA**: Pueden ver el mapa

**Nota**: Solo se muestran puntos ECA con estado "Activo" y que tengan coordenadas válidas (latitud y longitud).

## 📱 Responsive Design

### Desktop (>1200px)
- Mapa 66% ancho (izquierda)
- Sidebar 34% ancho (derecha)
- Sidebar siempre visible

### Tablet (768px - 1199px)
- Mapa ocupa todo el ancho
- Sidebar ocupa todo el ancho
- Botón "Lista" en el mapa para mostrar/ocultar sidebar

### Mobile (<768px)
- Mapa ocupa mitad de altura
- Sidebar ocupa mitad de altura
- Botón "Lista" en el mapa para expandir sidebar a pantalla completa

## 🛠️ Configuración Personalizada

### Cambiar coordenadas por defecto (en mapa-interactivo.js)
```javascript
this.coordenadasDefecto = {
    latitud: 4.7110,      // Cambiar latitud
    longitud: -74.0721,   // Cambiar longitud
    zoom: 11              // Cambiar zoom inicial
};
```

### Cambiar colores (en mapa-interactivo.css)
```css
:root {
    --color-primario: #2ecc71;      /* Verde */
    --color-secundario: #3498db;    /* Azul */
    --color-acento: #e74c3c;        /* Rojo */
    --color-oscuro: #2c3e50;        /* Gris oscuro */
    --color-claro: #ecf0f1;         /* Gris claro */
}
```

## 🐛 Solución de Problemas

### El mapa no carga
- Verifica la consola del navegador (F12) para errores
- Asegúrate que `/mapa` está accesible
- Verifica que Spring Boot está ejecutándose

### No aparecen los puntos
- Verifica que hay puntos ECA en la base de datos con estado "Activo"
- Verifica que tienen latitud y longitud válidas
- Intenta recargar con el botón "Recargar"

### El mapa no responde
- Recarga la página (F5)
- Limpia el caché del navegador (Ctrl+Shift+Delete)
- Verifica la conexión a internet

## 📊 Estructura de Datos

### PuntoEcaMapDTO
```java
- puntoEcaID: UUID
- nombrePunto: String
- latitud: Double
- longitud: Double
- direccion: String
- ciudad: String
- localidadNombre: String
- celular: String
- email: String
- descripcion: String
- horarioAtencion: String
```

## 🔄 Integración con Otras Vistas

Para agregar un enlace al mapa desde otra página:

```html
<a href="/mapa" class="btn btn-primary">
    <i class="fas fa-map-location-dot"></i> Ver Mapa de Puntos ECA
</a>
```

## 📚 Librerías Utilizadas

- **Leaflet.js**: Mapa interactivo
- **Leaflet MarkerCluster**: Agrupación de marcadores
- **Bootstrap 5**: Estructura y estilos base
- **Font Awesome 6**: Iconos
- **OpenStreetMap**: Tiles del mapa

## 📝 Notas de Desarrollo

- El mapa se recarga completamente cada vez que se hace click en "Recargar"
- La búsqueda es case-insensitive (no distingue mayúsculas)
- Los puntos sin coordenadas se filtran automáticamente
- Los popups se cierran al hacer click en otro marcador
- La clase MapaInteractivo usa Fetch API para las llamadas AJAX

## 🚀 Próximas Mejoras Sugeridas

- [ ] Filtrado por localidad con selector
- [ ] Filtrado por rango de distancia
- [ ] Exportar ubicaciones a CSV/PDF
- [ ] Guardar favoritos de puntos
- [ ] Calcular ruta hacia un punto
- [ ] Ver historial de visitas

---

**Creado**: Diciembre 2025  
**Versión**: 1.0

