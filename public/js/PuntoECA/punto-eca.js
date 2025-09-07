
// =================== TOAST ===================
const toast = new bootstrap.Toast(document.getElementById('toastOK'), {
    delay: 1600
});
const showToast = (msg) => {
    document.getElementById('toastText').textContent = msg;
    toast.show();
};

// =================== DATOS DEMO ===================
const PROV = [{
    nombre: 'ReciclaBog',
    tipo: 'Proveedor',
    contacto: 'Ana T.',
    tel: '3101234567',
    mail: 'ana@reciclabo.g',
    dir: 'Calle 10'
},
{
    nombre: 'Centro Norte',
    tipo: 'Centro de acopio',
    contacto: 'Juan P.',
    tel: '3207654321',
    mail: 'cn@centro.co',
    dir: 'Av 19 #20'
},
{
    nombre: 'Vidrios Andinos',
    tipo: 'Centro de acopio',
    contacto: 'J. Pérez',
    tel: '3207654322',
    mail: 'comercial@vidrios.co',
    dir: 'Cra 15 #3'
}
];
const MATERIALES = [{
    id: 1,
    nombre: 'Plástico PET',
    tipo: 'Plástico',
    categoria: 'PET',
    desc: 'Botellas PET transparentes',
    almacenamiento: 'Sacos en área cubierta',
    capacidad: 1200,
    umbral: 1000,
    umbralCrit: 1150,
    stock: 950,
    img: 'https://picsum.photos/seed/pet/200/200',
    precioCompra: 800,
    precioVenta: 1200,
    freq: 'semanal'
},
{
    id: 2,
    nombre: 'Cartón',
    tipo: 'Papel y cartón',
    categoria: 'Mixto',
    desc: 'Cartón corrugado limpio',
    almacenamiento: 'Apilado en estibas',
    capacidad: 2000,
    umbral: 1500,
    umbralCrit: 1800,
    stock: 640,
    img: 'https://picsum.photos/seed/card/200/200',
    precioCompra: 300,
    precioVenta: 600,
    freq: 'mensual'
},
{
    id: 3,
    nombre: 'Vidrio verde',
    tipo: 'Vidrio',
    categoria: 'Botella verde',
    desc: 'Botella de vidrio verde',
    almacenamiento: 'Tolva a la intemperie',
    capacidad: 5000,
    umbral: 4200,
    umbralCrit: 4800,
    stock: 4200,
    img: 'https://picsum.photos/seed/glass/200/200',
    precioCompra: 50,
    precioVenta: 180,
    freq: 'manual'
}
];
const HIST_COMPRAS = [{
    fecha: '2025-08-01',
    material: 1,
    kg: 120,
    proveedor: 'ReciclaBog',
    punit: 750
},
{
    fecha: '2025-08-14',
    material: 2,
    kg: 320,
    proveedor: 'CartoCol',
    punit: 320
},
{
    fecha: '2025-08-20',
    material: 3,
    kg: 600,
    proveedor: 'Vidrios Andinos',
    punit: 70
}
];
const HIST_SALIDAS = [{
    fecha: '2025-08-05',
    material: 1,
    kg: 200,
    centro: 'Centro Norte',
    punit: 1150
},
{
    fecha: '2025-08-18',
    material: 3,
    kg: 1500,
    centro: 'Vidrios Andinos',
    punit: 170
},
{
    fecha: '2025-08-22',
    material: 2,
    kg: 600,
    centro: 'Cartón Sur',
    punit: 580
}
];
let DESPACHOS = [{
    fecha: '2025-08-28',
    hora: '10:00',
    material: 1,
    centro: 'Centro Norte'
},
{
    fecha: '2025-09-03',
    hora: '09:30',
    material: 2,
    centro: 'Cartón Sur'
}
];

// =================== HELPERS ===================
function pad2(n) {
    return n.toString().padStart(2, '0')
}

function fmtDate(d) {
    return d.getFullYear() + '-' + pad2(d.getMonth() + 1) + '-' + pad2(d.getDate())
}

function startOfMonth(d) {
    return new Date(d.getFullYear(), d.getMonth(), 1)
}

function endOfMonth(d) {
    return new Date(d.getFullYear(), d.getMonth() + 1, 0)
}

// =================== KPIs + Alertas ===================
function renderKPIs() {
    const inv = MATERIALES.reduce((a, m) => a + (m.stock || 0), 0);
    const entMes = HIST_COMPRAS.reduce((a, h) => a + h.kg, 0);
    const salMes = HIST_SALIDAS.reduce((a, h) => a + h.kg, 0);
    document.getElementById('kpiInventario').textContent = inv.toLocaleString('es-CO');
    document.getElementById('kpiEntradasMes').textContent = entMes.toLocaleString('es-CO');
    document.getElementById('kpiSalidasMes').textContent = salMes.toLocaleString('es-CO');
    const prox = DESPACHOS.map(d => d.fecha).sort()[0];
    document.getElementById('kpiProximoDespacho').textContent = prox || '—';

    const alertas = MATERIALES.filter(m => (m.stock || 0) >= (m.umbral || Infinity));
    document.getElementById('alertCount').textContent = alertas.length;
    const list = document.getElementById('alertList');
    list.innerHTML = alertas.length ? '' : 'Sin alertas.';
    alertas.forEach(m => {
        const div = document.createElement('div');
        const nivel = (m.stock || 0) >= (m.umbralCrit || Number.MAX_SAFE_INTEGER) ? 'CRÍTICO' : 'ALERTA';
        div.innerHTML = `<span class="badge badge-soft me-2">${m.tipo}</span> <strong>${m.nombre}</strong> ${nivel}: ${m.stock || 0}/${m.capacidad} kg (umbral ${m.umbral || '—'})`;
        list.appendChild(div);
    });
}

// =================== MATERIAL GRID ===================
function renderMateriales() {
    const tipo = document.getElementById('fTipo').value.toLowerCase();
    const cat = document.getElementById('fCategoria').value.toLowerCase();
    const txt = document.getElementById('fTexto').value.toLowerCase();
    const cont = document.getElementById('gridMateriales');
    const items = MATERIALES.filter(m => {
        const okTipo = !tipo || m.tipo.toLowerCase() === tipo;
        const okCat = !cat || m.categoria.toLowerCase() === cat;
        const okTxt = !txt || (m.nombre + m.desc).toLowerCase().includes(txt);
        return okTipo && okCat && okTxt;
    });
    cont.innerHTML = items.map(m => {
        const pct = Math.min(100, Math.round(((m.stock || 0) / (m.capacidad || 1)) * 100));
        let barClass = 'bg-success';
        if ((m.stock || 0) >= (m.umbralCrit || Infinity)) barClass = 'bg-danger';
        else if ((m.stock || 0) >= (m.umbral || Infinity)) barClass = 'bg-warning';
        const freqTxt = m.freq ? m.freq.charAt(0).toUpperCase() + m.freq.slice(1) : 'Manual';
        return `
        <div class="col-12 col-sm-6 col-lg-4">
          <div class="card card-hover h-100" data-mid="${m.id}">
            <div class="card-body d-flex gap-3">
              <img src="${m.img || 'https://picsum.photos/seed/mat' + m.id + '/200/200'}" class="material-thumb" alt="${m.nombre}">
              <div class="flex-grow-1">
                <div class="d-flex justify-content-between align-items-center">
                  <h6 class="mb-1">${m.nombre}</h6>
                  <span class="badge badge-soft">${m.categoria}</span>
                </div>
                <div class="small text-muted">${m.tipo} · <span class="text-success">${freqTxt}</span></div>
                <div class="small mt-1">Capacidad: <strong>${m.capacidad} kg</strong></div>
                <div class="small mt-1">Stock: <strong>${m.stock || 0} kg</strong></div>
                <div class="progress compact mt-1" title="${pct}%">
                  <div class="progress-bar ${barClass}" style="width:${pct}%">${pct}%</div>
                </div>
              </div>
            </div>
            <div class="card-footer d-flex justify-content-end gap-2">
              <button class="btn btn-outline-success btn-sm btn-detalle">Detalle</button>
            </div>
          </div>
        </div>`;
    }).join('');
    cont.querySelectorAll('.btn-detalle').forEach(btn => {
        btn.addEventListener('click', e => {
            const id = +e.target.closest('[data-mid]').dataset.mid;
            abrirMaterial(id);
        })
    });
}

// =================== REGISTRO MATERIAL (Modal) ===================
['matStock', 'matCapMax'].forEach(id => {
    document.getElementById(id).addEventListener('input', () => {
        const stock = parseFloat(document.getElementById('matStock').value || 0);
        const cap = parseFloat(document.getElementById('matCapMax').value || 1);
        const pct = Math.min(100, Math.round((stock / cap) * 100));
        document.getElementById('matOcupacionPct').textContent = pct + '%';
        const bar = document.getElementById('matOcupacionBar');
        bar.style.width = pct + '%';
        bar.classList.remove('bg-success', 'bg-warning', 'bg-danger');
        if (pct >= 90) bar.classList.add('bg-danger');
        else if (pct >= 75) bar.classList.add('bg-warning');
        else bar.classList.add('bg-success');
    });
});

function fillCentrosSelects() {
    const centros = PROV.filter(p => p.tipo.toLowerCase().includes('centro'));
    const opts = centros.map(c => `<option value="${c.nombre}">${c.nombre}</option>`).join('');
    document.getElementById('matCentro').innerHTML = '<option value="">— Seleccione —</option>' + opts;
    document.getElementById('calCentro').innerHTML = opts;
}

document.getElementById('formMaterial').addEventListener('submit', (e) => {
    e.preventDefault();
    const id = (MATERIALES.at(-1)?.id || 0) + 1;
    const m = {
        id,
        nombre: document.getElementById('matNombre').value.trim(),
        tipo: document.getElementById('matTipo').value,
        categoria: document.getElementById('matCategoria').value.trim() || '—',
        desc: document.getElementById('matDesc').value.trim(),
        almacenamiento: '—',
        capacidad: parseFloat(document.getElementById('matCapMax').value),
        stock: parseFloat(document.getElementById('matStock').value || 0),
        umbral: parseFloat(document.getElementById('matUmbralKg').value || 0),
        umbralCrit: parseFloat(document.getElementById('matUmbralCrit').value || 0),
        img: document.getElementById('matImg').value.trim(),
        precioCompra: parseFloat(document.getElementById('matPrecioCompra').value || 0),
        precioVenta: parseFloat(document.getElementById('matPrecioVenta').value || 0),
        freq: document.getElementById('matFreq').value
    };
    MATERIALES.push(m);
    renderMateriales();
    renderKPIs();
    fillMaterialSelects();
    // Despacho opcional
    const centroSel = document.getElementById('matCentro').value;
    const dia = parseInt(document.getElementById('matDia').value || '1', 10);
    const hora = document.getElementById('matHora').value || '10:00';
    if (m.freq !== 'manual' && centroSel) {
        const base = new Date();
        base.setDate(dia || 1);
        const push = (d) => DESPACHOS.push({
            fecha: fmtDate(d),
            hora,
            material: id,
            centro: centroSel
        });
        push(base);
        if (m.freq === 'semanal') {
            for (let i = 1; i <= 3; i++) {
                const dd = new Date(base);
                dd.setDate(dd.getDate() + 7 * i);
                push(dd);
            }
        }
        if (m.freq === 'quincenal') {
            for (let i = 1; i <= 3; i++) {
                const dd = new Date(base);
                dd.setDate(dd.getDate() + 15 * i);
                push(dd);
            }
        }
        if (m.freq === 'mensual') {
            for (let i = 1; i <= 3; i++) {
                const dd = new Date(base);
                dd.setMonth(dd.getMonth() + i);
                push(dd);
            }
        }
        renderCalendar();
    }
    showToast('Material registrado');
    bootstrap.Modal.getInstance(document.getElementById('modalRegistrarMaterial')).hide();
    e.target.reset();
    document.getElementById('matOcupacionBar').style.width = '0%';
    document.getElementById('matOcupacionPct').textContent = '0%';
});

document.getElementById('matPlanificarBtn').addEventListener('click', () => {
    showToast('Se planificarán eventos al guardar el material.');
});

// =================== DETALLE MATERIAL (Modal) ===================
let modalMaterial;
const mmCalState = {
    date: new Date(),
    material: null
};

function abrirMaterial(id) {
    const m = MATERIALES.find(x => x.id === id);
    if (!m) return;
    document.getElementById('mmNombre').textContent = m.nombre;
    document.getElementById('mmTipo').textContent = m.tipo;
    document.getElementById('mmCategoria').textContent = m.categoria;
    document.getElementById('mmImg').src = m.img || 'https://picsum.photos/seed/mat' + m.id + '/200/200';
    document.getElementById('mmAlmacen').textContent = m.almacenamiento || '—';
    document.getElementById('mmCap').textContent = m.capacidad || '—';
    document.getElementById('mmUmbral').textContent = m.umbral || '—';
    document.getElementById('mmUmbralCrit').textContent = m.umbralCrit || '—';
    document.getElementById('mmDesc').textContent = m.desc || '—';
    document.getElementById('mmPrecioCompra').value = m.precioCompra || 0;
    document.getElementById('mmPrecioVenta').value = m.precioVenta || 0;

    const ent = HIST_COMPRAS.filter(h => h.material === id);
    const sal = HIST_SALIDAS.filter(h => h.material === id);
    document.getElementById('mmEntradas').innerHTML = ent.map(h => `<tr><td>${h.fecha}</td><td>${h.kg}</td><td>${h.proveedor}</td><td>${(h.punit * h.kg).toLocaleString('es-CO')}</td></tr>`).join('') || '<tr><td colspan="4" class="text-muted">Sin entradas</td></tr>';
    document.getElementById('mmSalidas').innerHTML = sal.map(h => `<tr><td>${h.fecha}</td><td>${h.kg}</td><td>${h.centro}</td><td>${(h.punit * h.kg).toLocaleString('es-CO')}</td></tr>`).join('') || '<tr><td colspan="4" class="text-muted">Sin salidas</td></tr>';

    mmCalState.date = new Date();
    mmCalState.material = id;
    renderMmCalendar();
    modalMaterial = modalMaterial || new bootstrap.Modal(document.getElementById('modalMaterial'));
    modalMaterial.show();
}
document.getElementById('mmGuardarInfo').addEventListener('click', () => {
    showToast('Información de material guardada');
});

function renderMmCalendar() {
    const d = mmCalState.date,
        matId = mmCalState.material;
    document.getElementById('mmCalTitulo').textContent = d.toLocaleDateString('es-CO', {
        month: 'long',
        year: 'numeric'
    });
    const first = startOfMonth(d),
        last = endOfMonth(d),
        firstDow = (first.getDay() + 6) % 7,
        days = last.getDate();
    const grid = document.getElementById('mmCalGrid');
    grid.innerHTML = '';
    for (let i = 0; i < firstDow; i++) grid.appendChild(document.createElement('div'));
    for (let day = 1; day <= days; day++) {
        const cell = document.createElement('div');
        cell.className = 'day';
        const date = new Date(d.getFullYear(), d.getMonth(), day);
        const iso = fmtDate(date);
        const label = document.createElement('div');
        label.className = 'small text-muted';
        label.textContent = day;
        cell.appendChild(label);
        DESPACHOS.filter(e => e.fecha === iso && e.material === matId).forEach(e => {
            const ev = document.createElement('span');
            ev.className = 'event';
            ev.textContent = `${e.hora} · ${e.centro}`;
            cell.appendChild(ev);
        });
        grid.appendChild(cell);
    }
}
document.getElementById('mmPrev').addEventListener('click', () => {
    mmCalState.date.setMonth(mmCalState.date.getMonth() - 1);
    renderMmCalendar();
});
document.getElementById('mmNext').addEventListener('click', () => {
    mmCalState.date.setMonth(mmCalState.date.getMonth() + 1);
    renderMmCalendar();
});
document.getElementById('mmPlanificar').addEventListener('click', () => {
    const id = mmCalState.material;
    if (!id) return;
    const freq = document.getElementById('mmFreq').value;
    const dia = parseInt(document.getElementById('mmDia').value || '1', 10);
    const base = new Date(mmCalState.date.getFullYear(), mmCalState.date.getMonth(), dia);
    const push = (d) => DESPACHOS.push({
        fecha: fmtDate(d),
        hora: '10:00',
        material: id,
        centro: 'Centro definido'
    });
    push(base);
    if (freq === 'semanal') {
        for (let i = 1; i <= 3; i++) {
            const dd = new Date(base);
            dd.setDate(dd.getDate() + 7 * i);
            push(dd);
        }
    }
    if (freq === 'quincenal') {
        for (let i = 1; i <= 3; i++) {
            const dd = new Date(base);
            dd.setDate(dd.getDate() + 15 * i);
            push(dd);
        }
    }
    if (freq === 'mensual') {
        for (let i = 1; i <= 3; i++) {
            const dd = new Date(base);
            dd.setMonth(dd.getMonth() + i);
            push(dd);
        }
    }
    renderMmCalendar();
    renderCalendar();
    showToast('Frecuencia planificada');
});

// =================== HISTORIAL GLOBAL ===================
function fillMaterialSelects() {
    const opts = ['<option value="">Todos los materiales</option>', ...MATERIALES.map(m => `<option value="${m.id}">${m.nombre}</option>`)].join('');
    document.getElementById('hcMaterial').innerHTML = opts;
    document.getElementById('hsMaterial').innerHTML = opts;
    document.getElementById('calMat').innerHTML = MATERIALES.map(m => `<option value="${m.id}">${m.nombre}</option>`).join('');
}

function renderHistorial() {
    const f = (list, matSel, desdeId, hastaId, tbodyId) => {
        const material = +document.getElementById(matSel).value || null;
        const desde = document.getElementById(desdeId).value || '0000-01-01';
        const hasta = document.getElementById(hastaId).value || '9999-12-31';
        const rows = list.filter(x => (!material || x.material === material) && x.fecha >= desde && x.fecha <= hasta)
            .map(x => {
                const mat = MATERIALES.find(m => m.id === x.material);
                const total = (x.punit || 0) * (x.kg || 0);
                const tercero = x.proveedor || x.centro || '—';
                return `<tr><td>${x.fecha}</td><td>${mat?.nombre || '—'}</td><td>${x.kg}</td><td>${tercero}</td><td>${(x.punit || 0).toLocaleString('es-CO')}</td><td>${total.toLocaleString('es-CO')}</td></tr>`;
            }).join('');
        document.getElementById(tbodyId).innerHTML = rows || `<tr><td colspan="6" class="text-muted">Sin registros</td></tr>`;
    };
    f(HIST_COMPRAS, 'hcMaterial', 'hcDesde', 'hcHasta', 'hcTabla');
    f(HIST_SALIDAS, 'hsMaterial', 'hsDesde', 'hsHasta', 'hsTabla');
}
document.getElementById('hcFiltrar').addEventListener('click', renderHistorial);
document.getElementById('hsFiltrar').addEventListener('click', renderHistorial);

// =================== CALENDARIO GLOBAL ===================
const calState = {
    date: new Date()
};

function renderCalendar() {
    const d = calState.date;
    const first = startOfMonth(d);
    const last = endOfMonth(d);
    const firstDow = (first.getDay() + 6) % 7;
    const days = last.getDate();
    document.getElementById('calTitulo').textContent = d.toLocaleDateString('es-CO', {
        month: 'long',
        year: 'numeric'
    });
    const grid = document.getElementById('calGrid');
    grid.innerHTML = '';
    for (let i = 0; i < firstDow; i++) {
        grid.appendChild(document.createElement('div'))
    }
    for (let day = 1; day <= days; day++) {
        const cell = document.createElement('div');
        cell.className = 'day';
        const date = new Date(d.getFullYear(), d.getMonth(), day);
        const iso = fmtDate(date);
        const label = document.createElement('div');
        label.className = 'small text-muted';
        label.textContent = day;
        cell.appendChild(label);
        DESPACHOS.filter(e => e.fecha === iso).forEach(e => {
            const mat = MATERIALES.find(m => m.id === e.material);
            const ev = document.createElement('span');
            ev.className = 'event';
            ev.textContent = `${e.hora} · ${mat?.nombre || '—'} (${e.centro})`;
            cell.appendChild(ev);
        });
        const hoy = new Date();
        if (date.toDateString() === hoy.toDateString()) cell.classList.add('today');
        grid.appendChild(cell);
    }
}
document.getElementById('calPrev').addEventListener('click', () => {
    calState.date.setMonth(calState.date.getMonth() - 1);
    renderCalendar();
});
document.getElementById('calNext').addEventListener('click', () => {
    calState.date.setMonth(calState.date.getMonth() + 1);
    renderCalendar();
});
document.getElementById('calGuardar').addEventListener('click', () => {
    const id = +document.getElementById('calMat').value;
    if (!id) return;
    const fecha = document.getElementById('calFecha').value;
    if (!fecha) return;
    const hora = document.getElementById('calHora').value || '10:00';
    const centro = document.getElementById('calCentro').value || '—';
    const freq = document.getElementById('calFreq').value;
    DESPACHOS.push({
        fecha,
        hora,
        material: id,
        centro
    });
    const d0 = new Date(fecha + 'T00:00:00');
    const add = (n) => {
        const dd = new Date(d0);
        dd.setDate(dd.getDate() + n);
        DESPACHOS.push({
            fecha: fmtDate(dd),
            hora,
            material: id,
            centro
        });
    };
    if (freq === 'semanal') {
        add(7);
        add(14);
        add(21);
    }
    if (freq === 'quincenal') {
        add(15);
        add(30);
        add(45);
    }
    if (freq === 'mensual') {
        for (let i = 1; i <= 3; i++) {
            const mm = new Date(d0);
            mm.setMonth(mm.getMonth() + i);
            DESPACHOS.push({
                fecha: fmtDate(mm),
                hora,
                material: id,
                centro
            });
        }
    }
    renderCalendar();
    showToast('Despacho programado');
});

// =================== PROVEEDORES/CENTROS ===================
function renderProveedores() {
    const tbody = document.getElementById('provTabla');
    tbody.innerHTML = PROV.map(p => `<tr><td>${p.nombre}</td><td>${p.tipo}</td><td>${p.contacto}</td><td>${p.tel}</td><td>${p.mail}</td><td class="text-end"><button class="btn btn-sm btn-outline-secondary">Editar</button></td></tr>`).join('');
    fillCentrosSelects();
}
document.getElementById('formProveedor').addEventListener('submit', (e) => {
    e.preventDefault();
    const p = {
        nombre: document.getElementById('provNombre').value,
        tipo: document.getElementById('provTipo').value,
        contacto: document.getElementById('provContacto').value,
        tel: document.getElementById('provTelefono').value,
        mail: document.getElementById('provEmail').value,
        dir: document.getElementById('provDireccion').value
    };
    PROV.push(p);
    renderProveedores();
    showToast('Centro/Proveedor creado');
    bootstrap.Modal.getInstance(document.getElementById('modalProveedor')).hide();
    e.target.reset();
});

// =================== CONVERSACIONES (demo) ===================
const THREADS = [{
    id: 1,
    titulo: 'Ciudadano A',
    preview: '¿Dónde está el punto…?',
    msgs: [{
        from: 'user',
        text: '¿Dónde está el punto de reciclaje más cercano?'
    }, {
        from: 'eca',
        text: 'Estamos en la Calle 5 #123. Atendemos 8:00–17:00.'
    }]
},
{
    id: 2,
    titulo: 'Ciudadano B',
    preview: '¿Aceptan plástico hoy?',
    msgs: [{
        from: 'user',
        text: '¿Aceptan plástico hoy?'
    }, {
        from: 'eca',
        text: 'Sí, de 8 a 5 pm.'
    }]
}
];

function renderThreads() {
    const list = document.getElementById('threadList');
    list.innerHTML = THREADS.map((t, i) => `
        <button class="list-group-item list-group-item-action ${i === 0 ? 'active' : ''}" data-thread-id="${t.id}">
          <div class="fw-semibold">${t.titulo}</div>
          <small class="text-muted">${t.preview}</small>
        </button>`).join('');
    bindThreadClicks();
    loadThread(THREADS[0].id);
}

function bindThreadClicks() {
    document.getElementById('threadList').addEventListener('click', (e) => {
        const btn = e.target.closest('[data-thread-id]');
        if (!btn) return;
        document.querySelectorAll('#threadList .list-group-item').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        loadThread(+btn.dataset.threadId);
    }, {
        once: true
    });
}

function loadThread(id) {
    const t = THREADS.find(x => x.id === id);
    const win = document.getElementById('chatWindow');
    win.innerHTML = t.msgs.map(m => `<div class="message ${m.from === 'user' ? 'user' : 'eca'}">${m.text}</div>`).join('');
    win.dataset.threadId = id;
    win.scrollTop = win.scrollHeight;
}
document.getElementById('chatSend').addEventListener('click', () => {
    const input = document.getElementById('chatInput');
    const text = input.value.trim();
    if (!text) return;
    const win = document.getElementById('chatWindow');
    const id = +win.dataset.threadId;
    THREADS.find(t => t.id === id).msgs.push({
        from: 'eca',
        text
    }); // enviar como ECA
    const bubble = document.createElement('div');
    bubble.className = 'message eca';
    bubble.textContent = text;
    win.appendChild(bubble);
    win.scrollTop = win.scrollHeight;
    input.value = '';
    // TODO: POST /api/conversations/{id}/messages
});

// =================== INIT ===================
['fTipo', 'fCategoria', 'fTexto'].forEach(id => document.getElementById(id).addEventListener('input', renderMateriales));

function init() {
    renderKPIs();
    renderMateriales();
    renderHistorial();
    renderCalendar();
    renderProveedores();
    renderThreads();
    fillMaterialSelects();
    fillCentrosSelects();
}
init();
