
/* ============================
   0) Config
   ============================ */
const BOGOTA = [4.7110, -74.0721];
const ENDPOINT = '/api/eca/puntos'; // Laravel route

/* ============================
   1) Inicializar Leaflet
   ============================ */
const map = L.map('map', { zoomControl: true }).setView(BOGOTA, 12);

// Tiles OSM oficiales + atribución obligatoria. :contentReference[oaicite:2]{index=2}
L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
  maxZoom: 19,
  attribution:
    '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
}).addTo(map);

/* ============================
   2) Estado y utilidades
   ============================ */
let PUNTOS = [];
const markers = new Map(); // id -> Leaflet marker
const lista = document.getElementById('lista');
const filtroInput = document.getElementById('filtro');
const btnCentrar = document.getElementById('btnCentrar');

// Modal Bootstrap (si existe en tu Blade)
const modalEl = document.getElementById('modalPunto');
const modal = modalEl ? new bootstrap.Modal(modalEl) : null;

/* ============================
   3) Cargar datos desde Laravel
   ============================ */
async function cargarPuntos(q = '') {
  const url = q ? `${ENDPOINT}?q=${encodeURIComponent(q)}` : ENDPOINT;
  const res = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
  if (!res.ok) throw new Error('Error al cargar puntos');
  const data = await res.json();
  PUNTOS = (data && data.puntos) || [];
  pintarMapa(PUNTOS);
  renderLista(PUNTOS);
}

/* ============================
   4) Pintar mapa y marcadores
   ============================ */
function clearMarkers() {
  markers.forEach(mk => map.removeLayer(mk));
  markers.clear();
}

function addMarker(p) {
  const mk = L.marker([p.lat, p.lng]).addTo(map);
  mk.bindPopup(`<strong>${p.nombre}</strong><br><small>${p.direccion ?? '—'}</small>`);
  mk.on('click', () => abrirModal(p));
  markers.set(p.id, mk);
}

function pintarMapa(items) {
  clearMarkers();
  items.forEach(p => addMarker(p));

  if (items.length) {
    const group = L.featureGroup([...markers.values()]);
    map.fitBounds(group.getBounds().pad(0.2));
  } else {
    map.setView(BOGOTA, 12);
  }
}

/* ============================
   5) Lista vertical (Bootstrap)
   ============================ */
function renderLista(items) {
  lista.innerHTML = items.map(p => `
    <article class="card card-hover shadow-sm" data-id="${p.id}">
      <div class="card-body">
        <div class="d-flex align-items-start gap-3">
          <img src="${p.img || '../images/eca-default.png'}" width="64" height="64" class="rounded" style="object-fit:cover" alt="">
          <div class="flex-grow-1">
            <div class="d-flex justify-content-between">
              <h6 class="mb-1">${p.nombre}</h6>
              <span class="badge text-bg-light border">${p.localidad ?? '—'}</span>
            </div>
            <div class="small text-muted">${p.direccion ?? '—'}</div>
            <div class="small mt-1"><strong>Horarios:</strong> ${p.horario ?? '—'}</div>
            <div class="mt-2">
              ${(p.materiales || []).map(m => `<span class="badge bg-success-subtle text-success border me-1">${m}</span>`).join('')}
            </div>
          </div>
        </div>
      </div>
    </article>
  `).join('');

  lista.querySelectorAll('[data-id]').forEach(card => {
    card.addEventListener('click', () => {
      const id = String(card.dataset.id);
      const p = PUNTOS.find(x => String(x.id) === id);
      if (!p) return;
      map.setView([p.lat, p.lng], 14, { animate: true });
      const mk = markers.get(p.id);
      if (mk) mk.openPopup();
      abrirModal(p);
    });
  });
}

/* ============================
   6) Filtros y acciones
   ============================ */
if (filtroInput) {
  filtroInput.addEventListener('input', (e) => {
    const q = e.target.value.toLowerCase().trim();
    const filtered = PUNTOS.filter(p =>
      (p.nombre || '').toLowerCase().includes(q) ||
      (p.localidad || '').toLowerCase().includes(q) ||
      (p.direccion || '').toLowerCase().includes(q)
    );
    pintarMapa(filtered);
    renderLista(filtered);
  });
}

if (btnCentrar) {
  btnCentrar.addEventListener('click', () => map.setView(BOGOTA, 12, { animate: true }));
}

/* ============================
   7) Modal (opcional)
   ============================ */
function abrirModal(p) {
  if (!modal) return; // si no usas modal
  document.getElementById('modalTitle').textContent = p.nombre;
  document.getElementById('modalImg').src = p.img || '../images/eca-default.png';
  document.getElementById('modalCategoria').textContent = p.categoria || '—';
  document.getElementById('modalLocalidad').textContent = p.localidad || '—';
  document.getElementById('modalDireccion').textContent = p.direccion || '—';
  document.getElementById('modalHorario').textContent = p.horario || '—';
  document.getElementById('modalContacto').textContent = p.contacto || '—';
  document.getElementById('modalCorreo').textContent = p.correo || '—';
  document.getElementById('modalTelefono').textContent = p.telefono || '—';
  document.getElementById('modalMateriales').innerHTML =
    (p.materiales || []).join(', ') || '—';
  const a = document.getElementById('modalWeb');
  a.textContent = p.web ? 'Visitar sitio' : '—';
  a.href = p.web || '#';
  modal.show();
}

/* ============================
   8) Go!
   ============================ */
cargarPuntos().catch(err => {
  console.error(err);
  lista.innerHTML = `<div class="alert alert-danger">No se pudieron cargar los puntos.</div>`;
});