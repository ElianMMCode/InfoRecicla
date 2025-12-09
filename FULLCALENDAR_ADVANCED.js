/**
 * PERSONALIZACIONES AVANZADAS DE FULLCALENDAR
 *
 * Este archivo contiene ejemplos de configuraciones avanzadas que puedes
 * aplicar editando src/main/resources/static/js/PuntoECA/fullcalendar.js
 */

// ============================================================================
// PERSONALIZACIÓN 1: Habilitar edición de eventos (Drag & Drop)
// ============================================================================

// Opción 1: Versión Básica (reemplaza la sección de configuración)
const calendarConfig = {
    initialView: 'dayGridMonth',
    headerToolbar: {
        left: 'prev,next today',
        center: 'title',
        right: 'dayGridMonth,timeGridWeek,timeGridDay'
    },
    locale: 'es',
    height: 'auto',
    editable: true,  // Permite arrastrar eventos
    eventDurationEditable: true,  // Permite cambiar duración
    selectable: true,  // Permite seleccionar rangos de fechas
    selectConstraint: 'businessHours',  // Opcional: solo en horario de negocio
    // ... resto de configuración
};

// ============================================================================
// PERSONALIZACIÓN 2: Agregar horario de negocio (Business Hours)
// ============================================================================

const calendarWithBusinessHours = {
    // ... configuración anterior ...
    businessHours: {
        daysOfWeek: [1, 2, 3, 4, 5],  // Lunes a Viernes
        startTime: '09:00',
        endTime: '18:00'
    },
    slotLabelInterval: '00:30',  // Intervalo de 30 minutos
    slotDuration: '00:30',
};

// ============================================================================
// PERSONALIZACIÓN 3: Personalización de colores y estilos avanzados
// ============================================================================

const calendarWithAdvancedStyling = {
    // ... configuración anterior ...
    eventClassNames: function(arg) {
        // Agregar clases CSS dinámicamente según propiedades del evento
        if (arg.event.backgroundColor === '#28a745') {
            return ['evento-exitoso'];
        }
        if (arg.event.backgroundColor === '#dc3545') {
            return ['evento-urgente'];
        }
        return [];
    },
    eventDidMount: function(info) {
        // Personalizar el elemento del evento después de montarlo
        const element = info.el;

        // Agregar icono según tipo
        if (info.event.title.includes('Recolección')) {
            element.innerHTML = '♻️ ' + element.innerHTML;
        }
        if (info.event.title.includes('Capacitación')) {
            element.innerHTML = '📚 ' + element.innerHTML;
        }

        // Agregar tooltip
        element.title = info.event.extendedProps.descripcion || '';

        // Agregar borde personalizado
        element.style.borderLeft = '4px solid ' + info.event.backgroundColor;
    }
};

// ============================================================================
// PERSONALIZACIÓN 4: Manejo avanzado de eventos (clicks, doble click, etc)
// ============================================================================

const calendarWithAdvancedEventHandling = {
    // ... configuración anterior ...
    eventClick: function(info) {
        console.log('Evento clickeado:', info.event);

        // Mostrar modal con detalles del evento
        const detalles = {
            titulo: info.event.title,
            inicio: info.event.start,
            fin: info.event.end,
            descripcion: info.event.extendedProps.descripcion,
            color: info.event.backgroundColor
        };

        mostrarModalDetalles(detalles);
    },
    eventMouseEnter: function(info) {
        // Efecto hover
        info.el.style.opacity = '0.85';
        info.el.style.transform = 'scale(1.02)';
    },
    eventMouseLeave: function(info) {
        // Restaurar estado normal
        info.el.style.opacity = '1';
        info.el.style.transform = 'scale(1)';
    },
    dateClick: function(info) {
        console.log('Fecha clickeada:', info.dateStr);
        // Abrir formulario para crear nuevo evento
        mostrarFormularioCrearEvento(info.dateStr);
    },
    select: function(info) {
        console.log('Rango seleccionado:', info.start, info.end);
        // Abrir formulario con rango preseleccionado
        mostrarFormularioCrearEvento(info.start, info.end);
    }
};

// ============================================================================
// PERSONALIZACIÓN 5: Cargar eventos dinámicamente con filtros
// ============================================================================

const calendarWithDynamicEvents = {
    // ... configuración anterior ...
    events: function(info, successCallback, failureCallback) {
        const start = info.start.toISOString();
        const end = info.end.toISOString();

        // Obtener parámetros adicionales (ej: filtros de usuario)
        const filtros = {
            tipo: document.getElementById('filtroTipo')?.value || 'todos',
            estado: document.getElementById('filtroEstado')?.value || 'todos'
        };

        // Construir URL con parámetros
        let url = `/api/punto-eca/1/eventos?start=${start}&end=${end}`;

        if (filtros.tipo !== 'todos') {
            url += `&tipo=${filtros.tipo}`;
        }
        if (filtros.estado !== 'todos') {
            url += `&estado=${filtros.estado}`;
        }

        fetch(url)
            .then(response => response.json())
            .then(data => {
                console.log('Eventos cargados:', data);
                successCallback(data);
            })
            .catch(error => {
                console.error('Error cargando eventos:', error);
                failureCallback(error);
            });
    }
};

// ============================================================================
// PERSONALIZACIÓN 6: Sincronización con modal de Bootstrap para crear/editar
// ============================================================================

function mostrarModalDetalles(evento) {
    const modal = new bootstrap.Modal(document.getElementById('modalDetalles'));

    document.getElementById('detallesTitulo').textContent = evento.titulo;
    document.getElementById('detallesDescripcion').textContent = evento.descripcion || 'Sin descripción';
    document.getElementById('detallesInicio').textContent = new Date(evento.inicio).toLocaleString('es-ES');
    document.getElementById('detallesFin').textContent = new Date(evento.fin).toLocaleString('es-ES');

    modal.show();
}

function mostrarFormularioCrearEvento(fechaInicio, fechaFin) {
    const modal = new bootstrap.Modal(document.getElementById('modalCrearEvento'));

    document.getElementById('formFechaInicio').value = fechaInicio ? fechaInicio.toISOString().split('T')[0] : '';
    document.getElementById('formFechaFin').value = fechaFin ? fechaFin.toISOString().split('T')[0] : '';
    document.getElementById('formTitulo').value = '';
    document.getElementById('formDescripcion').value = '';

    modal.show();
}

// ============================================================================
// PERSONALIZACIÓN 7: Validación y guardado de eventos
// ============================================================================

async function guardarEvento(eventoData) {
    // Validar datos
    if (!eventoData.titulo || eventoData.titulo.trim() === '') {
        alert('❌ El título es requerido');
        return false;
    }

    if (new Date(eventoData.fechaInicio) >= new Date(eventoData.fechaFin)) {
        alert('❌ La fecha de fin debe ser posterior a la de inicio');
        return false;
    }

    try {
        const response = await fetch('/api/punto-eca/1/eventos', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(eventoData)
        });

        if (!response.ok) {
            throw new Error('Error al guardar el evento');
        }

        const evento = await response.json();
        console.log('✅ Evento guardado:', evento);

        // Recargar calendario
        if (window.puntoEcaCalendar) {
            window.puntoEcaCalendar.refetchEvents();
        }

        return true;
    } catch (error) {
        console.error('❌ Error:', error);
        alert('Error al guardar el evento: ' + error.message);
        return false;
    }
}

// ============================================================================
// PERSONALIZACIÓN 8: Exportar eventos a iCalendar
// ============================================================================

function exportarEventosICalendar() {
    if (!window.puntoEcaCalendar) return;

    const events = window.puntoEcaCalendar.getEvents();
    let icsContent = `BEGIN:VCALENDAR
VERSION:2.0
PRODID:-//Inforecicla//Calendar//ES
CALSCALE:GREGORIAN
METHOD:PUBLISH
BEGIN:VTIMEZONE
TZID:America/Bogota
END:VTIMEZONE
`;

    events.forEach(event => {
        icsContent += `BEGIN:VEVENT
UID:${event.id}@inforecicla.local
DTSTAMP:${new Date().toISOString().replace(/[-:]/g, '').split('.')[0]}Z
DTSTART:${event.start.toISOString().replace(/[-:]/g, '').split('.')[0]}Z
DTEND:${event.end.toISOString().replace(/[-:]/g, '').split('.')[0]}Z
SUMMARY:${event.title}
DESCRIPTION:${event.extendedProps?.descripcion || ''}
END:VEVENT
`;
    });

    icsContent += `END:VCALENDAR`;

    // Descargar archivo
    const blob = new Blob([icsContent], { type: 'text/calendar' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'eventos-inforecicla.ics';
    a.click();
    window.URL.revokeObjectURL(url);
}

// ============================================================================
// PERSONALIZACIÓN 9: Notificaciones de eventos próximos
// ============================================================================

function mostrarNotificacionesEventosProximos() {
    if (!window.puntoEcaCalendar) return;

    const events = window.puntoEcaCalendar.getEvents();
    const ahora = new Date();

    events.forEach(event => {
        const tiempoRestante = event.start - ahora;
        const minutosRestantes = Math.floor(tiempoRestante / 60000);

        if (minutosRestantes > 0 && minutosRestantes <= 15) {
            mostrarNotificacion(`⏰ ${event.title} comienza en ${minutosRestantes} minutos`);
        }
    });
}

function mostrarNotificacion(mensaje) {
    // Usar Notification API del navegador
    if ('Notification' in window && Notification.permission === 'granted') {
        new Notification('Inforecicla', {
            body: mensaje,
            icon: '/imagenes/logo.png'
        });
    } else {
        // Fallback: usar toast de Bootstrap
        const toast = document.createElement('div');
        toast.className = 'toast';
        toast.setAttribute('role', 'alert');
        toast.innerHTML = `
            <div class="toast-header">
                <strong class="me-auto">Recordatorio</strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
            </div>
            <div class="toast-body">${mensaje}</div>
        `;
        document.body.appendChild(toast);
        const bsToast = new bootstrap.Toast(toast);
        bsToast.show();
    }
}

// Solicitar permiso para notificaciones
if ('Notification' in window && Notification.permission === 'default') {
    Notification.requestPermission();
}

// ============================================================================
// PERSONALIZACIÓN 10: Tema oscuro
// ============================================================================

// CSS para tema oscuro (agregar a fullcalendar-custom.css)
const darkThemeCSS = `
.dark-mode #calendar {
    background-color: #1e1e1e;
    color: #e0e0e0;
}

.dark-mode .fc-button-primary {
    background-color: #1a7324 !important;
    border-color: #0d3d12 !important;
}

.dark-mode .fc-daygrid-day {
    background-color: #2a2a2a;
    border-color: #404040;
}

.dark-mode .fc-col-header {
    background-color: #323232;
    border-color: #404040;
}

.dark-mode .fc-col-header-cell {
    color: #a0a0a0;
}
`;

function toggleDarkMode() {
    document.body.classList.toggle('dark-mode');
    localStorage.setItem('darkMode', document.body.classList.contains('dark-mode'));
}

// Recuperar tema guardado
window.addEventListener('load', function() {
    if (localStorage.getItem('darkMode') === 'true') {
        document.body.classList.add('dark-mode');
    }
});

// ============================================================================
// USO DE ESTAS PERSONALIZACIONES
// ============================================================================

/*
Para usar estas personalizaciones, reemplaza la sección de configuración en:
src/main/resources/static/js/PuntoECA/fullcalendar.js

Ejemplo:
const calendar = new FullCalendar.Calendar(calendarEl, {
    ...calendarWithBusinessHours,
    ...calendarWithAdvancedEventHandling,
    // agregar más opciones según necesites
});

Ten en cuenta que algunas opciones pueden entrar en conflicto.
Prueba cada una por separado primero.
*/

