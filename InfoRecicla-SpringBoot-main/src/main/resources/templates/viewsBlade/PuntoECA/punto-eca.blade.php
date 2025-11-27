<x-app-layout>


    <x-navbar-layout>
    </x-navbar-layout>

    <link rel="stylesheet" href="{{ asset('css/PuntoECA/punto-eca.css') }}">
    <main class="container my-4">
        <!-- TABS DE TODAS LAS SECCIONES-->
        <ul class="nav nav-pills" id="mainTabs" role="tablist">
            <!-- Dependiendo de la seccion que se ingrese por url esta se vera activa-->
            <li class="nav-item" role="presentation">
                <a href="{{ route('eca.index', ['seccion' => 'resumen']) }}"
                    class="nav-link {{ $seccion === 'resumen' ? 'active' : '' }}">Resumen</a>
            </li>
            <li class="nav-item" role="presentation">
                <a href="{{ route('eca.index', ['seccion' => 'perfil']) }}"
                    class="nav-link {{ $seccion === 'perfil' ? 'active' : '' }}">Perfil</a>
            </li>
            <li class="nav-item" role="presentation">
                <a href="{{ route('eca.index', ['seccion' => 'materiales']) }}"
                    class="nav-link {{ $seccion === 'materiales' ? 'active' : '' }}">Materiales</a>
            </li>
            <li class="nav-item" role="presentation">
                <a href="{{ route('eca.index', ['seccion' => 'movimientos']) }}"
                    class="nav-link {{ $seccion === 'movimientos' ? 'active' : '' }}">Movimientos</a>
            </li>
            <li class="nav-item" role="presentation">
                <a href="{{ route('eca.index', ['seccion' => 'historial']) }}"
                    class="nav-link {{ $seccion === 'historial' ? 'active' : '' }}">Historial</a>
            </li>
            <li class="nav-item" role="presentation">
                <a href="{{ route('eca.index', ['seccion' => 'calendario']) }}"
                    class="nav-link {{ $seccion === 'calendario' ? 'active' : '' }}">Calendario</a>
            </li>
            <li class="nav-item" role="presentation">
                <a href="{{ route('eca.index', ['seccion' => 'centros']) }}"
                    class="nav-link {{ $seccion === 'centros' ? 'active' : '' }}">Centros</a>
            </li>
            {{-- <li class="nav-item" role="presentation">
                <a href="{{ route('eca.index', ['seccion' => 'conversaciones']) }}"
                    class="nav-link {{ $seccion === 'conversaciones' ? 'active' : '' }}">Conversaciones</a>
            </li> --}}
            <li class="nav-item" role="presentation">
                <a href="{{ route('eca.index', ['seccion' => 'configuracion']) }}"
                    class="nav-link {{ $seccion === 'configuracion' ? 'active' : '' }}">Configuración</a>
            </li>
        </ul>

        <div class="tab-content pt-3">

            <!-- RESUMEN -->
            @if ($seccion === 'resumen')
                <section class="tab-pane fade show active" id="tab-panel">
                    @include('components.flash-messages')
                    <div class="row g-3">
                        <div class="col-md-3">
                            <div class="card card-hover">
                                <div class="card-body">
                                    <div class="text-muted small">Inventario total (kg)</div>
                                    <div class="h4 mb-0" id="kpiInventario">—</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card card-hover">
                                <div class="card-body">
                                    <div class="text-muted small">Entradas mes (kg)</div>
                                    <div class="h4 mb-0" id="kpiEntradasMes">—</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card card-hover">
                                <div class="card-body">
                                    <div class="text-muted small">Salidas mes (kg)</div>
                                    <div class="h4 mb-0" id="kpiSalidasMes">—</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card card-hover">
                                <div class="card-body">
                                    <div class="text-muted small">Próximo despacho</div>
                                    <div class="h6 mb-0" id="kpiProximoDespacho">—</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card card-hover mt-3">
                        <div
                            class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                            <strong>Alertas de capacidad / umbrales</strong>
                            <span class="badge badge-soft" id="alertCount">0</span>
                        </div>
                        <div class="card-body">
                            <div id="alertList" class="vstack gap-2 small text-muted">Sin alertas.</div>
                        </div>
                    </div>
                    {{-- (Tabla de ocupación movida a la pestaña Materiales) --}}
                </section>
            @endif

            @if ($seccion === 'perfil')
                <section class="tab-pane fade show active" id="tab-perfil">
                    @include('components.flash-messages')
                    <form action="{{ route('eca.perfil.update') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row g-4">
                            {{-- = Encargado = --}}
                            <div class="col-lg-6">
                                <div class="card card-hover h-100">
                                    <div class="card-body">
                                        <h5 class="mb-3">Encargado</h5>

                                        <div class="d-flex align-items-center gap-3 mb-3">
                                            <img id="previewPerfil"
                                                src="{{ $usuarios->avatar_url ?? asset('imagenes/perfil_default.png') }}"
                                                class="rounded-circle img-thumbnail"
                                                style="width:96px;height:96px;object-fit:cover;" alt="Foto encargado">
                                            <div class="flex-grow-1">
                                                <div class="small text-muted">Foto (opcional)</div>
                                                <input
                                                    class="form-control form-control-sm @error('usuarios.avatar') is-invalid @enderror"
                                                    type="file" id="fotoPerfil" name="usuarios[avatar]"
                                                    accept="image/*">
                                                @error('usuarios.avatar')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="row g-3">
                                            <div class="col-12 col-md-6">
                                                <label class="form-label">Nombre</label>
                                                <input type="text"
                                                    class="form-control @error('usuarios.nombre') is-invalid @enderror"
                                                    id="mgrNombre" name="usuarios[nombre]"
                                                    value="{{ old('usuarios.nombre', $usuarios->nombre) }}">
                                                @error('usuarios.nombre')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-12 col-md-6">
                                                <label class="form-label">Apellido</label>
                                                <input type="text"
                                                    class="form-control @error('usuarios.apellido') is-invalid @enderror"
                                                    id="mgrNombre" name="usuarios[apellido]"
                                                    value="{{ old('usuarios.apellido', $usuarios->apellido) }}">
                                                @error('usuarios.apellido')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-12">
                                                <label class="form-label">Correo</label>
                                                <input type="email" class="form-control" id="mgrEmail"
                                                    name="usuarios[correo]"
                                                    value="{{ old('usuarios.correo', $usuarios->correo) }}">
                                                @error('usuarios.correo')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <hr class="my-4">

                                        <h6 class="mb-2">Cambiar contraseña (opcional)</h6>
                                        <div class="row g-3">
                                            <div class="col-12">
                                                <label class="form-label">Contraseña actual</label>
                                                <input type="password"
                                                    class="form-control @error('usuarios.current_password') is-invalid @enderror"
                                                    name="usuarios[current_password]" autocomplete="current-password">
                                                @error('usuarios.current_password')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-12 col-md-6">
                                                <label class="form-label">Nueva contraseña</label>
                                                <input type="password" class="form-control" name="usuarios[password]"
                                                    autocomplete="new-password">
                                                @error('usuarios.password')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-12 col-md-6">
                                                <label class="form-label">Confirmación</label>
                                                <input type="password" class="form-control"
                                                    name="usuarios[password_confirmation]"
                                                    autocomplete="new-password">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- ===== Punto ECA ===== --}}
                            <div class="col-lg-6">
                                <div class="card card-hover h-100">
                                    <div class="card-body">
                                        <h5 class="mb-3">Punto ECA</h5>

                                        <div class="d-flex align-items-center gap-3 mb-3">
                                            <img id="previewPunto"
                                                src="{{ $punto->foto_url ?? asset('imagenes/eca-default.png') }}"
                                                class="img-thumbnail rounded"
                                                style="width:140px;height:100px;object-fit:cover;" alt="Foto punto">
                                            <div class="flex-grow-1">
                                                <div class="small text-muted">Foto (opcional)</div>
                                                <input class="form-control form-control-sm" type="file"
                                                    id="fotoPunto" name="punto[foto]" accept="image/*">
                                                @error('punto.foto')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror

                                                <div class="small text-muted mt-2">Logo (opcional)</div>
                                                <input class="form-control form-control-sm" type="file"
                                                    name="punto[logo]" accept="image/*">
                                                @error('punto.logo')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="row g-3">
                                            <div class="col-12">
                                                <label
                                                    class="form-label @error('punto.nombre') is-invalid @enderror">Nombre
                                                    del Punto</label>
                                                <input type="text" class="form-control" id="puntoNombre"
                                                    name="punto[nombre]"
                                                    value="{{ old('punto.nombre', $punto->nombre) }}">
                                                @error('punto.nombre')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label">Dirección</label>
                                                <input type="text" class="form-control" id="puntoDireccion"
                                                    name="punto[direccion]"
                                                    value="{{ old('punto.direccion', $punto->direccion) }}">
                                                @error('punto.direccion')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-12 col-md-6">
                                                <label class="form-label">Latitud</label>
                                                <div class="input-group">
                                                    <input type="text" inputmode="decimal"
                                                        pattern="^-?\\d{1,3}\\.\\d+"
                                                        class="form-control @error('punto.latitud') is-invalid @enderror"
                                                        name="punto[latitud]" id="puntoLatitud"
                                                        value="{{ old('punto.latitud', $punto->latitud) }}"
                                                        placeholder="Ej: 4.609710">
                                                    <button type="button" class="btn btn-outline-success"
                                                        id="btnGetUbicacion"
                                                        title="Obtener ubicación actual">GPS</button>
                                                </div>
                                                <div class="form-text">Usa el botón GPS o ingresa manualmente.</div>
                                                @error('punto.latitud')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-12 col-md-6">
                                                <label class="form-label">Longitud</label>
                                                <input type="text" inputmode="decimal"
                                                    pattern="^-?\\d{1,3}\\.\\d+"
                                                    class="form-control @error('punto.longitud') is-invalid @enderror"
                                                    name="punto[longitud]" id="puntoLongitud"
                                                    value="{{ old('punto.longitud', $punto->longitud) }}"
                                                    placeholder="Ej: -74.081749">
                                                @error('punto.longitud')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-12 col-md-6">
                                                <label class="form-label">Teléfono</label>
                                                <input type="tel"
                                                    class="form-control @error('punto.telefono') is-invalid @enderror"
                                                    id="punto[telefono]" name="punto[telefono]"
                                                    value="{{ old('punto.telefono', $punto->telefonoPunto) }}"
                                                    inputmode="numeric" maxlength="10" pattern="^\d{10}$"
                                                    placeholder="Ej: 3XXXXXXXXX">
                                                @error('punto.telefono')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-6">
                                                <label class="form-label">Ciudad</label>
                                                <input type="text" class="form-control" id="puntoCiudad"
                                                    name="punto[ciudad]"
                                                    value="{{ old('punto.ciudad', $punto->ciudad) }}">
                                                @error('punto.ciudad')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-6">
                                                <label class="form-label">Localidad</label>
                                                <input type="text" class="form-control" id="puntoLocalidad"
                                                    name="punto[localidad]"
                                                    value="{{ old('punto.localidad', $punto->localidad) }}">
                                                @error('punto.localidad')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-12">
                                                <label class="form-label">Horario</label>
                                                <input type="text" class="form-control" id="puntoHorario"
                                                    name="punto[horario_atencion]"
                                                    value="{{ old('punto.horario_atencion', $punto->horario_atencion) }}">
                                                @error('punto.horario_atencion')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-12">
                                                <button class="btn btn-outline-secondary" type="button"
                                                    id="btnUbicacion">Obtener ubicación</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="text-end mt-2">
                                    <button type="submit" class="btn btn-success">Guardar cambios</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </section>
            @endif

            @push('styles')
                <style>
                    /* Mejoras accesibilidad y legibilidad de tablas */
                    table.table thead.bg-success.text-white th {
                        white-space: nowrap;
                        font-weight: 600;
                    }

                    /* Dar un tono levemente más oscuro al verde de cabecera para mejor contraste (sin tocar Bootstrap base) */
                    table.table thead.bg-success.text-white {
                        background-color: #157347 !important;
                    }

                    /* Ajustar altura y densidad */
                    table.table.table-sm tbody td,
                    table.table.table-sm thead th {
                        padding: .45rem .6rem;
                    }

                    /* Tablas de centros: mejorar legibilidad y truncado */
                    .centros-table tbody tr:hover {
                        background: #f8fff9 !important;
                    }

                    .centros-table td,
                    .centros-table th {
                        vertical-align: middle;
                    }

                    .centros-table .badge {
                        font-weight: 500;
                    }
                </style>
            @endpush

            <!-- MATERIALES -->
            @if ($seccion === 'materiales')
                <section class="tab-pane fade show active" id="tab-materiales" role="tabpanel"
                    aria-labelledby="materiales-tab" data-url="{{ url('punto-eca/materiales') }}">
                    @include('components.flash-messages')

                    <div class="card mb-4">
                        <div
                            class="card-header bg-success text-white d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <strong>Ocupación de inventario</strong>
                            <span class="small text-light">Porcentaje de stock sobre capacidad</span>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-sm align-middle mb-0">
                                    <thead class="bg-success text-white">
                                        <tr>
                                            <th>Material</th>
                                            <th class="text-end" style="width:140px;">Stock</th>
                                            <th class="text-end" style="width:140px;">Capacidad</th>
                                            <th style="min-width:260px;">% Ocupación</th>
                                            <th class="text-end" style="width:130px;">Umbral alerta</th>
                                            <th class="text-end" style="width:140px;">Umbral crítico</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $tieneDatos = false; @endphp
                                        @foreach ($inventario ?? collect() as $inv)
                                            @php
                                                $stock = (float) ($inv->stock_actual ?? 0);
                                                $cap = (float) ($inv->capacidad_max ?? 0);
                                                $ua = is_null($inv->umbral_alerta) ? null : (float) $inv->umbral_alerta;
                                                $uc = is_null($inv->umbral_critico)
                                                    ? null
                                                    : (float) $inv->umbral_critico;
                                                $pct = $cap > 0 ? ($stock / max($cap, 0.000001)) * 100 : null; // evitar div/0
                                                $barClass = 'bg-secondary';
                                                if (!is_null($pct)) {
                                                    if ($cap > 0 && $stock >= $cap) {
                                                        $barClass = 'bg-danger';
                                                    } elseif (!is_null($uc) && $stock <= $uc) {
                                                        $barClass = 'bg-danger';
                                                    } elseif (!is_null($ua) && $stock <= $ua) {
                                                        $barClass = 'bg-warning';
                                                    } else {
                                                        $barClass = 'bg-success';
                                                    }
                                                }
                                                $tieneDatos = true;
                                            @endphp
                                            <tr>
                                                <td class="fw-semibold">{{ $inv->material->nombre ?? '—' }}</td>
                                                <td class="text-end">{{ number_format($stock, 2) }}
                                                    {{ $inv->unidad_medida }}</td>
                                                <td class="text-end">{{ $cap ? number_format($cap, 2) : '—' }}
                                                    {{ $inv->unidad_medida }}</td>
                                                <td style="min-width:260px;">
                                                    @if (!is_null($pct))
                                                        @php $pctShown = number_format($pct, 2); @endphp
                                                        <div class="d-flex align-items-center gap-2">
                                                            <div class="progress flex-grow-1" style="height:16px;">
                                                                <div class="progress-bar {{ $barClass }}"
                                                                    role="progressbar"
                                                                    style="width: {{ min($pct, 100) }}%;"
                                                                    aria-valuenow="{{ $pctShown }}"
                                                                    aria-valuemin="0" aria-valuemax="100">
                                                                    <span class="small">{{ $pctShown }}%</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @else
                                                        <span class="text-muted small">Sin capacidad</span>
                                                    @endif
                                                </td>
                                                <td class="text-end">
                                                    {{ !is_null($ua) ? number_format($ua, 2) : '—' }}</td>
                                                <td class="text-end">
                                                    {{ !is_null($uc) ? number_format($uc, 2) : '—' }}</td>
                                            </tr>
                                        @endforeach
                                        @if (!$tieneDatos)
                                            <tr>
                                                <td colspan="6" class="text-center text-muted">No hay
                                                    materiales en el inventario.</td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                            <div class="small text-muted mt-2">
                                Colores: <span class="badge bg-success">OK</span>
                                <span class="badge bg-warning">Bajo (&le; alerta)</span>
                                <span class="badge bg-danger">Crítico / Lleno</span>
                            </div>
                        </div>
                    </div>

                    <div class="card mb-4">
                        <div class="card-header bg-success text-white">
                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                <strong class="me-2"> por:</strong>
                                <a class="btn btn-sm btn-outline-light" href="#f_categoria">Categoría</a>
                                <a class="btn btn-sm btn-outline-light" href="#f_tipo">Tipo</a>
                                <a class="btn btn-sm btn-outline-light" href="#f_nombre">Nombre</a>
                            </div>
                        </div>



                        <div class="card-body">
                            {{-- formulario para los filtros --}}
                            <form id="form-filtros" action="{{ route('eca.materiales.index') }}" method="get">
                            </form>

                            {{-- Panel: Categoría --}}
                            <div class="border rounded p-3 mb-3" id="f_categoria">
                                <div class="row g-3 align-items-end">
                                    <div class="col-md-6">
                                        <label class="form-label">Categoría</label>
                                        <select class="form-select" name="categoria" form="form-filtros">
                                            <option value="">Todas</option>
                                            @foreach ($categorias as $c)
                                                <option value="{{ $c->id }}" @selected(request('categoria') === $c->id)>
                                                    {{ $c->nombre }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <button class="btn btn-success w-100" type="submit"
                                            form="form-filtros">Buscar</button>
                                    </div>
                                </div>
                            </div>

                            {{-- Panel: Tipo --}}
                            <div class="border rounded p-3 mb-3" id="f_tipo">
                                <div class="row g-3 align-items-end">
                                    <div class="col-md-6">
                                        <label class="form-label">Tipo</label>
                                        <select class="form-select" name="tipo" form="form-filtros">
                                            <option value="">Todos</option>
                                            @foreach ($tipos as $t)
                                                <option value="{{ $t->id }}" @selected(request('tipo') === $t->id)>
                                                    {{ $t->nombre }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <button class="btn btn-success w-100" type="submit"
                                            form="form-filtros">Buscar</button>
                                    </div>
                                </div>
                            </div>

                            {{-- Panel: Nombre --}}
                            <div class="border rounded p-3 mb-3" id="f_nombre">
                                <div class="row g-3 align-items-end">
                                    <div class="col-md-6">
                                        <label class="form-label">Nombre del material</label>
                                        <input class="form-control" type="text" name="nombre"
                                            value="{{ request('nombre') }}" placeholder="Ej. Botella PET"
                                            form="form-filtros">
                                        @error('nombre')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-3">
                                        <button class="btn btn-succes w-100" type="submit"
                                            form="form-filtros">Buscar</button>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                    <div>

                        {{-- TABLA DE OCUPACIÓN DEL INVENTARIO (movida desde Resumen) --}}


                        <!-- Tabla de materiales disponibles (selección y registro) -->
                        <div class="table-responsive">
                            <table class="table table-sm align-middle">
                                <thead class="bg-success text-white">
                                    <tr>
                                        <th style="min-width: 160px;">Material</th>
                                        <th>Categoría</th>
                                        <th>Tipo</th>
                                        <th title="Capacidad máxima admitida por el punto ECA">Cap. máx</th>
                                        <th title="Unidad de medida (kg, unidad, t, m3)">Unidad</th>
                                        <th>Stock actual</th>
                                        <th title="Umbral para alerta">Umbral alerta</th>
                                        <th title="Umbral crítico">Umbral crítico</th>
                                        <th title="Precio compra">P. compra</th>
                                        <th title="Precio venta">P. venta</th>
                                        <th title="Activo (1/0)">Activo</th>
                                        <th class="text-end" style="min-width: 120px;">Acción</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    @foreach ($materiales as $material)
                                        <form id="reg-{{ $material->id }}"
                                            action="{{ route('eca.inventario.store') }}" method="post"
                                            style="display:none;">
                                            @csrf
                                            <input type="hidden" name="material_id" value="{{ $material->id }}">
                                            @error('material_id')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </form>

                                        <tr>
                                            <td>
                                                <div class="text-muted small">{{ $material->nombre }}</div>
                                            </td>
                                            <td>{{ $material->categoria->nombre }}</td>
                                            <td>{{ $material->tipo->nombre }}</td>

                                            <td>
                                                <input class="form-control form-control-sm" type="number"
                                                    step="0.01" name="capacidad_max" placeholder="0.00"
                                                    form="reg-{{ $material->id }}">
                                                @error('capacidad_max')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </td>

                                            <td>
                                                <select class="form-select form-select-sm" name="unidad_medida"
                                                    form="reg-{{ $material->id }}">
                                                    <option value="">—</option>
                                                    <option value="kg">kg</option>
                                                    <option value="unidad">unidad</option>
                                                    <option value="t">t</option>
                                                    <option value="m3">m³</option>
                                                </select>
                                                @error('unidad_medida')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </td>

                                            <td><input class="form-control form-control-sm" type="number"
                                                    step="0.01" name="stock_actual" placeholder="0.00"
                                                    form="reg-{{ $material->id }}">
                                                @error('stock_actual')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            <td><input class="form-control form-control-sm" type="number"
                                                    step="0.01" name="umbral_alerta" placeholder="0.00"
                                                    form="reg-{{ $material->id }}">
                                                @error('umbral_alerta')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            <td><input class="form-control form-control-sm" type="number"
                                                    step="0.01" name="umbral_critico" placeholder="0.00"
                                                    form="reg-{{ $material->id }}">
                                                @error('umbral_critico')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            <td>
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text">$</span>
                                                    <input class="form-control" type="number" step="0.01"
                                                        name="precio_compra" placeholder="0.00" min="0"
                                                        form="reg-{{ $material->id }}">
                                                </div>
                                                @error('precio_compra')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            <td>
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text">$</span>
                                                    <input class="form-control" type="number" step="0.01"
                                                        name="precio_venta" placeholder="0.00" min="0"
                                                        form="reg-{{ $material->id }}">
                                                </div>
                                                @error('precio_venta')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            <td>
                                                <select class="form-select form-select-sm" name="activo"
                                                    form="reg-{{ $material->id }}">
                                                    <option value="1">1</option>
                                                    <option value="0">0</option>
                                                </select>
                                                @error('activo')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </td>

                                            <td class="text-end">
                                                <button type="submit" class="btn btn-success btn-sm"
                                                    form="reg-{{ $material->id }}">
                                                    Registrar
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>

                            @if (($materiales ?? null) && $materiales->total())
                                <div class="text-center mb-2 text-muted small">
                                    Mostrando del {{ $materiales->firstItem() }} al {{ $materiales->lastItem() }}
                                    de {{ $materiales->total() }} resultados
                                </div>
                                <div class="d-flex justify-content-center">
                                    {{ $materiales->onEachSide(1)->links('pagination::bootstrap-5') }}
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- CONSULTAR / EDITAR / ELIMINAR REGISTRADOS -->
                    <div class="card">
                        <div class="card-header bg-success text-white">
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                                <strong>Materiales registrados</strong>
                                <div class="d-flex gap-2">
                                    <a class="btn btn-sm btn-outline-light" href="#q_categoria">Categoría</a>
                                    <a class="btn btn-sm btn-outline-light" href="#q_tipo">Tipo</a>
                                    <a class="btn btn-sm btn-outline-light" href="#q_nombre">Nombre</a>
                                </div>
                            </div>
                        </div>

                        <div class="card-body">

                            {{-- Filtros  --}}
                            <form id="form-consulta" action="{{ route('eca.materiales.index') }}" method="get">
                            </form>

                            <div class="border rounded p-3 mb-3" id="q_categoria">
                                <div class="row g-3 align-items-end">
                                    <div class="col-md-6">
                                        <label class="form-label">Categoría</label>
                                        <select class="form-select" name="q_categoria" form="form-consulta">
                                            <option value="">Todas</option>
                                            @foreach ($categorias as $c)
                                                <option value="{{ $c->id }}" @selected(request('q_categoria') === $c->id)>
                                                    {{ $c->nombre }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <button class="btn btn-success w-100" type="submit"
                                            form="form-consulta">Buscar</button>
                                    </div>
                                </div>
                            </div>

                            <div class="border rounded p-3 mb-3" id="q_tipo">
                                <div class="row g-3 align-items-end">
                                    <div class="col-md-6">
                                        <label class="form-label">Tipo</label>
                                        <select class="form-select" name="q_tipo" form="form-consulta">
                                            <option value="">Todos</option>
                                            @foreach ($tipos as $t)
                                                <option value="{{ $t->id }}" @selected(request('q_tipo') === $t->id)>
                                                    {{ $t->nombre }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <button class="btn btn-success w-100" type="submit"
                                            form="form-consulta">Buscar</button>
                                    </div>
                                </div>
                            </div>

                            <div class="border rounded p-3 mb-3" id="q_nombre">
                                <div class="row g-3 align-items-end">
                                    <div class="col-md-6">
                                        <label class="form-label">Nombre del material</label>
                                        <input class="form-control" type="text" name="q_nombre"
                                            value="{{ request('q_nombre') }}" placeholder="Ej. Botella PET"
                                            form="form-consulta">
                                    </div>
                                    <div class="col-md-3">
                                        <button class="btn btn-success w-100" type="submit"
                                            form="form-consulta">Buscar</button>
                                    </div>
                                </div>
                            </div>

                            {{-- ===== Tabla editable ===== --}}
                            <div class="table-responsive">
                                <table class="table table-sm align-middle">
                                    <thead class="bg-success text-white">
                                        <tr>
                                            <th>Material</th>
                                            <th>Categoría</th>
                                            <th>Tipo</th>
                                            <th title="Capacidad máxima">Cap. máx</th>
                                            <th>Unidad</th>
                                            <th>Stock</th>
                                            <th>Umbral alerta</th>
                                            <th>Umbral crítico</th>
                                            <th>P. compra</th>
                                            <th>P. venta</th>
                                            <th>Activo</th>
                                            <th class="text-end" style="min-width:160px;">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($inventario as $inv)
                                            <form id="upd-{{ $inv->id }}"
                                                action="{{ route('eca.inventario.update', $inv->id) }}"
                                                method="post" style="display:none;">
                                                @csrf
                                                @method('PUT')
                                            </form>

                                            <form id="del-{{ $inv->id }}"
                                                action="{{ route('eca.inventario.destroy', $inv->id) }}"
                                                method="post" style="display:none;">
                                                @csrf
                                                @method('DELETE')
                                            </form>

                                            <tr>
                                                <td>
                                                    <div class="fw-semibold">{{ $inv->material->nombre ?? '—' }}
                                                    </div>
                                                </td>
                                                <td>{{ $inv->material->categoria->nombre ?? '—' }}</td>
                                                <td>{{ $inv->material->tipo->nombre ?? '—' }}</td>

                                                <td>
                                                    <input class="form-control form-control-sm" type="number"
                                                        step="0.01" name="capacidad_max"
                                                        value="{{ $inv->capacidad_max }}"
                                                        form="upd-{{ $inv->id }}">
                                                    @error('capacidad_max')
                                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                                    @enderror
                                                </td>

                                                <td>
                                                    <select class="form-select form-select-sm" name="unidad_medida"
                                                        form="upd-{{ $inv->id }}">
                                                        @foreach (['kg', 'unidad', 't', 'm3'] as $u)
                                                            <option value="{{ $u }}"
                                                                @selected($inv->unidad_medida === $u)>
                                                                {{ $u }}</option>
                                                        @endforeach
                                                    </select>
                                                    @error('unidad_medida')
                                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                                    @enderror
                                                </td>

                                                <td>
                                                    <input class="form-control form-control-sm" type="number"
                                                        step="0.01" name="stock_actual"
                                                        value="{{ $inv->stock_actual }}"
                                                        form="upd-{{ $inv->id }}">
                                                    @error('stock_actual')
                                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                                    @enderror
                                                </td>

                                                <td>
                                                    <input class="form-control form-control-sm" type="number"
                                                        step="0.01" name="umbral_alerta"
                                                        value="{{ $inv->umbral_alerta }}"
                                                        form="upd-{{ $inv->id }}">
                                                    @error('umbral_alerta')
                                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                                    @enderror
                                                </td>

                                                <td>
                                                    <input class="form-control form-control-sm" type="number"
                                                        step="0.01" name="umbral_critico"
                                                        value="{{ $inv->umbral_critico }}"
                                                        form="upd-{{ $inv->id }}">
                                                    @error('umbral_critico')
                                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                                    @enderror
                                                </td>

                                                <td>
                                                    <div class="input-group input-group-sm">
                                                        <span class="input-group-text">$</span>
                                                        <input class="form-control" type="number" step="0.01"
                                                            name="precio_compra" value="{{ $inv->precio_compra }}"
                                                            min="0" form="upd-{{ $inv->id }}">
                                                    </div>
                                                    @error('precio_compra')
                                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                                    @enderror
                                                </td>

                                                <td>
                                                    <div class="input-group input-group-sm">
                                                        <span class="input-group-text">$</span>
                                                        <input class="form-control" type="number" step="0.01"
                                                            name="precio_venta" value="{{ $inv->precio_venta }}"
                                                            min="0" form="upd-{{ $inv->id }}">
                                                    </div>
                                                    @error('precio_venta')
                                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                                    @enderror
                                                </td>

                                                <td>
                                                    <select class="form-select form-select-sm" name="activo"
                                                        form="upd-{{ $inv->id }}">
                                                        <option value="1" @selected($inv->activo)>1</option>
                                                        <option value="0" @selected(!$inv->activo)>0</option>
                                                    </select>
                                                    @error('activo')
                                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                                    @enderror
                                                </td />

                                                <td class="text-end">
                                                    <div class="btn-group">
                                                        <button type="submit" class="btn btn-outline-success btn-sm"
                                                            form="upd-{{ $inv->id }}">
                                                            Guardar
                                                        </button>
                                                        <button type="submit" class="btn btn-outline-danger btn-sm"
                                                            form="del-{{ $inv->id }}"
                                                            onclick="return confirm('¿Eliminar este material del inventario?')">
                                                            Eliminar
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="13" class="text-center text-muted">Aún no hay
                                                    materiales
                                                    registrados.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            {{-- Resumen y paginación --}}
                            @if (($inventario ?? null) && $inventario->total())
                                <div class="text-center mb-2 text-muted small">
                                    Mostrando del {{ $inventario->firstItem() }} al {{ $inventario->lastItem() }}
                                    de {{ $inventario->total() }} resultados
                                </div>
                                <div class="d-flex justify-content-center">
                                    {{ $inventario->onEachSide(1)->links('pagination::bootstrap-5') }}
                                </div>
                            @endif

                        </div>
                    </div>
                </section>
            @endif

            <!-- Entradas y Salidas de Material -->
            @if ($seccion === 'movimientos')
                <section class="tab-pane fade show active" id="tab-movimientos">
                    @include('components.flash-messages')
                    <div class="row gy-4">
                        <div class="row g-4">

                            <!--  ENTRADA  -->
                            <div class="col-12 col-lg-6">
                                <div class="card card-hover h-100">
                                    <div class="card-header bg-success text-white">
                                        <strong>Registrar ENTRADA (Compra)</strong>
                                    </div>
                                    <div class="card-body">
                                        @php
                                            $selectedCompraId = old('compra.inventario_id') ?? request('compra_inv');
                                            $selectedCompra = $selectedCompraId
                                                ? $inventario->firstWhere('id', $selectedCompraId) ?? null
                                                : null;
                                        @endphp

                                        {{-- Paso 1: Selección de inventario (GET) si aún no se eligió --}}
                                        @if (!$selectedCompra)
                                            <form action="{{ route('eca.index', ['seccion' => 'movimientos']) }}"
                                                method="get" class="vstack gap-3">
                                                <input type="hidden" name="seccion" value="movimientos">
                                                <label class="form-label">Inventario / Material</label>
                                                <select name="compra_inv" class="form-select" required>
                                                    <option value="" disabled selected>— Selecciona —</option>
                                                    @foreach ($inventario as $invSel)
                                                        @php
                                                            $cap = (float) ($invSel->capacidad_max ?? 0);
                                                            $stock = (float) ($invSel->stock_actual ?? 0);
                                                            $disp = max($cap - $stock, 0);
                                                        @endphp
                                                        <option value="{{ $invSel->id }}">
                                                            {{ $invSel->material->nombre }} (Stock:
                                                            {{ number_format($stock, 2) }} / Cap:
                                                            {{ number_format($cap, 2) }} | Disp:
                                                            {{ number_format($disp, 2) }}
                                                            {{ $invSel->unidad_medida ?? '' }})
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <div class="text-end">
                                                    <button class="btn btn-outline-success">Continuar</button>
                                                </div>
                                            </form>
                                        @else
                                            {{-- Paso 2: Formulario de registro (POST) con max dinámico --}}
                                            @php
                                                $cap = (float) ($selectedCompra->capacidad_max ?? 0);
                                                $stock = (float) ($selectedCompra->stock_actual ?? 0);
                                                $disp = max($cap - $stock, 0);
                                            @endphp
                                            <div class="small text-muted mb-2">
                                                Seleccionado: <strong>{{ $selectedCompra->material->nombre }}</strong>
                                                | Stock: {{ number_format($stock, 2) }} / Cap:
                                                {{ number_format($cap, 2) }}
                                                | Disponible: {{ number_format($disp, 2) }}
                                                {{ $selectedCompra->unidad_medida }}
                                            </div>
                                            <form action="{{ route('eca.movimientos.compra.store') }}"
                                                method="post" class="vstack gap-3">
                                                @csrf
                                                <input type="hidden" name="compra[inventario_id]"
                                                    value="{{ $selectedCompra->id }}">

                                                <div class="row g-3">
                                                    <div class="col-md-6">
                                                        <label class="form-label">Fecha</label>
                                                        <input type="date" name="compra[fecha]"
                                                            class="form-control" value="{{ old('compra.fecha') }}"
                                                            required>
                                                        @error('compra.fecha')
                                                            <div class="invalid-feedback d-block">{{ $message }}
                                                            </div>
                                                        @enderror
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label">Cantidad</label>
                                                        <input type="number" name="compra[cantidad]" step="0.01"
                                                            min="0.01"
                                                            max="{{ number_format($disp, 2, '.', '') }}"
                                                            class="form-control"
                                                            value="{{ old('compra.cantidad') }}"
                                                            {{ $disp <= 0 ? 'disabled' : '' }} required>
                                                        @error('compra.cantidad')
                                                            <div class="invalid-feedback d-block">{{ $message }}
                                                            </div>
                                                        @enderror
                                                        @if ($disp <= 0)
                                                            <div class="text-danger small mt-1">Sin capacidad
                                                                disponible.</div>
                                                        @endif
                                                    </div>
                                                </div>

                                                <label class="form-label">Observaciones (opcional)</label>
                                                <textarea name="compra[observaciones]" rows="2" class="form-control">{{ old('compra.observaciones') }}</textarea>

                                                <div class="d-flex justify-content-between align-items-center">
                                                    <a href="{{ route('eca.index', ['seccion' => 'movimientos']) }}"
                                                        class="btn btn-outline-secondary btn-sm">Cambiar material</a>
                                                    <button type="submit" class="btn btn-success"
                                                        {{ $disp <= 0 ? 'disabled' : '' }}>Guardar entrada</button>
                                                </div>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- SALIDA -->
                            <div class="col-12 col-lg-6">
                                <div class="card card-hover h-100">
                                    <div class="card-header bg-success text-white">
                                        <strong>Registrar SALIDA (Despacho)</strong>
                                    </div>
                                    <div class="card-body">
                                        @php
                                            $selectedVentaId = old('venta.inventario_id') ?? request('venta_inv');
                                            $selectedVenta = $selectedVentaId
                                                ? $inventario->firstWhere('id', $selectedVentaId) ?? null
                                                : null;
                                        @endphp

                                        {{-- Paso 1: Selección inventario para salida (GET) --}}
                                        @if (!$selectedVenta)
                                            <form action="{{ route('eca.index', ['seccion' => 'movimientos']) }}"
                                                method="get" class="vstack gap-3">
                                                <input type="hidden" name="seccion" value="movimientos">
                                                <label class="form-label">Inventario / Material</label>
                                                <select name="venta_inv" class="form-select" required>
                                                    <option value="" disabled selected>— Selecciona —</option>
                                                    @foreach ($inventario as $invSel)
                                                        @php
                                                            $stockV = (float) ($invSel->stock_actual ?? 0);
                                                        @endphp
                                                        <option value="{{ $invSel->id }}">
                                                            {{ $invSel->material->nombre }} (Stock:
                                                            {{ number_format($stockV, 2) }}
                                                            {{ $invSel->unidad_medida ?? '' }})
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <div class="text-end">
                                                    <button class="btn btn-outline-danger">Continuar</button>
                                                </div>
                                            </form>
                                        @else
                                            @php
                                                $stockV = (float) ($selectedVenta->stock_actual ?? 0);
                                            @endphp
                                            <div class="small text-muted mb-2">
                                                Seleccionado: <strong>{{ $selectedVenta->material->nombre }}</strong>
                                                | Stock disponible: {{ number_format($stockV, 2) }}
                                                {{ $selectedVenta->unidad_medida }}
                                            </div>
                                            <form action="{{ route('eca.movimientos.venta.store') }}" method="post"
                                                class="vstack gap-3">
                                                @csrf
                                                <input type="hidden" name="venta[inventario_id]"
                                                    value="{{ $selectedVenta->id }}">

                                                <div class="row g-3">
                                                    <div class="col-md-6">
                                                        <label class="form-label">Fecha</label>
                                                        <input type="date" name="venta[fecha]"
                                                            class="form-control" value="{{ old('venta.fecha') }}"
                                                            required>
                                                        @error('venta.fecha')
                                                            <div class="invalid-feedback d-block">{{ $message }}
                                                            </div>
                                                        @enderror
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label">Cantidad</label>
                                                        <input type="number" name="venta[cantidad]" step="0.01"
                                                            min="0.01"
                                                            max="{{ number_format($stockV, 2, '.', '') }}"
                                                            class="form-control" value="{{ old('venta.cantidad') }}"
                                                            {{ $stockV <= 0 ? 'disabled' : '' }} required>
                                                        @error('venta.cantidad')
                                                            <div class="invalid-feedback d-block">{{ $message }}
                                                            </div>
                                                        @enderror
                                                        @if ($stockV <= 0)
                                                            <div class="text-danger small mt-1">Sin stock disponible.
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>

                                                <div class="row g-3">
                                                    <div class="col-md-5">
                                                        <label class="form-label">Centro de acopio (Opcional)</label>
                                                        <select name="venta[centro_acopio_global]" id="centro_global"
                                                            class="form-select">
                                                            <option value="">— Selecciona —</option>
                                                            @foreach ($centrosGlobalesLista as $cag)
                                                                <option value="{{ $cag->id }}"
                                                                    @selected(old('venta.centro_acopio_global') == $cag->id)>{{ $cag->nombre }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-md-7">
                                                        <label class="form-label">Centro de acopios personales
                                                            (Opcional)</label>
                                                        <select name="venta[centro_acopio_propio]" id="centro_propio"
                                                            class="form-select">
                                                            <option value="">— Selecciona —</option>
                                                            @foreach ($centrosPropiosLista as $cag)
                                                                <option value="{{ $cag->id }}"
                                                                    @selected(old('venta.centro_acopio_propio') == $cag->id)>{{ $cag->nombre }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    {{-- Hidden para centro seleccionado --}}
                                                    <input type="hidden" name="venta[centro_acopio_id]"
                                                        id="centro_hidden"
                                                        value="{{ old('venta.centro_acopio_id') }}">
                                                </div>

                                                <label class="form-label">Observaciones (opcional)</label>
                                                <textarea name="venta[observaciones]" rows="2" class="form-control">{{ old('venta.observaciones') }}</textarea>

                                                <div class="d-flex justify-content-between align-items-center">
                                                    <a href="{{ route('eca.index', ['seccion' => 'movimientos']) }}"
                                                        class="btn btn-outline-secondary btn-sm">Cambiar material</a>
                                                    <button type="submit" class="btn btn-danger"
                                                        {{ $stockV <= 0 ? 'disabled' : '' }}>Guardar salida</button>
                                                </div>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </div>

                        </div>



                        <div class="col-12">
                            <div class="card">
                                <div
                                    class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                                    <strong>Últimos movimientos</strong>
                                    <span class="text-muted small">Compras y ventas recientes</span>
                                </div>

                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-sm align-middle table-hover mb-0">
                                            <thead class="bg-success text-white small">
                                                <tr>
                                                    <th class="text-nowrap" style="width:100px;">Fecha</th>
                                                    <th style="width:90px;">Tipo</th>
                                                    <th style="min-width:140px;">Material</th>
                                                    <th class="text-end" style="width:120px;">Cantidad</th>
                                                    <th style="width:70px;">Unidad</th>
                                                    <th class="text-end" style="width:110px;">Precio</th>
                                                    <th style="min-width:160px;">Observaciones</th>
                                                </tr>
                                            </thead>
                                            <tbody class="small">
                                                @forelse($ultimosMovimientos as $m)
                                                    @php
                                                        $isCompra = $m['tipo'] === 'compra';
                                                        $rowClass = $isCompra
                                                            ? 'table-success-subtle'
                                                            : 'table-danger-subtle';
                                                    @endphp
                                                    <tr class="{{ $rowClass }}">
                                                        <td class="text-nowrap">{{ $m['fecha'] }}</td>
                                                        <td>
                                                            <span
                                                                class="badge rounded-pill {{ $isCompra ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-danger-subtle text-danger border border-danger-subtle' }}">
                                                                {{ $isCompra ? 'Compra' : 'Venta' }}
                                                            </span>
                                                        </td>
                                                        <td class="fw-semibold">{{ $m['material'] }}</td>
                                                        <td class="text-end fw-semibold">
                                                            {{ number_format($m['cantidad'] ?? 0, 2) }}</td>
                                                        <td>{{ $m['unidad'] }}</td>
                                                        <td class="text-end">
                                                            @if (!is_null($m['precio_unit']))
                                                                ${{ number_format($m['precio_unit'], 2) }}
                                                            @else
                                                                —
                                                            @endif
                                                        </td>
                                                        <td class="text-truncate" style="max-width:260px;"
                                                            title="{{ $m['observ'] ?? '' }}">
                                                            {{ $m['observ'] ? Str::limit($m['observ'], 60) : '—' }}
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="7" class="text-center text-muted py-3">No hay
                                                            movimientos recientes.</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            @endif



            <!-- HISTORIAL GLOBAL -->
            @if ($seccion === 'historial')
                <section class="tab-pane fade show active" id="tab-historial">
                    @include('components.flash-messages')
                    <ul class="nav nav-tabs" role="tablist">
                        <li class="nav-item">
                            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#hist-compras"
                                type="button">
                                Compras (Entradas)
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#hist-salidas"
                                type="button">
                                Salidas (Centros)
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content border border-top-0 rounded-bottom p-3">
                        {{-- COMPRAS --}}
                        <div class="tab-pane fade show active" id="hist-compras">
                            <form class="row g-2 mb-2" method="get"
                                action="{{ route('eca.index', ['seccion' => 'historial']) }}">
                                <div class="col-md-3">
                                    <input name="hc_desde" type="date" class="form-control"
                                        value="{{ request('hc_desde') }}">
                                </div>
                                <div class="col-md-3">
                                    <input name="hc_hasta" type="date" class="form-control"
                                        value="{{ request('hc_hasta') }}">
                                </div>
                                <div class="col-md-3">
                                    <select name="hc_material" class="form-select">
                                        <option value="">Todos los materiales</option>
                                        @foreach ($materialesPunto ?? [] as $m)
                                            <option value="{{ $m->id }}" @selected(request('hc_material') === $m->id)>
                                                {{ $m->nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3 d-grid">
                                    <button class="btn btn-outline-success">Filtrar</button>
                                </div>
                            </form>

                            <div class="table-responsive">
                                <table class="table table-sm table-striped align-middle">
                                    <thead class="bg-success text-white">
                                        <tr>
                                            <th>Fecha</th>
                                            <th>Material</th>
                                            <th class="text-end">Cantidad</th>
                                            <th>Unidad</th>
                                            <th class="text-end">Precio/Unidad</th>
                                            <th class="text-end">Total</th>
                                            <th>Obs.</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse(($histCompras ?? []) as $c)
                                            <tr>
                                                <td>{{ \Illuminate\Support\Str::of(optional($c->fecha)->format('Y-m-d') ?? $c->fecha)->limit(10) }}
                                                </td>
                                                <td>{{ $c->inventario->material->nombre ?? '—' }}</td>
                                                <td class="text-end">{{ number_format($c->cantidad ?? 0, 2) }}</td>
                                                <td>{{ $c->inventario->unidad_medida ?? '' }}</td>
                                                <td class="text-end">
                                                    {{ is_numeric($c->inventario->precio_compra) ? '$' . number_format($c->inventario->precio_compra, 2) : '—' }}
                                                </td>
                                                <td class="text-end">
                                                    ${{ number_format($c->precio_compra, 2) }}
                                                </td>
                                                <td class="text-truncate" style="max-width: 280px;">
                                                    {{ $c->observaciones ?? '—' }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center text-muted">Sin resultados.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            @if (($histCompras ?? null) instanceof \Illuminate\Contracts\Pagination\Paginator)
                                <div class="d-flex justify-content-center">
                                    {{ $histCompras->onEachSide(1)->links('pagination::bootstrap-5') }}
                                </div>
                            @endif
                        </div>

                        {{-- VENTAS --}}
                        <div class="tab-pane fade" id="hist-salidas">
                            <form class="row g-2 mb-2" method="get"
                                action="{{ route('eca.index', ['seccion' => 'historial']) }}">
                                <div class="col-md-3">
                                    <input name="hs_desde" type="date" class="form-control"
                                        value="{{ request('hs_desde') }}">
                                </div>
                                <div class="col-md-3">
                                    <input name="hs_hasta" type="date" class="form-control"
                                        value="{{ request('hs_hasta') }}">
                                </div>
                                <div class="col-md-3">
                                    <select name="hs_material" class="form-select">
                                        <option value="">Todos los materiales</option>
                                        @foreach ($materialesPunto ?? [] as $m)
                                            <option value="{{ $m->id }}" @selected(request('hs_material') === $m->id)>
                                                {{ $m->nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3 d-grid">
                                    <button class="btn btn-outline-success">Filtrar</button>
                                </div>
                            </form>

                            <div class="table-responsive">
                                <table class="table table-sm table-striped align-middle">
                                    <thead class="bg-success text-white">
                                        <tr>
                                            <th>Fecha</th>
                                            <th>Material</th>
                                            <th class="text-end">Cantidad</th>
                                            <th>Unidad</th>
                                            <th class="text-end">Precio/Unidad</th>
                                            <th class="text-end">Total</th>
                                            <th>Obs.</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse(($histVentas ?? []) as $v)
                                            <tr>
                                                <td>{{ \Illuminate\Support\Str::of(optional($v->fecha)->format('Y-m-d') ?? $v->fecha)->limit(10) }}
                                                </td>
                                                <td>{{ $v->inventario->material->nombre ?? '—' }}</td>
                                                <td class="text-end">{{ number_format($v->cantidad ?? 0, 2) }}</td>
                                                <td>{{ $v->inventario->unidad_medida ?? '' }}</td>
                                                <td class="text-end">
                                                    {{ is_numeric($v->precio_venta) ? '$' . number_format($v->precio_venta, 2) : '—' }}
                                                </td>
                                                <td class="text-end">
                                                    @php
                                                        $total =
                                                            (float) ($v->cantidad ?? 0) *
                                                            (float) ($v->precio_venta ?? 0);
                                                    @endphp
                                                    {{ number_format($total, 2) }}
                                                </td>
                                                <td class="text-truncate" style="max-width: 280px;">
                                                    {{ $v->observaciones ?? '—' }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center text-muted">Sin resultados.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            @if (($histVentas ?? null) instanceof \Illuminate\Contracts\Pagination\Paginator)
                                <div class="d-flex justify-content-center">
                                    {{ $histVentas->onEachSide(1)->links('pagination::bootstrap-5') }}
                                </div>
                            @endif
                        </div>
                    </div>
                </section>
            @endif

            @if ($seccion === 'calendario')
                <section class="tab-pane fade show active" id="tab-calendario">
                    @include('components.flash-messages')
                    @php
                        // tomar la fecha seleccionada desde la url
                        $sel = request('sel');
                    @endphp

                    <div class="row g-3">
                        <div class="col-lg-8">
                            <div class="card">
                                <div class="card-body">
                                    <div class="calendar">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <div class="h5 mb-0" id="caltitulo">{{ $mesTitulo ?? '—' }}</div>
                                            <div class="d-flex gap-2">
                                                <a class="btn btn-sm btn-outline-secondary" id="calprev"
                                                    href="{{ $navPrevUrl ?? '#' }}">◀</a>
                                                <a class="btn btn-sm btn-outline-secondary" id="calnext"
                                                    href="{{ $navNextUrl ?? '#' }}">▶</a>
                                            </div>
                                        </div>

                                        <div class="grid text-center small text-muted mb-1">
                                            <div>lun</div>
                                            <div>mar</div>
                                            <div>mié</div>
                                            <div>jue</div>
                                            <div>vie</div>
                                            <div>sáb</div>
                                            <div>dom</div>
                                        </div>

                                        {{-- rejilla para el calendario --}}
                                        <div class="grid" id="calgrid">
                                            @foreach ($dias ?? [] as $d)
                                                @php
                                                    $dYmd = $d['date']->toDateString();
                                                    $dayUrl = request()->fullUrlWithQuery([
                                                        'seccion' => 'calendario',
                                                        'sel' => $dYmd,
                                                    ]);
                                                @endphp
                                                <a href="{{ $dayUrl }}" class="text-decoration-none">
                                                    <div class="p-2 border {{ $d['inMonth'] ? 'bg-white' : 'bg-light' }}"
                                                        title="{{ $dYmd }}">
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <strong
                                                                class="{{ ($sel ?? '') === $dYmd ? 'text-success' : '' }}">{{ $d['date']->day }}</strong>
                                                        </div>
                                                        <div class="mt-1 d-flex flex-column gap-1">
                                                            {{-- Badges del día: "HH:MM · Material" --}}
                                                            @foreach ($d['events'] as $ev)
                                                                @php $h = isset($ev['time']) ? \Illuminate\Support\Str::of($ev['time'])->limit(5,'')->__toString() : ''; @endphp
                                                                <span
                                                                    class="badge bg-success text-wrap">{{ $h }}
                                                                    · {{ $ev['material'] ?? 'Material' }}</span>
                                                            @endforeach
                                                            @if (empty($d['events']))
                                                                <span class="small text-muted">—</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </a>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="small text-muted mt-2">
                                <span class="badge bg-success">salida</span> despacho programado al centro elegido.
                            </div>
                        </div>

                        <div class="col-lg-4">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h6 class="mb-3">programar nuevo despacho</h6>

                                    <form action="{{ route('eca.calendario.store') }}" method="post"
                                        class="vstack gap-3">
                                        @csrf

                                        <div>
                                            <label class="form-label">material</label>
                                            <select name="material_id" class="form-select" required>
                                                <option value="" disabled selected>— selecciona —</option>
                                                @foreach ($inventario ?? [] as $inv)
                                                    <option value="{{ $inv->material_id }}"
                                                        @selected(old('material_id') === $inv->material_id)>
                                                        {{ $inv->material->nombre ?? '—' }} (stock:
                                                        {{ $inv->stock_actual ?? 0 }}
                                                        {{ $inv->unidad_medida ?? '' }})
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('material_id')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div>
                                            <label class="form-label">centro de acopio</label>
                                            <select name="centro_acopio_id" class="form-select" required>
                                                <option value="" disabled selected>— selecciona —</option>
                                                <optgroup label="propios del punto">
                                                    @foreach ($centrospropioslista ?? [] as $c)
                                                        <option value="{{ $c->id }}"
                                                            @selected(old('centro_acopio_id') === $c->id)>{{ $c->nombre }}</option>
                                                    @endforeach
                                                </optgroup>
                                                <optgroup label="globales">
                                                    @foreach ($centrosglobaleslista ?? [] as $c)
                                                        <option value="{{ $c->id }}"
                                                            @selected(old('centro_acopio_id') === $c->id)>{{ $c->nombre }}</option>
                                                    @endforeach
                                                </optgroup>
                                            </select>
                                            @error('centro_acopio_id')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div>
                                            <label class="form-label">frecuencia</label>
                                            <select name="frecuencia" class="form-select" required>
                                                @php
                                                    $freqs = [
                                                        'manual' => 'manual (solo esta fecha)',
                                                        'semanal' => 'semanal',
                                                        'quincenal' => 'cada 15 días',
                                                        'mensual' => 'mensual',
                                                        'unico' => 'única vez',
                                                    ];
                                                @endphp
                                                @foreach ($freqs as $v => $t)
                                                    <option value="{{ $v }}"
                                                        @selected(old('frecuencia') === $v)>
                                                        {{ $t }}</option>
                                                @endforeach
                                            </select>
                                            @error('frecuencia')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="row g-3">
                                            <div class="col-6">
                                                <label class="form-label">fecha</label>
                                                <input type="date" name="fecha" class="form-control"
                                                    value="{{ old('fecha') ?? now()->toDateString() }}" required>
                                            </div>
                                            <div class="col-6">
                                                <label class="form-label">hora</label>
                                                <input type="time" name="hora" class="form-control"
                                                    value="{{ old('hora') ?? '10:00' }}" required>
                                            </div>
                                        </div>

                                        <div>
                                            <label class="form-label">notas (opcional)</label>
                                            <textarea name="notas" class="form-control" rows="2" maxlength="300">{{ old('notas') }}</textarea>
                                        </div>

                                        <button type="submit" class="btn btn-success w-100">guardar</button>

                                        <hr>
                                        <div class="small text-muted">
                                            mostrando: <span id="lblrango">{{ $rangoLabel ?? '—' }}</span>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-3">
                        <div class="row g-3">
                            <div class="col-lg-8">

                                {{-- EVENTOS DEL DÍA --}}
                                <div class="card">
                                    <div class="card-body">
                                        @php
                                            $selParam = request('sel');
                                            $selDate = $selParam ? \Carbon\Carbon::parse($selParam) : null;
                                            $selKey = $selDate ? $selDate->toDateString() : null;
                                            $eventosDia = [];
                                            if ($selKey) {
                                                foreach ($dias ?? [] as $d) {
                                                    if ($d['date']->toDateString() === $selKey) {
                                                        $eventosDia = $d['events'] ?? [];
                                                        break;
                                                    }
                                                }
                                            }
                                        @endphp

                                        <h6 class="mb-1">eventos del día <span id="lbldiasel"
                                                class="text-muted">{{ $selKey ?? '—' }}</span></h6>

                                        <div id="listdia" class="vstack gap-2 mt-2">
                                            @if (empty($selKey))
                                                <div class="text-muted">selecciona un día en la grilla.</div>
                                            @else
                                                @forelse ($eventosDia as $ev)
                                                    @php $hhmm = isset($ev['hora']) ? \Illuminate\Support\Str::of($ev['hora'])->limit(5,'')->__toString() : ''; @endphp
                                                    <div class="border rounded p-2">
                                                        <div class="d-flex flex-wrap gap-2 align-items-center mb-1">
                                                            <span class="badge bg-success">{{ $hhmm }}</span>
                                                            <span
                                                                class="fw-semibold">{{ $ev['material'] ?? 'material' }}</span>
                                                            <span class="text-muted">·
                                                                {{ $ev['centro'] ?? 'centro de acopio' }}</span>
                                                            <span class="text-muted">·
                                                                {{ $ev['frecuencia'] ?? '—' }}</span>
                                                            @if (isset($ev['id']))
                                                                <form method="post"
                                                                    action="{{ route('eca.calendario.destroy', $ev['id']) }}"
                                                                    class="ms-auto d-inline"
                                                                    onsubmit="return confirm('¿Eliminar este evento?');">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <input type="hidden" name="sel"
                                                                        value="{{ $selKey }}">
                                                                    <button class="btn btn-sm btn-outline-danger"
                                                                        title="Eliminar">
                                                                        &times;
                                                                    </button>
                                                                </form>
                                                            @endif
                                                        </div>

                                                        {{-- DETALLE --}}
                                                        <div class="table-responsive">
                                                            <table class="table table-sm table-bordered mb-0">
                                                                <tbody class="small">
                                                                    <tr>
                                                                        <th class="w-25">Punto</th>
                                                                        <td>{{ $ev['punto'] ?? '—' }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th>Material</th>
                                                                        <td>{{ $ev['material'] ?? '—' }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th>Centro</th>
                                                                        <td>{{ $ev['centro'] ?? '—' }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th>Frecuencia</th>
                                                                        <td>{{ $ev['frecuencia'] ?? '—' }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th>Fecha</th>
                                                                        <td>{{ $ev['fecha'] ?? $selKey }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th>Hora</th>
                                                                        <td>{{ $ev['hora'] ?? '—' }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th>Notas</th>
                                                                        <td>{{ $ev['notas'] ?? '—' }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th>Creado</th>
                                                                        <td>{{ $ev['creado'] ?? '—' }}</td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                @empty
                                                    <div class="text-muted">sin eventos para este día.</div>
                                                @endforelse
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                {{-- EVENTOS DEL MES --}}
                                <div class="card mt-3">
                                    <div class="card-body">
                                        <h6 class="mb-3">eventos del mes</h6>

                                        <div id="listmes" class="vstack gap-2">
                                            @php
                                                $byDate = [];
                                                foreach ($dias ?? [] as $d) {
                                                    $dkey = $d['date']->toDateString();
                                                    if (!empty($d['events'])) {
                                                        $byDate[$dkey] = $d['events'];
                                                    }
                                                }
                                                ksort($byDate);
                                            @endphp

                                            @forelse ($byDate as $dkey => $events)
                                                <div class="border rounded p-2">
                                                    <div class="fw-semibold mb-2">{{ $dkey }}</div>

                                                    @foreach ($events as $ev)
                                                        @php $hhmm = isset($ev['hora']) ? \Illuminate\Support\Str::of($ev['hora'])->limit(5,'')->__toString() : ''; @endphp

                                                        <div class="border rounded p-2 mb-2">
                                                            <div
                                                                class="d-flex flex-wrap gap-2 align-items-center mb-1">
                                                                <span
                                                                    class="badge bg-success">{{ $hhmm }}</span>
                                                                <span><strong>{{ $ev['material'] ?? 'material' }}</strong></span>
                                                                <span class="text-muted">·
                                                                    {{ $ev['centro'] ?? 'centro' }}</span>
                                                                <span class="text-muted">·
                                                                    {{ $ev['frecuencia'] ?? '—' }}</span>
                                                                @if (isset($ev['id']))
                                                                    <form method="post"
                                                                        action="{{ route('eca.calendario.destroy', $ev['id']) }}"
                                                                        class="ms-auto d-inline"
                                                                        onsubmit="return confirm('¿Eliminar este evento?');">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <input type="hidden" name="sel"
                                                                            value="{{ $dkey }}">
                                                                        <button class="btn btn-sm btn-outline-danger"
                                                                            title="Eliminar">&times;</button>
                                                                    </form>
                                                                @endif
                                                            </div>

                                                            <div class="table-responsive">
                                                                <table class="table table-sm table-bordered mb-0">
                                                                    <tbody class="small">
                                                                        <tr>
                                                                            <th class="w-25">Punto</th>
                                                                            <td>{{ $ev['punto'] ?? '—' }}</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th>Material</th>
                                                                            <td>{{ $ev['material'] ?? '—' }}</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th>Centro</th>
                                                                            <td>{{ $ev['centro'] ?? '—' }}</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th>Frecuencia</th>
                                                                            <td>{{ $ev['frecuencia'] ?? '—' }}</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th>Fecha</th>
                                                                            <td>{{ $ev['fecha'] ?? $dkey }}</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th>Hora</th>
                                                                            <td>{{ $ev['hora'] ?? '—' }}</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th>Notas</th>
                                                                            <td>{{ $ev['notas'] ?? '—' }}</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th>Creado</th>
                                                                            <td>{{ $ev['creado'] ?? '—' }}</td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @empty
                                                <div class="text-muted">sin eventos en este rango.</div>
                                            @endforelse
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                </section>
            @endif





            @if ($seccion === 'centros')
                <section class="tab-pane fade show active" id="tab-centros">
                    @include('components.flash-messages')
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">Centros de acopio</h5>
                    </div>

                    {{-- FILTROS --}}
                    <div class="card mb-4">
                        <div
                            class="card-header bg-success text-white py-2 d-flex align-items-center justify-content-between flex-wrap gap-2">
                            <strong class="small text-uppercase">Filtros</strong>
                            <a href="{{ route('eca.index', ['seccion' => 'centros']) }}"
                                class="btn btn-sm btn-outline-light">Reset</a>
                        </div>
                        <div class="card-body py-3">
                            <form class="row g-3 align-items-end" method="get"
                                action="{{ route('eca.index', ['seccion' => 'centros']) }}">
                                <div class="col-12 col-md-3">
                                    <label class="form-label small mb-1">Nombre</label>
                                    <input type="text" name="f_nombre" class="form-control form-control-sm"
                                        placeholder="Ej. Planta" value="{{ request('f_nombre') }}">
                                </div>
                                <div class="col-6 col-md-2">
                                    <label class="form-label small mb-1">Tipo</label>
                                    <select name="f_tipo" class="form-select form-select-sm">
                                        <option value="">Todos</option>
                                        @foreach (['Planta', 'Proveedor', 'Otro'] as $t)
                                            <option value="{{ $t }}" @selected(request('f_tipo') === $t)>
                                                {{ $t }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-6 col-md-2">
                                    <label class="form-label small mb-1">Estado</label>
                                    <select name="f_estado" class="form-select form-select-sm">
                                        <option value="">Todos</option>
                                        @foreach (['activo', 'inactivo', 'bloqueado'] as $e)
                                            <option value="{{ $e }}" @selected(request('f_estado') === $e)>
                                                {{ ucfirst($e) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-6 col-md-2">
                                    <label class="form-label small mb-1">Localidad</label>
                                    <input type="text" name="f_localidad" class="form-control form-control-sm"
                                        placeholder="Ciudad" value="{{ request('f_localidad') }}">
                                </div>
                                <div class="col-12 col-md-3">
                                    <label
                                        class="form-label small mb-1 d-flex justify-content-between"><span>Materiales</span><span
                                            class="text-muted">(multi)</span></label>
                                    <select name="f_materiales[]" class="form-select form-select-sm" multiple
                                        size="4">
                                        @foreach ($materialesPunto ?? [] as $m)
                                            <option value="{{ $m->id }}" @selected(collect(request('f_materiales'))->contains($m->id))>
                                                {{ $m->nombre }}</option>
                                        @endforeach
                                    </select>
                                    <div class="form-text small">Mantén Ctrl para múltiples</div>
                                </div>
                                <div class="col-12 col-md-auto ms-auto">
                                    <button class="btn btn-success btn-sm px-4"><strong>Buscar</strong></button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="row g-4">
                        <div class="col-12 col-lg-9">
                            {{-- Centros globales --}}
                            <div class="card card-hover mb-4">
                                <div
                                    class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                                    <strong>Centros globales</strong>
                                    <span class="small text-muted">Catálogo general</span>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table
                                            class="table table-sm table-striped align-middle table-hover centros-table">
                                            <thead class="bg-success text-white small">
                                                <tr>
                                                    <th class="text-nowrap">Nombre</th>
                                                    <th class="text-nowrap">Tipo</th>
                                                    <th class="text-nowrap">Ciudad</th>
                                                    <th style="min-width:220px;">Materiales que recicla</th>
                                                    <th class="text-nowrap">Contacto</th>
                                                    <th class="text-nowrap">Teléfono</th>
                                                    <th style="min-width:180px;">Correo</th>
                                                    <th class="text-nowrap">Estado</th>
                                                </tr>
                                            </thead>
                                            <tbody class="small">
                                                @forelse(($centrosGlobales ?? []) as $c)
                                                    @php
                                                        $nombresMat = ($c->materiales ?? collect())
                                                            ->pluck('nombre')
                                                            ->all();
                                                        $estado = strtolower($c->estado ?? '');
                                                        $badgeClass = match ($estado) {
                                                            'activo' => 'bg-success',
                                                            'inactivo' => 'bg-secondary',
                                                            'bloqueado' => 'bg-danger',
                                                            default => 'bg-light text-dark',
                                                        };
                                                        $tel = $c->telefono ?? null;
                                                        if ($tel) {
                                                            $digits = preg_replace('/\D+/', '', $tel);
                                                            if (strlen($digits) >= 7) {
                                                                $tel =
                                                                    substr($digits, 0, 3) .
                                                                    '-' .
                                                                    substr($digits, 3, 3) .
                                                                    '-' .
                                                                    substr($digits, 6);
                                                            }
                                                        }
                                                    @endphp
                                                    <tr>
                                                        <td class="fw-semibold" title="{{ $c->nombre }}">
                                                            {{ \Illuminate\Support\Str::limit($c->nombre, 28) }}</td>
                                                        <td>{{ $c->tipo }}</td>
                                                        <td>{{ $c->ciudad ?? '—' }}</td>
                                                        <td>
                                                            @if ($nombresMat)
                                                                <span class="d-inline-block text-truncate"
                                                                    style="max-width:240px;"
                                                                    title="{{ implode(', ', $nombresMat) }}">
                                                                    {{ implode(', ', array_slice($nombresMat, 0, 6)) }}
                                                                    @if (count($nombresMat) > 6)
                                                                        …
                                                                    @endif
                                                                </span>
                                                            @else
                                                                <span class="text-muted">—</span>
                                                            @endif
                                                        </td>
                                                        <td title="{{ $c->contacto ?? '—' }}">
                                                            {{ \Illuminate\Support\Str::limit($c->contacto ?? '—', 22) }}
                                                        </td>
                                                        <td>{{ $tel ?? '—' }}</td>
                                                        <td>
                                                            @if ($c->correo)
                                                                <span class="d-inline-block text-truncate"
                                                                    style="max-width:170px;"
                                                                    title="{{ $c->correo }}">{{ $c->correo }}</span>
                                                            @else
                                                                <span class="text-muted">—</span>
                                                            @endif
                                                        </td>
                                                        <td><span
                                                                class="badge {{ $badgeClass }}">{{ ucfirst($estado ?: '—') }}</span>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="8" class="text-center text-muted">Sin
                                                            resultados.</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>

                                    @if (($centrosGlobales ?? null) instanceof \Illuminate\Contracts\Pagination\Paginator)
                                        <div class="d-flex justify-content-center">
                                            {{ $centrosGlobales->onEachSide(1)->links('pagination::bootstrap-5') }}
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="card card-hover">
                                <div
                                    class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                                    <strong>Centros del Punto</strong>
                                    <span class="small text-muted">Propios del Punto ECA</span>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table
                                            class="table table-sm table-striped align-middle table-hover centros-table">
                                            <thead class="bg-success text-white small">
                                                <tr>
                                                    <th class="text-nowrap">Nombre</th>
                                                    <th class="text-nowrap">Tipo</th>
                                                    <th class="text-nowrap">Localidad</th>
                                                    <th class="text-nowrap">Teléfono</th>
                                                    <th class="text-nowrap">Estado</th>
                                                    <th class="text-nowrap">Notas</th>
                                                    <th class="text-end text-nowrap">Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody class="small">
                                                @forelse(($centrosPropios ?? []) as $c)
                                                    <tr>
                                                        <td>
                                                            <form id="upd-centro-{{ $c->id }}"
                                                                action="{{ route('eca.centros.update', $c->id) }}"
                                                                method="post" class="row g-1 align-items-center">
                                                                @csrf
                                                                @method('PUT')
                                                                <input type="text" name="cac[nombre]"
                                                                    class="form-control form-control-sm"
                                                                    maxlength="150" value="{{ $c->nombre }}"
                                                                    required>
                                                                <input type="hidden" name="cac[descripcion]"
                                                                    value="{{ $c->descripcion }}">
                                                        </td>
                                                        <td>
                                                            <select name="cac[tipo]"
                                                                class="form-select form-select-sm"
                                                                form="upd-centro-{{ $c->id }}" required>
                                                                @foreach (['Planta', 'Proveedor', 'Otro'] as $t)
                                                                    <option value="{{ $t }}"
                                                                        @selected($c->tipo === $t)>
                                                                        {{ $t }}</option>
                                                                @endforeach
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <input type="text" name="cac[localidad]"
                                                                class="form-control form-control-sm" maxlength="60"
                                                                value="{{ $c->localidad }}"
                                                                form="upd-centro-{{ $c->id }}">
                                                        </td>
                                                        <td>
                                                            <input type="text" name="cac[telefono]"
                                                                class="form-control form-control-sm" maxlength="20"
                                                                value="{{ $c->telefono }}"
                                                                form="upd-centro-{{ $c->id }}">
                                                        </td>
                                                        <td class="text-center">
                                                            <div class="form-check form-switch d-inline-block">
                                                                <input type="hidden" name="cac[estado]"
                                                                    value="inactivo"
                                                                    form="upd-centro-{{ $c->id }}">
                                                                <input class="form-check-input" type="checkbox"
                                                                    role="switch" name="cac[estado]"
                                                                    value="activo"
                                                                    form="upd-centro-{{ $c->id }}"
                                                                    @checked($c->estado === 'activo')>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <input type="text" name="cac[notas]"
                                                                class="form-control form-control-sm" maxlength="300"
                                                                value="{{ $c->notas }}"
                                                                form="upd-centro-{{ $c->id }}"
                                                                placeholder="Notas...">
                                                        </td>
                                                        <td class="text-end">
                                                            <div class="btn-group btn-group-sm" role="group">
                                                                <button type="submit" class="btn btn-success"
                                                                    form="upd-centro-{{ $c->id }}">Guardar</button>
                                                                </form>
                                                                <form
                                                                    action="{{ route('eca.centros.destroy', $c->id) }}"
                                                                    method="post"
                                                                    onsubmit="return confirm('¿Eliminar centro?');">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button
                                                                        class="btn btn-outline-danger">Eliminar</button>
                                                                </form>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="7" class="text-center text-muted">Aún no has
                                                            creado centros.</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>

                                    @if (($centrosPropios ?? null) instanceof \Illuminate\Contracts\Pagination\Paginator)
                                        <div class="d-flex justify-content-center">
                                            {{ $centrosPropios->onEachSide(1)->links('pagination::bootstrap-5') }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- FORMULARIO NUEVO CENTRO --}}
                        <div class="col-12 col-lg-3 col-xl-3">
                            <div class="card card-hover h-100">
                                <div class="card-header bg-success text-white">
                                    <strong>Nuevo centro del punto</strong>
                                </div>
                                <div class="card-body p-3">
                                    <form action="{{ route('eca.centros.store') }}" method="post"
                                        class="vstack gap-2 small">
                                        @csrf

                                        <div>
                                            <label class="form-label">Nombre</label>
                                            <input type="text" name="cac[nombre]" class="form-control"
                                                value="{{ old('cac.nombre') }}" required>
                                            @error('cac.nombre')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="row g-2">
                                            <div class="col-6">
                                                <label class="form-label mb-1">Tipo</label>
                                                <select name="cac[tipo]" class="form-select form-select-sm"
                                                    required>
                                                    @foreach (['Planta', 'Proveedor', 'Otro'] as $t)
                                                        <option value="{{ $t }}"
                                                            @selected(old('cac.tipo') === $t)>
                                                            {{ $t }}</option>
                                                    @endforeach
                                                </select>
                                                @error('cac.tipo')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-6">
                                                <label class="form-label mb-1">Materiales</label>
                                                <select name="cac[materiales][]" class="form-select form-select-sm"
                                                    multiple size="5">
                                                    @foreach ($materialesPunto ?? [] as $m)
                                                        <option value="{{ $m->id }}"
                                                            @selected(collect(old('cac.materiales', []))->contains($m->id))>
                                                            {{ $m->nombre }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <div class="form-text">Multi-selección.</div>
                                                @error('cac.materiales')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                                @error('cac.materiales.*')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div>
                                            <label class="form-label">Descripción</label>
                                            <input type="text" name="cac[descripcion]" class="form-control"
                                                value="{{ old('cac.descripcion') }}">
                                            @error('cac.descripcion')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div>
                                            <label class="form-label mb-1">Teléfono</label>
                                            <input type="text" name="cac[telefono]"
                                                class="form-control form-control-sm"
                                                value="{{ old('cac.telefono') }}">
                                            @error('cac.telefono')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="row g-2">
                                            <div class="col-6">
                                                <label class="form-label mb-1">Correo</label>
                                                <input type="email" name="cac[correo]"
                                                    class="form-control form-control-sm"
                                                    value="{{ old('cac.correo') }}">
                                                @error('cac.correo')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-6">
                                                <label class="form-label mb-1">Sitio web</label>
                                                <input type="url" name="cac[sitio_web]"
                                                    class="form-control form-control-sm"
                                                    value="{{ old('cac.sitio_web') }}">
                                                @error('cac.sitio_web')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div>
                                            <label class="form-label">Horario de atención</label>
                                            <input type="text" name="cac[horario_atencion]"
                                                class="form-control" value="{{ old('cac.horario_atencion') }}">
                                            @error('cac.horario_atencion')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div>
                                            <label class="form-label mb-1">Localidad</label>
                                            <input type="text" name="cac[localidad]"
                                                class="form-control form-control-sm"
                                                value="{{ old('cac.localidad') }}">
                                            @error('cac.localidad')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div>
                                            <label class="form-label">Dirección</label>
                                            <input type="text" name="cac[direccion]" class="form-control"
                                                value="{{ old('cac.direccion') }}">
                                            @error('cac.direccion')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div>
                                            <label class="form-label mb-1">Notas</label>
                                            <input type="text" name="cac[notas]"
                                                class="form-control form-control-sm"
                                                value="{{ old('cac.notas') }}" maxlength="300">
                                            @error('cac.notas')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center mt-1">
                                            <div class="form-check form-switch small">
                                                <input type="hidden" name="cac[estado]" value="inactivo">
                                                <input class="form-check-input" type="checkbox" name="cac[estado]"
                                                    value="activo" checked>
                                                <label class="form-check-label">Activo</label>
                                            </div>
                                            <button class="btn btn-success btn-sm">Guardar</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                    </div>
                </section>
            @endif



            <!-- CONVERSACIONES -->
            @if ($seccion === 'conversaciones')
                <section class="tab-pane fade show active" id="tab-conversaciones">
                    @include('components.flash-messages')
                    <div class="row g-3">
                        <div class="col-lg-4">
                            <div class="card p-3">
                                <h6 class="mb-3">Conversaciones</h6>
                                <div class="list-group sidebar-threads" id="threadList"></div>
                            </div>
                        </div>
                        <div class="col-lg-8 d-flex flex-column">
                            <div class="chat-window d-flex flex-column mb-3" id="chatWindow"></div>
                            <div class="input-group">
                                <input type="text" class="form-control" id="chatInput"
                                    placeholder="Escribe un mensaje…">
                                <button class="btn btn-success" id="chatSend">Enviar</button>
                            </div>
                        </div>
                    </div>
                </section>
            @endif

            <!-- CONFIG -->
            @if ($seccion === 'configuracion')
                <section class="tab-pane fade show active" id="tab-config">
                    @include('components.flash-messages')
                    <div class="card">
                        <div class="card-body">
                            <h6 class="mb-3">Preferencias del punto ECA</h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Mostrar en mapa público</label>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="cfgMapa">
                                        <label class="form-check-label" for="cfgMapa">Visible cuando esté
                                            aprobado</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Recibir notificaciones</label>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="cfgNoti">
                                        <label class="form-check-label" for="cfgNoti">Aprobaciones, mensajes,
                                            comentarios</label>
                                    </div>
                                </div>
                            </div>
                            <div class="text-end mt-3"><button class="btn btn-success btn-sm"
                                    id="cfgGuardar">Guardar</button></div>
                        </div>
                    </div>
                </section>
            @endif

        </div>
    </main>

    <!-- MODAL: Alta de CENTRO -->
    <div class="modal fade" id="modalCentro" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form class="modal-content" id="formCentro">
                <div class="modal-header">
                    <h5 class="modal-title">Nuevo centro</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body row g-3">
                    <div class="col-12"><label class="form-label">Nombre</label><input type="text"
                            class="form-control" id="provNombre" required></div>
                    <div class="col-md-6"><label class="form-label">Tipo</label><select class="form-select"
                            id="provTipo">
                            <option>Centro de acopio</option>
                            <option>Centro</option>
                        </select></div>
                    <div class="col-md-6"><label class="form-label">Contacto</label><input type="text"
                            class="form-control" id="provContacto" required></div>
                    <div class="col-md-6"><label class="form-label">Teléfono</label><input type="tel"
                            class="form-control" id="provTelefono" required></div>
                    <div class="col-md-6"><label class="form-label">Correo</label><input type="email"
                            class="form-control" id="provCorreo" required></div>
                    <div class="col-12"><label class="form-label">Dirección</label><input type="text"
                            class="form-control" id="provDireccion" required></div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-outline-secondary" data-bs-dismiss="modal"
                        type="button">Cancelar</button>
                    <button class="btn btn-success" type="submit">Guardar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- TOAST -->
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index:1080">
        <div id="toastOK" class="toast align-items-center text-white bg-success border-0" role="alert"
            aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body" id="toastText">Acción realizada.</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto"
                    data-bs-dismiss="toast"></button>
            </div>
        </div>
    </div>

    @if ($seccion === 'movimientos')
        <script>
            (function() {
                const globalSelect = document.getElementById('centro_global');
                const propioSelect = document.getElementById('centro_propio');
                const hiddenInput = document.getElementById('centro_hidden');
                if (!globalSelect || !propioSelect || !hiddenInput) return;

                function sync(from, other) {
                    const val = from.value || '';
                    hiddenInput.value = val;
                    other.disabled = !!val;
                    if (!val) other.disabled = false;
                }
                globalSelect.addEventListener('change', () => sync(globalSelect, propioSelect));
                propioSelect.addEventListener('change', () => sync(propioSelect, globalSelect));
                window.addEventListener('pageshow', () => {
                    if (globalSelect.value) sync(globalSelect, propioSelect);
                    else if (propioSelect.value) sync(propioSelect, globalSelect);
                    else hiddenInput.value = '';
                });
            })();
        </script>
    @endif

    @if ($seccion === 'resumen' || $seccion === 'configuracion')
        <script>
            (function() {
                @if ($seccion === 'resumen' && isset($resumen))
                    const resumen = @json($resumen);
                    const fmt = (n) => {
                        try {
                            return (Number(n) || 0).toLocaleString('es-CO');
                        } catch (e) {
                            return n;
                        }
                    };
                    const setText = (id, val) => {
                        const el = document.getElementById(id);
                        if (el) el.textContent = val;
                    };
                    setText('kpiInventario', fmt(resumen.inventario_total));
                    setText('kpiEntradasMes', fmt(resumen.entradas_mes));
                    setText('kpiSalidasMes', fmt(resumen.salidas_mes));
                    setText('kpiProximoDespacho', resumen.proximo_despacho ?? '—');
                    const alertList = document.getElementById('alertList');
                    const alertCount = document.getElementById('alertCount');
                    if (alertList && alertCount) {
                        alertList.innerHTML = '';
                        if (Array.isArray(resumen.alertas) && resumen.alertas.length) {
                            alertCount.textContent = resumen.alertas.length.toString();
                            resumen.alertas.forEach(a => {
                                const div = document.createElement('div');
                                const rawTipo = (a?.tipo ?? '').toString();
                                // Normalizar (quitar acentos) para clasificación
                                const tipoNorm = rawTipo
                                    .toLowerCase()
                                    .normalize('NFD')
                                    .replace(/\p{Diacritic}/gu, '');
                                let badge = 'secondary';
                                let label = rawTipo.toUpperCase();
                                switch (tipoNorm) {
                                    case 'critico':
                                        badge = 'danger';
                                        label = 'CRÍTICO';
                                        break;
                                    case 'lleno':
                                        badge = 'warning';
                                        label = 'LLENO';
                                        break;
                                    case 'bajo':
                                        badge = 'secondary';
                                        label = 'BAJO';
                                        break;
                                }
                                const mensaje = a?.mensaje ?? a?.texto ?? '';
                                div.innerHTML =
                                    `<span class="badge bg-${badge} me-2 text-uppercase">${label}</span>${mensaje}`;
                                alertList.appendChild(div);
                            });
                        } else {
                            alertCount.textContent = '0';
                            alertList.textContent = 'Sin alertas.';
                        }
                    }
                @endif
                @if ($seccion === 'configuracion' && isset($config))
                    const cfg = @json($config);
                    const chk = (id, val) => {
                        const el = document.getElementById(id);
                        if (el) el.checked = !!val;
                    };
                    chk('cfgMapa', cfg.mostrar_mapa);
                    chk('cfgNoti', cfg.recibir_notificaciones);
                @endif
            })();
        </script>
    @endif


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>




</x-app-layout>
@push('scripts')
    <script>
        // Geolocalización precisa mejorada (mismo flujo que registro_eca)
        (function() {
            const btnPrimary = document.getElementById('btnGetUbicacion'); // Botón junto a campo lat
            const btnAlt = document.getElementById('btnUbicacion'); // Botón "Obtener ubicación" adicional
            const latInput = document.getElementById('puntoLatitud');
            const lngInput = document.getElementById('puntoLongitud');
            if (!latInput || !lngInput) return; // No continuar si faltan campos

            let busy = false;
            let watchId = null;
            let finished = false;

            function showToast(msg, type = 'info') {
                if (window.bootstrap) {
                    let el = document.getElementById('toastGeo');
                    if (!el) {
                        const container = document.createElement('div');
                        container.className = 'position-fixed bottom-0 end-0 p-3';
                        container.style.zIndex = 1080;
                        container.innerHTML =
                            `<div id="toastGeo" class="toast align-items-center text-bg-${type} border-0" role="alert" aria-live="assertive" aria-atomic="true"><div class="d-flex"><div class="toast-body">${msg}</div><button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Cerrar"></button></div></div>`;
                        document.body.appendChild(container);
                        el = document.getElementById('toastGeo');
                    } else {
                        el.querySelector('.toast-body').textContent = msg;
                        el.className = `toast align-items-center text-bg-${type} border-0`;
                    }
                    new bootstrap.Toast(el, {
                        delay: 2800
                    }).show();
                } else {
                    console.log(msg);
                }
            }

            function secureCheck() {
                if (isSecureContext) return true;
                const h = location.hostname;
                if (h === 'localhost' || h === '127.0.0.1') return true;
                showToast('Este dominio no es seguro para solicitar geolocalización. Usa HTTPS (ej: https://' + location
                    .host + ')', 'danger');
                return false;
            }

            async function permissionState() {
                if (!('permissions' in navigator)) return null;
                try {
                    const s = await navigator.permissions.query({
                        name: 'geolocation'
                    });
                    return s.state;
                } catch {
                    return null;
                }
            }

            function setBusy(state) {
                busy = state;
                if (btnPrimary) {
                    btnPrimary.disabled = state;
                    btnPrimary.textContent = state ? 'GPS…' : 'GPS';
                }
                if (btnAlt) {
                    btnAlt.disabled = state;
                    btnAlt.textContent = state ? 'Obteniendo…' : 'Obtener ubicación';
                }
            }

            function write(lat, lng) {
                if (lat == null || lng == null) return;
                lat = Number(lat);
                lng = Number(lng);
                if (isNaN(lat) || isNaN(lng)) return;
                latInput.value = lat.toFixed(6);
                lngInput.value = lng.toFixed(6);
            }

            function clearWatch() {
                if (watchId != null) {
                    navigator.geolocation.clearWatch(watchId);
                    watchId = null;
                }
            }

            function startWatchFallback() {
                if (!('geolocation' in navigator)) return;
                showToast('Escalando a seguimiento continuo…', 'warning');
                let gotFirst = false;
                watchId = navigator.geolocation.watchPosition(
                    pos => {
                        const {
                            latitude,
                            longitude,
                            accuracy
                        } = pos.coords;
                        if (!gotFirst) {
                            gotFirst = true;
                            write(latitude, longitude);
                            showToast('Ubicación (watch) ±' + Math.round(accuracy) + 'm', 'success');
                            setBusy(false);
                            // Mantener unos segundos por si mejora la precisión
                            setTimeout(() => clearWatch(), 5000);
                        } else if (accuracy < 25) {
                            write(latitude, longitude);
                        }
                    },
                    err => {
                        showToast('Watch falló (' + err.code + ').', 'danger');
                        setBusy(false);
                        clearWatch();
                    }, {
                        enableHighAccuracy: true,
                        timeout: 15000,
                        maximumAge: 0
                    }
                );
            }

            function attemptPrecise() {
                if (!('geolocation' in navigator)) {
                    showToast('Geolocalización no soportada', 'danger');
                    return;
                }
                if (!secureCheck()) return;
                setBusy(true);
                finished = false;
                showToast('Solicitando permiso…', 'info');
                const escalateTimer = setTimeout(() => {
                    if (!finished) {
                        startWatchFallback();
                    }
                }, 4000);
                navigator.geolocation.getCurrentPosition(
                    pos => {
                        if (finished) return;
                        finished = true;
                        clearTimeout(escalateTimer);
                        const {
                            latitude,
                            longitude,
                            accuracy
                        } = pos.coords;
                        write(latitude, longitude);
                        showToast('Ubicación capturada ±' + Math.round(accuracy) + 'm', 'success');
                        setBusy(false);
                    },
                    err => {
                        if (finished) return;
                        finished = true;
                        clearTimeout(escalateTimer);
                        const map = {
                            1: 'Permiso denegado',
                            2: 'Posición no disponible',
                            3: 'Tiempo agotado'
                        };
                        showToast((map[err.code] || 'Error geolocalización') + (err.code === 1 ?
                            ' (ajusta permisos del sitio)' : ''), 'danger');
                        if (err.code !== 1) {
                            startWatchFallback();
                        } else {
                            setBusy(false);
                        }
                    }, {
                        enableHighAccuracy: true,
                        timeout: 10000,
                        maximumAge: 0
                    }
                );
            }

            async function handleClick() {
                if (busy) return;
                const state = await permissionState();
                if (state === 'denied') {
                    showToast('Permiso previamente denegado. Habilita Ubicación en el candado del navegador.',
                        'danger');
                    return;
                }
                attemptPrecise();
            }

            if (btnPrimary) btnPrimary.addEventListener('click', handleClick);
            if (btnAlt) btnAlt.addEventListener('click', handleClick);
        })();
    </script>
@endpush
