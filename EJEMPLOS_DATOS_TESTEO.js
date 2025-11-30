/**
 * EJEMPLOS DE RESPUESTAS PARA TESTEAR
 * Pega estos datos en la consola cuando hagas click en "Agregar"
 */

// ============================================================
// EJEMPLO 1: Búsqueda con resultados de botellas de plástico
// ============================================================
window.BusquedaMaterialesUI.mostrarResultados([
  {
    id: 'mat_001',
    nombre: 'Botella PET 2L',
    unidad: 'unidad',
    categoria: 'Plástico'
  },
  {
    id: 'mat_002',
    nombre: 'Botella PET 1L',
    unidad: 'unidad',
    categoria: 'Plástico'
  },
  {
    id: 'mat_003',
    nombre: 'Botella PET 500ml',
    unidad: 'unidad',
    categoria: 'Plástico'
  }
]);

// ============================================================
// EJEMPLO 2: Búsqueda con resultados variados
// ============================================================
/*
window.BusquedaMaterialesUI.mostrarResultados([
  {
    id: 'mat_001',
    nombre: 'Botella PET 2L',
    unidad: 'unidad',
    categoria: 'Plástico'
  },
  {
    id: 'mat_002',
    nombre: 'Cartón Ondulado Gris',
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
    nombre: 'Aluminio Lata',
    unidad: 'unidad',
    categoria: 'Metal'
  },
  {
    id: 'mat_005',
    nombre: 'Espuma de Poliestireno',
    unidad: 'kg',
    categoria: 'Plástico'
  }
]);
*/

// ============================================================
// EJEMPLO 3: Sin resultados
// ============================================================
/*
window.BusquedaMaterialesUI.mostrarSinResultados('No se encontraron materiales con esa búsqueda');
*/

// ============================================================
// EJEMPLO 4: Muchos resultados (pagination)
// ============================================================
/*
window.BusquedaMaterialesUI.mostrarResultados([
  {id: 'm001', nombre: 'Material 1', unidad: 'unidad', categoria: 'Categoría A'},
  {id: 'm002', nombre: 'Material 2', unidad: 'kg', categoria: 'Categoría B'},
  {id: 'm003', nombre: 'Material 3', unidad: 'l', categoria: 'Categoría C'},
  {id: 'm004', nombre: 'Material 4', unidad: 'm', categoria: 'Categoría A'},
  {id: 'm005', nombre: 'Material 5', unidad: 'm2', categoria: 'Categoría D'},
  {id: 'm006', nombre: 'Material 6', unidad: 'unidad', categoria: 'Categoría B'},
  {id: 'm007', nombre: 'Material 7', unidad: 'kg', categoria: 'Categoría A'},
  {id: 'm008', nombre: 'Material 8', unidad: 'l', categoria: 'Categoría C'},
  {id: 'm009', nombre: 'Material 9', unidad: 'm', categoria: 'Categoría D'},
  {id: 'm010', nombre: 'Material 10', unidad: 'm2', categoria: 'Categoría E'}
]);
*/

// ============================================================
// EJEMPLO 5: Escuchar eventos y simular respuesta
// ============================================================
/*
document.addEventListener('catalogoMateriales:buscar', (evt) => {
  console.log('📢 Evento capturado:', evt.detail);

  // Simular delay del servidor
  setTimeout(() => {
    const respuesta = [
      {
        id: 'mat_001',
        nombre: `Resultado para: "${evt.detail.texto}"`,
        unidad: 'unidad',
        categoria: evt.detail.categoria || 'General'
      }
    ];
    window.BusquedaMaterialesUI.mostrarResultados(respuesta);
  }, 1000);
});
*/

// ============================================================
// NOTAS IMPORTANTES
// ============================================================
/*

1. Abre consola: F12 → Consola
2. Haz click en "Agregar" en la vista
3. Pega uno de los ejemplos anteriores y presiona Enter
4. Deberías ver los resultados en el modal

PARA TESTEAR EL FLUJO COMPLETO:
- Descomenta EJEMPLO 5
- Haz click en "Agregar"
- Verás el evento en consola
- Los datos se mostrarán automáticamente

CAMPOS REQUERIDOS:
- id: string (identificador único)
- nombre: string (nombre del material)
- unidad: string (kg, l, m, m2, unidad, etc)
- categoria: string (categoría del material)

Puedes tener más campos (se ignoran), pero estos 4 son obligatorios.

*/

// ============================================================
// QUICK START: Copia y pega esto directamente en consola
// ============================================================

// Datos de ejemplo para Plásticos
const plasticos = [
  {id: 'mat_001', nombre: 'Botella PET 2L', unidad: 'unidad', categoria: 'Plástico'},
  {id: 'mat_002', nombre: 'Bolsa Plástica', unidad: 'kg', categoria: 'Plástico'},
  {id: 'mat_003', nombre: 'Tubo PVC', unidad: 'm', categoria: 'Plástico'}
];

// Datos de ejemplo para Papel
const papel = [
  {id: 'mat_101', nombre: 'Cartón Ondulado', unidad: 'kg', categoria: 'Papel'},
  {id: 'mat_102', nombre: 'Papel Periódico', unidad: 'kg', categoria: 'Papel'},
  {id: 'mat_103', nombre: 'Cartón Gris', unidad: 'kg', categoria: 'Papel'}
];

// Datos de ejemplo para Vidrio
const vidrio = [
  {id: 'mat_201', nombre: 'Botella Cerveza', unidad: 'unidad', categoria: 'Vidrio'},
  {id: 'mat_202', nombre: 'Vidrio Plano', unidad: 'kg', categoria: 'Vidrio'},
  {id: 'mat_203', nombre: 'Frasco Vidrio', unidad: 'unidad', categoria: 'Vidrio'}
];

// Para mostrar plásticos:
// window.BusquedaMaterialesUI.mostrarResultados(plasticos);

// Para mostrar papel:
// window.BusquedaMaterialesUI.mostrarResultados(papel);

// Para mostrar vidrio:
// window.BusquedaMaterialesUI.mostrarResultados(vidrio);

// Para mostrar todos:
// window.BusquedaMaterialesUI.mostrarResultados([...plasticos, ...papel, ...vidrio]);

console.log('✅ Datos de ejemplo cargados');
console.log('Usa: window.BusquedaMaterialesUI.mostrarResultados(plasticos)');
console.log('     window.BusquedaMaterialesUI.mostrarResultados(papel)');
console.log('     window.BusquedaMaterialesUI.mostrarResultados(vidrio)');

