{{-- resources/views/Mapa/mapa.blade.php --}}
<x-app-layout>
    <div class="container-fluid py-3">
        <div class="row g-3">
            {{-- Lista vertical --}}
            <aside class="col-lg-4 col-md-5">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <h5 class="mb-0">Puntos ECA</h5>
                    @if($selectedId)
                    <a href="{{ route('mapa') }}" class="btn btn-sm btn-outline-secondary">Ver todos</a>
                    @endif
                </div>

                <div class="vstack gap-2">
                    @forelse($list as $p)
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h6 class="mb-1">{{ $p->nombre }}</h6>
                            <div class="small text-muted mb-2">
                                {{ $p->ciudad ?? '—' }}@if($p->localidad) • {{ $p->localidad }} @endif
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
                                    @if($p->sitio_web)
                                    <a href="{{ $p->sitio_web }}" target="_blank" rel="noopener">{{ $p->sitio_web }}</a>
                                    @else
                                    —
                                    @endif
                                </dd>

                                <dt class="col-4">NIT</dt>
                                <dd class="col-8">{{ $p->nit ?? '—' }}</dd>
                            </dl>

                            @if(is_null($p->latitud) || is_null($p->longitud))
                            <div class="mt-2 small text-danger">Sin coordenadas</div>
                            @else
                            <div class="mt-2">
                                <a class="btn btn-sm btn-outline-success"
                                    href="{{ route('mapa', ['id' => $p->id]) }}">
                                    Ver en el mapa
                                </a>
                            </div>
                            @endif
                        </div>
                    </div>
                    @empty
                    <p class="text-muted">No hay puntos para mostrar.</p>
                    @endforelse
                </div>
            </aside>

            {{-- Mapa estático con marcadores en la propia imagen + áreas clicables --}}
            <section class="col-lg-8 col-md-7">
                <div class="border rounded overflow-hidden shadow-sm">
                    <img
                        src="{{ $map['url'] }}"
                        width="{{ $map['width'] }}"
                        height="{{ $map['height'] }}"
                        alt="Mapa estático"
                        class="d-block"
                        usemap="#puntos-eca">
                </div>

                {{-- Áreas clicables (sin CSS/JS) --}}
                <map name="puntos-eca">
                    @foreach($areas as $a)
                    <area
                        shape="circle"
                        coords="{{ $a['x'] }},{{ $a['y'] }},{{ $a['r'] }}"
                        href="{{ route('mapa', ['id' => $a['id']]) }}"
                        alt="{{ $a['title'] }}"
                        title="{{ $a['title'] }}">
                    @endforeach
                </map>

                <div class="small text-muted mt-2">
                    Mapa estático OpenStreetMap • Centro:
                    {{ number_format($map['centerLat'], 5) }}, {{ number_format($map['centerLon'], 5) }}
                    • Zoom {{ $map['zoom'] }}
                </div>
            </section>
        </div>
    </div>
</x-app-layout>