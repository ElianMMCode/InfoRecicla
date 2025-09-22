{{-- resources/views/Mapa/mapa.blade.php --}}
<x-app-layout>

    {{-- NAVBAR de InfoRecicla (restaurado) --}}
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

                <div class="vstack gap-2">
                    @forelse($list as $p)
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <h6 class="mb-1">{{ $p->nombre }}</h6>
                                <div class="small text-muted mb-2">
                                    {{ $p->ciudad ?? '—' }}@if ($p->localidad)
                                        • {{ $p->localidad }}
                                    @endif
                                </div>

                                <dl class="row mb-0 small">
                                    <dt class="col-4">Dirección</dt>
                                    <dd class="col-8">{{ $p->direccion ?? '—' }}</dd>

                                    <dt class="col-4">Horario</dt>
                                    <dd class="col-8">{{ $p->horario_atencion ?? '—' }}</dd>

                                    <dt class="col-4">Teléfono</dt>
                                    <dd class="col-8">{{ $p->telefonoPunto ?? '—' }}</dd>

                                    <dt class="col-4">Correo</dt>
                                    <dd class="col-8">{{ $p->correoPunto ?? '—' }}</dd>

                                    <dt class="col-4">Web</dt>
                                    <dd class="col-8">
                                        @if ($p->sitio_web)
                                            <a href="{{ $p->sitio_web }}" target="_blank"
                                                rel="noopener">{{ $p->sitio_web }}</a>
                                        @else
                                            —
                                        @endif
                                    </dd>

                                    <dt class="col-4">NIT</dt>
                                    <dd class="col-8">{{ $p->nit ?? '—' }}</dd>
                                </dl>

                                @if (is_null($p->latitud) || is_null($p->longitud))
                                    <div class="mt-2 small text-danger">Sin coordenadas</div>
                                @else
                                    <div class="mt-2">
                                        <span class="badge text-bg-light border" id="modalCategoria">—</span>
                                        <span class="badge text-bg-light border" id="modalLocalidad">—</span>
                                    </div>
                                @endif
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
