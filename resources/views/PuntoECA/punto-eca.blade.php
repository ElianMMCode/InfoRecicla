<x-app-layout>

    <link rel="stylesheet" href="{{ asset('css/PuntoECA/punto-eca.css') }}">
    <style>
        /* Pequeño apoyo visual para los “tabs” sin JS via :target */
        #f_categoria,
        #f_tipo,
        #f_nombre,
        #q_categoria,
        #q_tipo,
        #q_nombre {
            display: none;
        }

        :target#f_categoria,
        :target#f_tipo,
        :target#f_nombre,
        :target#q_categoria,
        :target#q_tipo,
        :target#q_nombre {
            display: block;
        }
    </style>
    <!-- NAVBAR -->
    <x-navbar-layout>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#nav" aria-controls="nav"
            aria-expanded="false" aria-label="Alternar navegación">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div id="nav" class="collapse navbar-collapse">
            <ul class="navbar-nav ms-auto align-items-lg-center gap-2">
                <li class="nav-item"><a class="nav-link" href="/publicaciones">Publicaciones</a></li>
                <li class="nav-item"><a class="nav-link" href="/mapa">Mapa ECA</a></li>
                    <li class="nav-item">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="btn btn-light text-success fw-semibold px-3">Cerrar sesión</button>
                        </form>
                    </li>
                </ul>   

        </div>
    </x-navbar-layout>

    <main class="container my-4">
        <!-- TABS PRINCIPALES -->
        <ul class="nav nav-pills" id="mainTabs" role="tablist">
            <li class="nav-item" role="presentation"><button
                    class="nav-link {{ $seccion === 'resumen' ? 'active' : '' }}" data-bs-toggle="tab"
                    data-bs-target="#tab-panel" type="button">Resumen</button></li>
            <li class="nav-item" role="presentation"><button
                    class="nav-link {{ $seccion === 'perfil' ? 'active' : '' }}" data-bs-toggle="tab"
                    data-bs-target="#tab-perfil" type="button">Perfil</button></li>
            <li class="nav-item" role="presentation"><button
                    class="nav-link {{ $seccion === 'materiales' ? 'active' : '' }}" data-bs-toggle="tab"
                    data-bs-target="#tab-materiales" type="button">Materiales</button></li>
            <li class="nav-item" role="presentation"><button
                    class="nav-link {{ $seccion === 'historial' ? 'active' : '' }}" data-bs-toggle="tab"
                    data-bs-target="#tab-historial" type="button">Historial</button></li>
            <li class="nav-item" role="presentation"><button
                    class="nav-link {{ $seccion === 'calendario' ? 'active' : '' }}" data-bs-toggle="tab"
                    data-bs-target="#tab-calendario" type="button">Calendario</button></li>
            <li class="nav-item" role="presentation"><button
                    class="nav-link {{ $seccion === 'centros' ? 'active' : '' }}" data-bs-toggle="tab"
                    data-bs-target="#tab-centros" type="button">Centros</button></li>
            <li class="nav-item" role="presentation"><button
                    class="nav-link {{ $seccion === 'conversaciones' ? 'active' : '' }}" data-bs-toggle="tab"
                    data-bs-target="#tab-conversaciones" type="button">Conversaciones</button></li>
            <li class="nav-item" role="presentation"><button
                    class="nav-link {{ $seccion === 'configuracion' ? 'active' : '' }}" data-bs-toggle="tab"
                    data-bs-target="#tab-config" type="button">Configuración</button></li>
        </ul>

        <div class="tab-content pt-3">

            <!-- RESUMEN -->
            <section class="tab-pane fade {{ $seccion === 'resumen' ? 'show active' : '' }}" id="tab-panel">
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
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <strong>Alertas de capacidad / umbrales</strong>
                        <span class="badge badge-soft" id="alertCount">0</span>
                    </div>
                    <div class="card-body">
                        <div id="alertList" class="vstack gap-2 small text-muted">Sin alertas.</div>
                    </div>
                </div>
            </section>

            <section class="tab-pane fade {{ $seccion === 'perfil' ? 'show active' : '' }}" id="tab-perfil">
                <div class="row g-4">
                    <!-- ===== SUBMÓDULO: Datos del Encargado ===== -->
                    <div class="col-lg-6">
                        <div class="card card-hover">
                            <div class="card-body">
                                <h5 class="mb-3">Encargado</h5>

                                <!-- Foto del encargado (opcional) -->
                                <div class="d-flex align-items-center gap-3 mb-3">
                                    <img id="previewPerfil" src="../images/perfil_default.png" alt="Foto encargado"
                                        class="rounded-circle img-thumbnail"
                                        style="width:96px;height:96px;object-fit:cover;">
                                    <div class="flex-grow-1">
                                        <div class="small text-muted">Foto (opcional)</div>
                                        <input class="form-control form-control-sm" type="file" id="fotoPerfil"
                                            accept="image/*">
                                    </div>
                                </div>

                                <!-- Form datos del encargado -->
                                <form id="formEncargado" class="row g-3">
                                    <div class="col-12 col-md-6">
                                        <label class="form-label">Nombre</label>
                                        <input type="text" class="form-control" id="mgrNombre" placeholder="—">
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label class="form-label">Teléfono</label>
                                        <input type="tel" class="form-control" id="mgrTelefono" placeholder="—">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">Correo</label>
                                        <input type="email" class="form-control" id="mgrEmail" placeholder="—">
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- ===== SUBMÓDULO: Datos del Punto ECA ===== -->
                    <div class="col-lg-6">
                        <div class="card card-hover">
                            <div class="card-body">
                                <h5 class="mb-3">Punto ECA</h5>

                                <!-- Foto del punto (opcional) -->
                                <div class="d-flex align-items-center gap-3 mb-3">
                                    <img id="previewPunto" src="../images/eca-default.png" alt="Foto punto"
                                        class="img-thumbnail rounded"
                                        style="width:140px;height:100px;object-fit:cover;">
                                    <div class="flex-grow-1">
                                        <div class="small text-muted">Foto (opcional)</div>
                                        <input class="form-control form-control-sm" type="file" id="fotoPunto"
                                            accept="image/*">
                                    </div>
                                </div>

                                <!-- Form datos del punto -->
                                <form id="formPunto" class="row g-3">
                                    <div class="col-12">
                                        <label class="form-label">Nombre del Punto</label>
                                        <input type="text" class="form-control" id="puntoNombre" placeholder="—">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">Dirección</label>
                                        <input type="text" class="form-control" id="puntoDireccion"
                                            placeholder="—">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label">Ciudad</label>
                                        <input type="text" class="form-control" id="puntoCiudad" placeholder="—">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label">Localidad</label>
                                        <input type="text" class="form-control" id="puntoLocalidad"
                                            placeholder="—">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">Coordenadas</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" id="puntoLatLng"
                                                placeholder="Latitud, Longitud" readonly>
                                            <button class="btn btn-outline-secondary" type="button"
                                                id="btnUbicacion">Obtener
                                                ubicación</button>
                                        </div>
                                        <div class="form-text">Usa tu ubicación del navegador para rellenar
                                            automáticamente.</div>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">Horario</label>
                                        <input type="text" class="form-control" id="puntoHorario"
                                            placeholder="Lun–Vie 8:00–17:00">
                                    </div>
                                </form>

                                <div class="text-end mt-3">
                                    <button class="btn btn-success" id="btnGuardarPerfil">Guardar cambios</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- MATERIALES (UNIFICA INVENTARIO + REGISTRO) -->
            <section class="tab-pane fade {{ $seccion === 'materiales' ? 'show active' : '' }}" id="tab-materiales"
                role="tabpanel" aria-labelledby="materiales-tab"
                data-url="{{ url('punto-eca/materiales') }}"id="tab-materiales">
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <strong class="me-2">Filtrar por:</strong>
                            <a class="btn btn-sm btn-outline-success" href="#f_categoria">Categoría</a>
                            <a class="btn btn-sm btn-outline-success" href="#f_tipo">Tipo</a>
                            <a class="btn btn-sm btn-outline-success" href="#f_nombre">Nombre</a>
                        </div>
                    </div>
                    <div class="card-body">
                        {{-- ÚNICO formulario contenedor (GET) para los tres filtros --}}
                        <form id="form-filtros"
                            action="{{ route('punto-eca.seccion', ['seccion' => 'materiales']) }}" method="get">
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
                                    <button class="btn btn-success w-100" type="submit" form="form-filtros">Aplicar
                                        filtro</button>
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
                                    <button class="btn btn-success w-100" type="submit" form="form-filtros">Aplicar
                                        filtro</button>
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
                                </div>
                                <div class="col-md-3">
                                    <button class="btn btn-success w-100" type="submit" form="form-filtros">Aplicar
                                        filtro</button>
                                </div>
                            </div>
                        </div>

                        {{-- ¡OJO! No dupliques otro <form id="form-filtros"> aquí. --}}
                    </div>
                </div>

                <div>

                    <!-- Formulario "contenedor" para enviar filtros por GET/POST cuando lo integres -->
                    <form id="form-filtros" action="#" method="get"></form>

                    <!-- Tabla de materiales disponibles (selección y registro) -->
                    <div class="table-responsive">
                        <table class="table table-sm align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th style="min-width: 160px;">Material</th>
                                    <th>Categoría</th>
                                    <th>Tipo</th>
                                    <!-- Campos de inventario a completar por fila -->
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
                                <!--
              Cada fila corresponde a un material disponible.
              Más adelante, con PHP, iteras tus materiales y rellenas valores iniciales.
            -->
                                @foreach ($materiales as $material)
                                    {{-- 1) Form oculto por fila (el único que se envía) --}}
                                    <form id="reg-{{ $material->id }}"
                                        action="{{ route('punto-eca.inventario.store') }}" method="post"
                                        style="display:none;">
                                        @csrf
                                        <input type="hidden" name="material_id" value="{{ $material->id }}">
                                    </form>

                                    <tr>
                                        <td>
                                            <div class="text-muted small">{{ $material->nombre }}</div>
                                        </td>
                                        <td>{{ $material->categoria->nombre }}</td>
                                        <td>{{ $material->tipo->nombre }}</td>

                                        {{-- 2) TODOS los inputs referenciando el form con form="reg-...": --}}
                                        <td>
                                            <input class="form-control form-control-sm" type="number" step="0.001"
                                                name="capacidad_max" placeholder="0.000"
                                                form="reg-{{ $material->id }}">
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
                                        </td>

                                        <td><input class="form-control form-control-sm" type="number" step="0.001"
                                                name="stock_actual" placeholder="0.000"
                                                form="reg-{{ $material->id }}"></td>
                                        <td><input class="form-control form-control-sm" type="number" step="0.001"
                                                name="umbral_alerta" placeholder="0.000"
                                                form="reg-{{ $material->id }}"></td>
                                        <td><input class="form-control form-control-sm" type="number" step="0.001"
                                                name="umbral_critico" placeholder="0.000"
                                                form="reg-{{ $material->id }}"></td>
                                        <td><input class="form-control form-control-sm" type="number" step="0.01"
                                                name="precio_compra" placeholder="0.00"
                                                form="reg-{{ $material->id }}"></td>
                                        <td><input class="form-control form-control-sm" type="number" step="0.01"
                                                name="precio_venta" placeholder="0.00"
                                                form="reg-{{ $material->id }}"></td>
                                        <td>
                                            <select class="form-select form-select-sm" name="activo"
                                                form="reg-{{ $material->id }}">
                                                <option value="1">1</option>
                                                <option value="0">0</option>
                                            </select>
                                        </td>

                                        <td class="text-end">
                                            <button type="submit" class="btn btn-success btn-sm"
                                                form="reg-{{ $material->id }}">
                                                Registrar
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach



                                <!-- Duplica/itera filas con tus materiales cuando integres PHP -->

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

                <!-- ========================================= -->
                <!-- 2) CONSULTAR / EDITAR / ELIMINAR REGISTRADOS -->
                <!-- ========================================= -->
                <div class="card">
                    <div class="card-header bg-light">
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                            <strong>Materiales registrados</strong>
                            <div class="d-flex gap-2">
                                <a class="btn btn-sm btn-outline-success" href="#q_categoria">Categoría</a>
                                <a class="btn btn-sm btn-outline-success" href="#q_tipo">Tipo</a>
                                <a class="btn btn-sm btn-outline-success" href="#q_nombre">Nombre</a>
                            </div>
                        </div>
                    </div>

                    <div class="card-body">

                        {{-- ===== Filtros (inventario) ===== --}}
                        <form id="form-consulta"
                            action="{{ route('punto-eca.seccion', ['seccion' => 'materiales']) }}" method="get">
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
                                    <button class="btn btn-primary btn-success w-100" type="submit"
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
                                    <button class="btn btn-primary w-100" type="submit"
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
                                    <button class="btn btn-primary w-100" type="submit"
                                        form="form-consulta">Buscar</button>
                                </div>
                            </div>
                        </div>

                        {{-- ===== Tabla editable ===== --}}
                        <div class="table-responsive">
                            <table class="table table-sm align-middle">
                                <thead class="table-light">
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
                                        {{-- Form de UPDATE oculto por fila --}}
                                        <form id="upd-{{ $inv->id }}"
                                            action="{{ route('punto-eca.inventario.update', $inv->id) }}"
                                            method="post" style="display:none;">
                                            @csrf
                                            @method('PUT')
                                            {{-- No enviamos punto_eca_id: lo fija el controlador con el DEFAULT --}}
                                        </form>

                                        {{-- Form de DELETE oculto por fila --}}
                                        <form id="del-{{ $inv->id }}"
                                            action="{{ route('punto-eca.inventario.destroy', $inv->id) }}"
                                            method="post" style="display:none;">
                                            @csrf
                                            @method('DELETE')
                                        </form>

                                        <tr>
                                            <td>
                                                <div class="fw-semibold">{{ $inv->material->nombre ?? '—' }}</div>
                                            </td>
                                            <td>{{ $inv->material->categoria->nombre ?? '—' }}</td>
                                            <td>{{ $inv->material->tipo->nombre ?? '—' }}</td>

                                            <td>
                                                <input class="form-control form-control-sm" type="number"
                                                    step="0.001" name="capacidad_max"
                                                    value="{{ $inv->capacidad_max }}"
                                                    form="upd-{{ $inv->id }}">
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
                                            </td>

                                            <td>
                                                <input class="form-control form-control-sm" type="number"
                                                    step="0.001" name="stock_actual"
                                                    value="{{ $inv->stock_actual }}" form="upd-{{ $inv->id }}">
                                            </td>

                                            <td>
                                                <input class="form-control form-control-sm" type="number"
                                                    step="0.001" name="umbral_alerta"
                                                    value="{{ $inv->umbral_alerta }}"
                                                    form="upd-{{ $inv->id }}">
                                            </td>

                                            <td>
                                                <input class="form-control form-control-sm" type="number"
                                                    step="0.001" name="umbral_critico"
                                                    value="{{ $inv->umbral_critico }}"
                                                    form="upd-{{ $inv->id }}">
                                            </td>

                                            <td>
                                                <input class="form-control form-control-sm" type="number"
                                                    step="0.01" name="precio_compra"
                                                    value="{{ $inv->precio_compra }}"
                                                    form="upd-{{ $inv->id }}">
                                            </td>

                                            <td>
                                                <input class="form-control form-control-sm" type="number"
                                                    step="0.01" name="precio_venta"
                                                    value="{{ $inv->precio_venta }}" form="upd-{{ $inv->id }}">
                                            </td>

                                            <td>
                                                <select class="form-select form-select-sm" name="activo"
                                                    form="upd-{{ $inv->id }}">
                                                    <option value="1" @selected($inv->activo)>1</option>
                                                    <option value="0" @selected(!$inv->activo)>0</option>
                                                </select>
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
                                            <td colspan="13" class="text-center text-muted">Aún no hay materiales
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

            <!-- HISTORIAL GLOBAL -->
            <section class="tab-pane fade {{ $seccion === 'historial' ? 'show active' : '' }}" id="tab-historial"
                id="tab-historial">
                <ul class="nav nav-tabs" role="tablist">
                    <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab"
                            data-bs-target="#hist-compras" type="button">Compras (Entradas)</button></li>
                    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab"
                            data-bs-target="#hist-salidas" type="button">Salidas (Centros)</button></li>
                </ul>
                <div class="tab-content border border-top-0 rounded-bottom p-3">
                    <div class="tab-pane fade show active" id="hist-compras">
                        <div class="row g-2 mb-2">
                            <div class="col-md-3"><input id="hcDesde" type="date" class="form-control"></div>
                            <div class="col-md-3"><input id="hcHasta" type="date" class="form-control"></div>
                            <div class="col-md-3"><select id="hcMaterial" class="form-select">
                                    <option value="">Todos los materiales</option>
                                </select></div>
                            <div class="col-md-3 d-grid"><button id="hcFiltrar"
                                    class="btn btn-outline-success">Filtrar</button></div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-sm table-striped align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Material</th>
                                        <th>Kg</th>
                                        <th>Proveedor</th>
                                        <th>Precio/Kg</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody id="hcTabla"></tbody>
                            </table>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="hist-salidas">
                        <div class="row g-2 mb-2">
                            <div class="col-md-3"><input id="hsDesde" type="date" class="form-control"></div>
                            <div class="col-md-3"><input id="hsHasta" type="date" class="form-control"></div>
                            <div class="col-md-3"><select id="hsMaterial" class="form-select">
                                    <option value="">Todos los materiales</option>
                                </select></div>
                            <div class="col-md-3 d-grid"><button id="hsFiltrar"
                                    class="btn btn-outline-success">Filtrar</button></div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-sm table-striped align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Material</th>
                                        <th>Kg</th>
                                        <th>Centro de acopio</th>
                                        <th>Precio/Kg</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody id="hsTabla"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </section>

            <!-- CALENDARIO GLOBAL -->
            <section class="tab-pane fade {{ $seccion === 'calendario' ? 'show active' : '' }}" id="tab-calendario">
                <div class="row g-3">
                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-body">
                                <div class="calendar">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <div class="h5 mb-0" id="calTitulo">—</div>
                                        <div><button class="btn btn-sm btn-outline-secondary"
                                                id="calPrev">◀</button>
                                            <button class="btn btn-sm btn-outline-secondary" id="calNext">▶</button>
                                        </div>
                                    </div>
                                    <div class="grid text-center small text-muted mb-1">
                                        <div>Lun</div>
                                        <div>Mar</div>
                                        <div>Mié</div>
                                        <div>Jue</div>
                                        <div>Vie</div>
                                        <div>Sáb</div>
                                        <div>Dom</div>
                                    </div>
                                    <div class="grid" id="calGrid"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="card h-100">
                            <div class="card-body">
                                <h6>Programar nuevo despacho</h6>
                                <div class="mb-2"><label class="form-label">Material</label><select id="calMat"
                                        class="form-select"></select></div>
                                <div class="mb-2"><label class="form-label">Frecuencia</label>
                                    <select id="calFreq" class="form-select">
                                        <option value="unico">Único</option>
                                        <option value="semanal">Semanal</option>
                                        <option value="quincenal">Cada 15 días</option>
                                        <option value="mensual">Mensual</option>
                                    </select>
                                </div>
                                <div class="mb-2"><label class="form-label">Fecha inicial</label><input
                                        id="calFecha" type="date" class="form-control"></div>
                                <div class="mb-2"><label class="form-label">Hora</label><input id="calHora"
                                        type="time" class="form-control" value="10:00"></div>
                                <div class="mb-2"><label class="form-label">Centro de acopio</label><select
                                        id="calCentro" class="form-select"></select></div>
                                <button id="calGuardar" class="btn btn-success w-100">Guardar</button>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- CENTROS /  -->
            <section class="tab-pane fade {{ $seccion === 'centros' ? 'show active' : '' }}" id="tab-centros">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h5 class="mb-0">Centros de acopio </h5>
                    <button id="btnNuevoProveedor" class="btn btn-success btn-sm" data-bs-toggle="modal"
                        data-bs-target="#modalProveedor">Añadir</button>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm table-striped align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Nombre</th>
                                <th>Tipo</th>
                                <th>Contacto</th>
                                <th>Teléfono</th>
                                <th>Correo</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="provTabla"></tbody>
                    </table>
                </div>
            </section>

            <!-- CONVERSACIONES -->
            <section class="tab-pane fade {{ $seccion === 'conversaciones' ? 'show active' : '' }}"
                id="tab-conversaciones">
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

            <!-- CONFIG -->
            <section class="tab-pane fade {{ $seccion === 'configuracion' ? 'show active' : '' }}" id="tab-config">
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
                    <div class="col-md-6"><label class="form-label">Email</label><input type="email"
                            class="form-control" id="provEmail" required></div>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>


</x-app-layout>
