{{-- resources/views/Mapa/mapa.blade.php --}}
<x-app-layout>

    {{-- NAVBAR de InfoRecicla --}}
    <x-navbar-layout />

    {{-- Estilos del mapa --}}
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="{{ asset('css/Mapa/styleMapa.css') }}" />

    <div class="container-fluid py-3">
        <div class="row g-3">
            {{-- Columna izquierda: buscador + lista --}}
            <aside class="col-lg-3 col-md-4">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h5 class="mb-0">Puntos ECA</h5>
                    <button class="btn btn-sm btn-outline-success" id="btnCentrar">Centrar Bogotá</button>
                </div>

                <div class="input-group mb-3">
                    <span class="input-group-text">🔎</span>
                    <input
                        type="search"
                        id="filtro"
                        class="form-control"
                        placeholder="Buscar por nombre, localidad o dirección…">
                </div>

                <div id="lista" class="vstack gap-2"><!-- cards aquí --></div>
            </aside>

            {{-- Columna derecha: mapa --}}
            <section class="col-lg-9 col-md-8">
                <div
                    id="map"
                    class="shadow-sm"
                    {{-- Pasamos los puntos al JS externo SIN script inline --}}
                    data-puntos='@json(($list ?? []), JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)'>
                </div>
            </section>
        </div>
    </div>

    {{-- Modal de detalle de Punto ECA --}}
    <div class="modal fade" id="modalPunto" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="modalTitle">Punto ECA</h5>
                    <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-5">
                            <img id="modalImg" src="{{ asset('images/eca-default.png') }}" class="img-fluid rounded" alt="">
                            <div class="mt-2">
                                <span class="badge text-bg-light border" id="modalCategoria">—</span>
                                <span class="badge text-bg-light border" id="modalLocalidad">—</span>
                            </div>
                        </div>
                        <div class="col-md-7">
                            <dl class="row mb-0">
                                <dt class="col-sm-4">Dirección</dt>
                                <dd class="col-sm-8" id="modalDireccion">—</dd>
                                <dt class="col-sm-4">Horario</dt>
                                <dd class="col-sm-8" id="modalHorario">—</dd>
                                <dt class="col-sm-4">Contacto</dt>
                                <dd class="col-sm-8" id="modalContacto">—</dd>
                                <dt class="col-sm-4">Correo</dt>
                                <dd class="col-sm-8" id="modalCorreo">—</dd>
                                <dt class="col-sm-4">Teléfono</dt>
                                <dd class="col-sm-8" id="modalTelefono">—</dd>
                                <dt class="col-sm-4">Web</dt>
                                <dd class="col-sm-8"><a id="modalWeb" href="#" target="_blank" rel="noopener">—</a></dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Librerías --}}
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    {{-- Tu JS externo (debe existir en public/js/Mapa/mapa.js) --}}
    <script src="{{ asset('js/Mapa/mapa.js') }}"></script>
</x-app-layout>