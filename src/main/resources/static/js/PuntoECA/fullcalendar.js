/**
 * FullCalendar - Sistema de Creación de Eventos
 * Versión mejorada con Select2
 */

// Bandera para evitar que se ejecute más de una vez
if (window.fullCalendarInitialized) {
    console.warn('⚠️ FullCalendar ya fue inicializado, evitando duplicado');
} else {
    window.fullCalendarInitialized = true;

document.addEventListener('DOMContentLoaded', function() {
    try {
        console.log('🎯 Inicializando sistema de calendario...');

        // ===== VERIFICAR QUE JQUERY Y SELECT2 ESTÉN DISPONIBLES =====
        if (typeof jQuery === 'undefined') {
            console.error('❌ jQuery no está cargado');
            return;
        }
        if (typeof jQuery.fn.select2 === 'undefined') {
            console.error('❌ Select2 no está cargado');
            return;
        }
        console.log('✅ jQuery y Select2 están disponibles');

        // ===== ELEMENTOS DEL DOM =====
        const calendarEl = document.getElementById('calendar');
        const selectMaterial = document.getElementById('selectMaterial');
        const selectCentroAcopio = document.getElementById('selectCentroAcopio');
        const btnGuardarEvento = document.getElementById('btnGuardarEvento');
        const modalCrearEvento = document.getElementById('modalCrearEvento');
        const formCrearEvento = document.getElementById('formCrearEvento');

        if (!calendarEl) {
            console.warn('⚠️ Elemento #calendar no encontrado');
            return;
        }

        // ===== PARÁMETROS =====
        const puntoEcaId = document.querySelector('input[id="inputPuntoEcaId"]')?.value;
        const usuarioId = document.querySelector('input[id="inputUsuarioId"]')?.value;

        console.log('🔍 Buscando inputs...');
        console.log('  input#inputPuntoEcaId existe:', !!document.getElementById('inputPuntoEcaId'));
        console.log('  input#inputUsuarioId existe:', !!document.getElementById('inputUsuarioId'));
        console.log('📋 Parámetros:');
        console.log('  puntoEcaId:', puntoEcaId);
        console.log('  usuarioId:', usuarioId);

        if (!puntoEcaId || !usuarioId) {
            console.error('❌ Parámetros incompletos');
            return;
        }

        let calendar = null;

        // ===== INICIALIZAR CALENDARIO =====
        try {
            calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                locale: 'es',
                height: 'auto',
                selectable: true,
                events: {
                    url: `/api/eventos/punto/${puntoEcaId}/eventos`,
                    method: 'GET',
                    failure: function() {
                        console.warn('⚠️ Error cargando eventos del calendario');
                    }
                },
                select: function(info) {
                    abrirModal(info.start);
                },
                eventClick: function(info) {
                    console.log('📌 Evento clickeado:', info.event.title);
                    mostrarDetallesEvento(info.event);
                }
            });

            calendar.render();
            console.log('✅ Calendario inicializado');
        } catch (e) {
            console.error('❌ Error inicializando calendario:', e);
        }

        // ===== CARGAR MATERIALES =====
        function cargarMateriales() {
            if (!selectMaterial) {
                console.warn('⚠️ selectMaterial no encontrado');
                return;
            }

            console.log('📥 Cargando materiales...');

            fetch(`/punto-eca/catalogo/inventario/materiales/buscar?puntoId=${puntoEcaId}`)
                .then(r => {
                    if (!r.ok) throw new Error(`HTTP ${r.status}`);
                    return r.json();
                })
                .then(data => {
                    console.log('✅ Materiales recibidos:', data.length);

                    // Limpiar opciones anteriores
                    selectMaterial.innerHTML = '<option value="">-- Seleccionar Material --</option>';

                    if (Array.isArray(data) && data.length > 0) {
                        data.forEach(m => {
                            const opt = document.createElement('option');
                            opt.value = m.materialId;
                            opt.textContent = m.nmbMaterial || m.dscMaterial || 'Material sin nombre';
                            selectMaterial.appendChild(opt);
                        });
                        console.log('✅ ' + data.length + ' materiales agregados');
                    } else {
                        console.warn('⚠️ No hay materiales disponibles');
                        const opt = document.createElement('option');
                        opt.disabled = true;
                        opt.textContent = 'No hay materiales disponibles';
                        selectMaterial.appendChild(opt);
                    }

                    // Reinicializar Select2 después de cargar datos
                    const $ = jQuery;
                    if ($(selectMaterial).data('select2')) {
                        $(selectMaterial).select2('destroy');
                    }
                    $(selectMaterial).select2({
                        dropdownParent: $('#modalCrearEvento'),
                        language: 'es',
                        width: '100%',
                        minimumResultsForSearch: 1,
                        placeholder: 'Seleccionar Material...',
                        allowClear: true,
                        theme: 'bootstrap-5',
                        containerCssClass: 'select2-custom'
                    });
                    console.log('  ✅ Select2 reinicializado en selectMaterial');
                })
                .catch(e => {
                    console.error('❌ Error cargando materiales:', e);
                    selectMaterial.innerHTML = '<option disabled>Error cargando materiales</option>';
                });
        }

        // ===== CARGAR CENTROS =====
        function cargarCentrosAcopio() {
            if (!selectCentroAcopio) {
                console.warn('⚠️ selectCentroAcopio no encontrado');
                return;
            }

            console.log('📥 Cargando centros para puntoEcaId:', puntoEcaId);

            fetch(`/punto-eca/${puntoEcaId}/centros-acopio`)
                .then(response => {
                    console.log('📡 Response status:', response.status);
                    if (!response.ok) {
                        throw new Error(`HTTP ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('✅ Centros recibidos - cantidad:', data ? data.length : 0);
                    console.log('📦 Datos completos:', data);

                    // Limpiar opciones anteriores
                    selectCentroAcopio.innerHTML = '<option value="">-- Sin asignar --</option>';

                    if (data && Array.isArray(data) && data.length > 0) {
                        data.forEach((centro, idx) => {
                            console.log(`   Centro ${idx}:`, {
                                cntAcpId: centro.cntAcpId,
                                nombreCntAcp: centro.nombreCntAcp,
                                tienePuntoEca: centro.tienePuntoEca
                            });

                            const opt = document.createElement('option');
                            opt.value = centro.cntAcpId;

                            let nombre = centro.nombreCntAcp || 'Centro sin nombre';

                            if (centro.tienePuntoEca) {
                                nombre += ' (del punto)';
                            } else {
                                nombre += ' (global)';
                            }

                            opt.textContent = nombre;
                            selectCentroAcopio.appendChild(opt);
                        });

                        console.log('✅ ' + data.length + ' centros agregados al select');
                    } else {
                        console.warn('⚠️ No hay centros o data es vacío/null');
                        const opt = document.createElement('option');
                        opt.disabled = true;
                        opt.textContent = 'No hay centros disponibles';
                        selectCentroAcopio.appendChild(opt);
                    }

                    // Reinicializar Select2 después de cargar datos
                    const $ = jQuery;
                    if ($(selectCentroAcopio).data('select2')) {
                        $(selectCentroAcopio).select2('destroy');
                    }
                    $(selectCentroAcopio).select2({
                        dropdownParent: $('#modalCrearEvento'),
                        language: 'es',
                        width: '100%',
                        minimumResultsForSearch: 1,
                        placeholder: 'Seleccionar Centro...',
                        allowClear: true,
                        theme: 'bootstrap-5',
                        containerCssClass: 'select2-custom'
                    });
                    console.log('  ✅ Select2 reinicializado en selectCentroAcopio');
                })
                .catch(error => {
                    console.error('❌ Error cargando centros:', error.message);
                    console.error('Stack:', error.stack);
                    selectCentroAcopio.innerHTML = '<option disabled>Error cargando centros</option>';
                });
        }

        // ===== ABRIR MODAL =====
        function abrirModal(fecha) {
            const inputFecha = document.getElementById('inputFechaInicio');
            if (inputFecha) {
                const fechaStr = fecha.toISOString().split('T')[0];
                inputFecha.value = fechaStr;
            }

            if (modalCrearEvento) {
                const modal = new bootstrap.Modal(modalCrearEvento);
                modal.show();

                // Cargar datos y mostrar el modal
                cargarMateriales();
                cargarCentrosAcopio();

                // Inicializar Select2 del tipo de repetición después de que el modal sea visible
                setTimeout(() => {
                    inicializarSelect2();
                }, 100);
            }
        }

        // ===== MOSTRAR DETALLES DEL EVENTO =====
        function mostrarDetallesEvento(evento) {
            console.log('📋 Mostrando detalles del evento:', evento.title);

            const modalDetalles = document.getElementById('modalDetallesEvento');
            if (!modalDetalles) {
                console.warn('⚠️ Modal de detalles no encontrado');
                return;
            }

            // Rellenar datos del evento
            const tituloEl = document.getElementById('detallesTitulo');
            const descripcionEl = document.getElementById('detallesDescripcion');
            const fechaInicioEl = document.getElementById('detallesFechaInicio');
            const fechaFinEl = document.getElementById('detallesFechaFin');
            const materialEl = document.getElementById('detallesMaterial');
            const centroEl = document.getElementById('detallesCentro');
            const btnEditar = document.getElementById('btnEditarEvento');
            const btnBorrar = document.getElementById('btnBorrarEvento');

            if (tituloEl) tituloEl.textContent = evento.title || 'Sin título';
            if (descripcionEl) descripcionEl.textContent = evento.extendedProps?.descripcion || 'Sin descripción';
            if (fechaInicioEl) fechaInicioEl.textContent = new Date(evento.start).toLocaleString('es-ES');
            if (fechaFinEl) fechaFinEl.textContent = new Date(evento.end).toLocaleString('es-ES');
            if (materialEl) materialEl.textContent = evento.extendedProps?.material || 'Sin material';
            if (centroEl) centroEl.textContent = evento.extendedProps?.centro || 'Sin asignar';

            // Configurar botones
            if (btnEditar) {
                btnEditar.onclick = () => editarEvento(evento.id);
            }
            if (btnBorrar) {
                btnBorrar.onclick = () => borrarEvento(evento.id);
            }

            // Mostrar modal
            const modal = new bootstrap.Modal(modalDetalles);
            modal.show();
        }

        // ===== EDITAR EVENTO =====
        function editarEvento(eventoId) {
            console.log('✏️ Editando evento:', eventoId);
            alert('Función de edición en desarrollo');
            // TODO: Implementar edición de eventos
        }

        // ===== BORRAR EVENTO =====
        function borrarEvento(eventoId) {
            console.log('🗑️ Borrando evento:', eventoId);

            if (confirm('¿Está seguro de que desea borrar este evento?')) {
                fetch(`/api/eventos/${eventoId}`, {
                    method: 'DELETE'
                })
                .then(response => {
                    if (response.ok) {
                        console.log('✅ Evento borrado');
                        alert('Evento borrado correctamente');

                        // Recargar calendario
                        if (calendar) calendar.refetchEvents();

                        // Cerrar modal
                        const modalDetalles = document.getElementById('modalDetallesEvento');
                        const modal = bootstrap.Modal.getInstance(modalDetalles);
                        if (modal) modal.hide();
                    } else {
                        alert('❌ Error al borrar el evento');
                    }
                })
                .catch(error => {
                    console.error('❌ Error:', error);
                    alert('❌ Error al borrar el evento');
                });
            }
        }

        // ===== GUARDAR EVENTO =====
        async function guardarEvento() {
            console.log('💾 Guardando evento...');

            // Validar
            if (!selectMaterial?.value) {
                alert('❌ Selecciona un material');
                return;
            }

            const titulo = document.getElementById('inputTitulo')?.value;
            if (!titulo?.trim()) {
                alert('❌ Ingresa un título');
                return;
            }

            try {
                const datos = {
                    materialId: selectMaterial.value,
                    puntoEcaId: puntoEcaId,
                    usuarioId: usuarioId,
                    centroAcopioId: selectCentroAcopio?.value || null,
                    titulo: titulo,
                    descripcion: document.getElementById('inputDescripcion')?.value || '',
                    fechaInicio: (document.getElementById('inputFechaInicio')?.value || '') + 'T' + (document.getElementById('inputHoraInicio')?.value || '10:00') + ':00',
                    fechaFin: (document.getElementById('inputFechaInicio')?.value || '') + 'T' + (document.getElementById('inputHoraFin')?.value || '11:00') + ':00',
                    tipoRepeticion: document.getElementById('selectTipoRepeticion')?.value || 'SIN_REPETICION',
                    fechaFinRepeticion: document.getElementById('inputFechaFinRepeticion')?.value || null,
                    color: document.getElementById('inputColor')?.value || '#28a745'
                };

                console.log('📤 Enviando datos:', datos);

                if (btnGuardarEvento) {
                    btnGuardarEvento.disabled = true;
                    btnGuardarEvento.innerHTML = 'Guardando...';
                }

                const res = await fetch('/api/eventos/crear-venta', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(datos)
                });

                console.log('📡 Response status:', res.status);

                if (res.ok) {
                    const respuesta = await res.json();
                    console.log('✅ Evento guardado:', respuesta);
                    alert('✅ Evento creado correctamente');

                    // Recargar calendario
                    if (calendar) calendar.refetchEvents();

                    // Cerrar modal
                    const modal = bootstrap.Modal.getInstance(modalCrearEvento);
                    if (modal) modal.hide();

                    // Limpiar formulario
                    if (formCrearEvento) formCrearEvento.reset();
                } else {
                    const err = await res.json();
                    console.error('❌ Error:', err);
                    alert('❌ Error: ' + (err.error || 'Error al guardar'));
                }
            } catch (e) {
                console.error('❌ Exception:', e);
                alert('❌ Error: ' + e.message);
            } finally {
                if (btnGuardarEvento) {
                    btnGuardarEvento.disabled = false;
                    btnGuardarEvento.innerHTML = '<i class="bi bi-save"></i> Guardar Evento';
                }
            }
        }

        // ===== EVENT LISTENERS =====
        if (btnGuardarEvento) {
            // Remover listeners anteriores para evitar duplicados
            btnGuardarEvento.replaceWith(btnGuardarEvento.cloneNode(true));
            const btnNuevo = document.getElementById('btnGuardarEvento');

            if (btnNuevo) {
                btnNuevo.addEventListener('click', guardarEvento);
            }
        }

        if (modalCrearEvento) {
            modalCrearEvento.addEventListener('show.bs.modal', () => {
                console.log('📋 Modal abierto');
                cargarMateriales();
                cargarCentrosAcopio();
            });
        }

        console.log('✅ Sistema completamente inicializado');

        // ===== INICIALIZAR SELECT2 =====
        function inicializarSelect2() {
            console.log('🎨 Inicializando Select2...');

            try {
                // Usar $ de jQuery de forma segura
                const $ = jQuery;

                // Inicializar selectMaterial si existe y aún no está inicializado
                if (selectMaterial) {
                    if ($(selectMaterial).data('select2')) {
                        console.log('  ♻️ Destruyendo Select2 anterior en selectMaterial');
                        $(selectMaterial).select2('destroy');
                    }
                    $(selectMaterial).select2({
                        dropdownParent: $('#modalCrearEvento'),
                        language: 'es',
                        width: '100%',
                        minimumResultsForSearch: 1,
                        placeholder: 'Seleccionar Material...',
                        allowClear: true,
                        theme: 'bootstrap-5',
                        containerCssClass: 'select2-custom'
                    });
                    console.log('  ✅ Select2 inicializado en selectMaterial');
                }

                // Inicializar selectCentroAcopio si existe
                if (selectCentroAcopio) {
                    if ($(selectCentroAcopio).data('select2')) {
                        console.log('  ♻️ Destruyendo Select2 anterior en selectCentroAcopio');
                        $(selectCentroAcopio).select2('destroy');
                    }
                    $(selectCentroAcopio).select2({
                        dropdownParent: $('#modalCrearEvento'),
                        language: 'es',
                        width: '100%',
                        minimumResultsForSearch: 1,
                        placeholder: 'Seleccionar Centro...',
                        allowClear: true,
                        theme: 'bootstrap-5',
                        containerCssClass: 'select2-custom'
                    });
                    console.log('  ✅ Select2 inicializado en selectCentroAcopio');
                }

                // Inicializar selectTipoRepeticion
                const selectTipoRepeticion = document.getElementById('selectTipoRepeticion');
                if (selectTipoRepeticion) {
                    if ($(selectTipoRepeticion).data('select2')) {
                        console.log('  ♻️ Destruyendo Select2 anterior en selectTipoRepeticion');
                        $(selectTipoRepeticion).select2('destroy');
                    }
                    $(selectTipoRepeticion).select2({
                        dropdownParent: $('#modalCrearEvento'),
                        language: 'es',
                        width: '100%',
                        minimumResultsForSearch: 1,
                        placeholder: 'Seleccionar tipo de repetición...',
                        allowClear: false,
                        theme: 'bootstrap-5',
                        containerCssClass: 'select2-custom'
                    });
                    console.log('  ✅ Select2 inicializado en selectTipoRepeticion');
                }

                console.log('✅ Select2 completamente inicializado');
            } catch (e) {
                console.warn('⚠️ Error inicializando Select2:', e.message);
            }
        }

    } catch (error) {
        console.error('❌ Error global:', error);
    }
});

} // Cierre de la bandera fullCalendarInitialized
