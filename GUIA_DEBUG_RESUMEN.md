## 🔍 GUÍA DE DEBUG - Verificar datos del Resumen

### Paso 1: Compilar el proyecto
```bash
cd /home/rorschard/Documents/Java/Inforecicla
mvn clean compile
```

### Paso 2: Iniciar la aplicación
```bash
mvn spring-boot:run
```

### Paso 3: Verificar el endpoint de debug
En el navegador, accede a:
```
http://localhost:8080/punto-eca/{ID_PUNTO_ECA}/api/debug
```

Reemplaza `{ID_PUNTO_ECA}` con el UUID real de tu Punto ECA.

### Paso 4: Observar la respuesta JSON
La respuesta debería mostrar:
- `puntoEcaExiste`: true o false
- `inventariosCount`: número de inventarios
- `comprasCount`: número de compras
- `ventasCount`: número de ventas
- Posibles errores en cada servicio

### Si los datos son 0 (cero):
1. Verifica que el Punto ECA tenga datos en la BD
2. Verifica que haya inventarios registrados
3. Verifica que haya compras/ventas registradas

### Acceder al resumen normal:
Una vez que veas que el debug trae datos, el resumen debería funcionar en:
```
http://localhost:8080/punto-eca/{ID_PUNTO_ECA}/api/resumen
```

### Revisar logs en consola:
Los logs mostrarán exactamente qué datos se están obteniendo de cada servicio.

