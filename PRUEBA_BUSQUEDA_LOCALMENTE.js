/**
 * SCRIPT DE PRUEBA - Copia y pega en consola del navegador (F12)
 * para simular respuestas del backend mientras lo desarrollas
 */

// ====================================================
// PRUEBA 1: Simular búsqueda con resultados
// ====================================================
console.log("=== PRUEBA 1: Búsqueda con resultados ===");
const materialesFake = [
    {
        id: 'mat_001',
        nombre: 'Botella PET 2L',
        unidad: 'unidad',
        categoria: 'Plástico'
    },
    {
        id: 'mat_002',
        nombre: 'Cartón Ondulado',
        unidad: 'kg',
        categoria: 'Papel'
    },
    {
        id: 'mat_003',
        nombre: 'Vidrio Transparente',
        unidad: 'kg',
        categoria: 'Vidrio'
    },
    {
        id: 'mat_004',
        nombre: 'Metal Aluminio',
        unidad: 'kg',
        categoria: 'Metal'
    }
];

// Llamar a la UI directamente
window.BusquedaMaterialesUI.mostrarResultados(materialesFake);
console.log("✓ Resultados mostrados en el modal");

// ====================================================
// PRUEBA 2: Simular búsqueda sin resultados
// ====================================================
// (Descomenta para ejecutar)
/*
console.log("=== PRUEBA 2: Búsqueda sin resultados ===");
window.BusquedaMaterialesUI.mostrarSinResultados('No se encontraron materiales con esa búsqueda');
console.log("✓ Mensaje de sin resultados mostrado");
*/

// ====================================================
// PRUEBA 3: Simular error del servidor
// ====================================================
// (Descomenta para ejecutar)
/*
console.log("=== PRUEBA 3: Error del servidor ===");
window.BusquedaMaterialesUI.mostrarSinResultados('Error del servidor (500)');
console.log("✓ Mensaje de error mostrado");
*/

// ====================================================
// PRUEBA 4: Simular listener del evento (backend mock)
// ====================================================
// (Descomenta para ejecutar - capturará eventos cuando hagas click en "Agregar")
/*
console.log("=== PRUEBA 4: Escuchando eventos ===");
document.addEventListener('catalogoMateriales:buscar', (evt) => {
    console.log("📢 Evento recibido:", evt.detail);
    console.log("  - Texto:", evt.detail.texto);
    console.log("  - Categoría:", evt.detail.categoria);
    console.log("  - Tipo:", evt.detail.tipo);

    // Simular respuesta del backend
    setTimeout(() => {
        window.BusquedaMaterialesUI.mostrarResultados(materialesFake);
    }, 1000);
});
console.log("✓ Listener activo - haz click en 'Agregar' para ver el evento");
*/

// ====================================================
// INSTRUCCIONES
// ====================================================
console.log(`
╔════════════════════════════════════════════════════════════╗
║           PRUEBA DEL SISTEMA DE BÚSQUEDA                 ║
╠════════════════════════════════════════════════════════════╣
║                                                            ║
║ 1. Abre la vista en la app (sección Materiales)            ║
║ 2. Abre consola: F12 → Consola                             ║
║ 3. Pega este script y ejecuta (Enter)                      ║
║ 4. Deberías ver materiales en el modal de búsqueda         ║
║ 5. Selecciona uno y verifica que abre el formulario        ║
║                                                            ║
║ Para probar EVENTOS:                                       ║
║ - Descomenta PRUEBA 4                                      ║
║ - Haz click en "Agregar" en la vista                       ║
║ - Deberías ver el evento en consola                        ║
║                                                            ║
╚════════════════════════════════════════════════════════════╝
`);

