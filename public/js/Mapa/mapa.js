


/* ============================
1) Datos de ejemplo (cámbialos por tu API/BD)
============================ */
const PUNTOS = [{
    id: 1,
    nombre: "Punto ECA Suba",
    direccion: "Calle 132 # 54-10",
    localidad: "Suba",
    horario: "Lun–Sáb 8:00–17:00",
    contacto: "Juan Pérez",
    correo: "contacto@ecasuba.co",
    telefono: "320 555 1234",
    materiales: ["Plástico", "Papel y cartón", "Vidrio"],
    web: "https://ejemplo.com/suba",
    img: "../images/eca-default.png",
    lat: 4.7399,
    lng: -74.0833,
    categoria: "Reciclaje"
},
{
    id: 2,
    nombre: "Ecopunto Norte",
    direccion: "Autopista Nte. #170",
    localidad: "Usaquén",
    horario: "Lun–Vie 9:00–18:00",
    contacto: "Ana Torres",
    correo: "info@ecopuntonorte.co",
    telefono: "310 987 6543",
    materiales: ["Plástico", "Metales"],
    web: "https://ejemplo.com/norte",
    img: "../images/eca-default.png",
    lat: 4.7580,
    lng: -74.0417,
    categoria: "Reciclaje"
},
{
    id: 3,
    nombre: "Punto ECA Kennedy",
    direccion: "Av. 1 de Mayo # 78",
    localidad: "Kennedy",
    horario: "Lun–Dom 7:00–19:00",
    contacto: "Carlos Rojas",
    correo: "kenny@eca.co",
    telefono: "301 123 4567",
    materiales: ["Vidrio", "Metales", "Papel y cartón"],
    web: "https://ejemplo.com/kennedy",
    img: "../images/eca-default.png",
    lat: 4.6270,
    lng: -74.1480,
    categoria: "Reciclaje"
}
];

/* ============================
   2) Inicialización del mapa (Leaflet + OSM)
   - Sin geolocalización del navegador
   - Sin llave/Token
   ============================ */
const BOGOTA = [4.7110, -74.0721];
const map = L.map('map', {
    zoomControl: true
}).setView(BOGOTA, 12);

// Capa de tiles libre (no requiere API key)
const tiles = L.tileLayer(
    'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; OpenStreetMap',
    maxZoom: 19
}
);
tiles.addTo(map);

/* ============================
   3) Marcadores + Popups sencillos
   ============================ */
const markers = new Map(); // id -> marker

function addMarker(p) {
    const m = L.marker([p.lat, p.lng]).addTo(map);
    m.bindPopup(`<strong>${p.nombre}</strong><br><small>${p.direccion}</small>`);
    m.on('click', () => abrirModal(p));
    markers.set(p.id, m);
}
PUNTOS.forEach(addMarker);

/* ============================
   4) Lista vertical (cards)
   ============================ */
const lista = document.getElementById('lista');

function renderLista(items) {
    lista.innerHTML = items.map(p => `
        <article class="card card-hover shadow-sm" data-id="${p.id}">
          <div class="card-body">
            <div class="d-flex align-items-start gap-3">
              <img src="${p.img}" width="64" height="64" class="rounded" style="object-fit:cover" alt="">
              <div class="flex-grow-1">
                <div class="d-flex justify-content-between">
                  <h6 class="mb-1">${p.nombre}</h6>
                  <span class="badge text-bg-light border">${p.localidad}</span>
                </div>
                <div class="small text-muted">${p.direccion}</div>
                <div class="small mt-1"><strong>Horarios:</strong> ${p.horario}</div>
                <div class="mt-2">
                  ${p.materiales.map(m => `<span class="badge bg-success-subtle text-success border me-1">${m}</span>`).join('')}
                </div>
              </div>
            </div>
          </div>
        </article>
      `).join('');

    // click en card -> centra mapa + modal
    lista.querySelectorAll('[data-id]').forEach(card => {
        card.addEventListener('click', () => {
            const id = Number(card.dataset.id);
            const p = PUNTOS.find(x => x.id === id);
            if (!p) return;
            map.setView([p.lat, p.lng], 14, {
                animate: true
            });
            const mk = markers.get(id);
            if (mk) mk.openPopup();
            abrirModal(p);
        });
    });
}
renderLista(PUNTOS);

/* ============================
   5) Filtro de lista (cliente)
   ============================ */
document.getElementById('filtro').addEventListener('input', (e) => {
    const q = e.target.value.toLowerCase().trim();
    const filtered = PUNTOS.filter(p =>
        p.nombre.toLowerCase().includes(q) ||
        p.localidad.toLowerCase().includes(q) ||
        p.direccion.toLowerCase().includes(q)
    );
    renderLista(filtered);
});

// Botón centrar Bogotá
document.getElementById('btnCentrar').addEventListener('click', () => {
    map.setView(BOGOTA, 12, {
        animate: true
    });
});

/* ============================
   6) Modal: abrir con datos
   ============================ */
const modal = new bootstrap.Modal(document.getElementById('modalPunto'));

function abrirModal(p) {
    document.getElementById('modalTitle').textContent = p.nombre;
    document.getElementById('modalImg').src = p.img || '../images/eca-default.png';
    document.getElementById('modalCategoria').textContent = p.categoria || '—';
    document.getElementById('modalLocalidad').textContent = p.localidad || '—';
    document.getElementById('modalDireccion').textContent = p.direccion || '—';
    document.getElementById('modalHorario').textContent = p.horario || '—';
    document.getElementById('modalContacto').textContent = p.contacto || '—';
    document.getElementById('modalCorreo').textContent = p.correo || '—';
    document.getElementById('modalTelefono').textContent = p.telefono || '—';
    document.getElementById('modalMateriales').innerHTML = (p.materiales || []).join(', ') || '—';
    const a = document.getElementById('modalWeb');
    a.textContent = p.web ? 'Visitar sitio' : '—';
    a.href = p.web || '#';
    modal.show();
}

/* ============================
   7) Envío de mensajes (simulado)
   ============================ */
const formMsg = document.getElementById('formMsg');
const msgOk = document.getElementById('msgOk');
formMsg.addEventListener('submit', (e) => {
    e.preventDefault();
    // TODO: POST /api/conversations (o /api/eca/{id}/messages)
    formMsg.reset();
    msgOk.classList.remove('d-none');
    setTimeout(() => msgOk.classList.add('d-none'), 2000);
});

/* ============================
   8) Integración futura con tu BD
   ============================
   - Reemplaza PUNTOS por:
     const PUNTOS = await (await fetch('/api/eca/points?city=Bogota')).json();
   - Para cada punto, asegúrate de incluir: id, nombre, lat, lng, direccion, localidad, horario,
     contacto, correo, telefono, materiales[], web, img (opcional).
   - Si quieres paginación en la lista, aplica en el endpoint y pinta con renderLista().
*/
