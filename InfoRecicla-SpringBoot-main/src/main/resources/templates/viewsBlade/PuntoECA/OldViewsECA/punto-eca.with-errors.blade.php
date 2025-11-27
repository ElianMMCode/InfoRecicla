<x-app-layout>

    <link rel="stylesheet" href="{{ asset('css/PuntoECA/punto-eca.css') }}">

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
                        <button type="submit" class="btn btn-light text-success fw-semibold px-3">Cerrar
                            sesión</button>
                    </form>
                </li>
            </ul>

        </div>
    </x-navbar-layout>

    <main class="container my-4">
        <!-- TABS DE TODAS LAS SECCIONES-->
        <ul class="nav nav-pills" id="mainTabs" role="tablist">
            <!-- Dependiendo de la seccion que se ingrese por url esta se vera activa-->
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
                    class="nav-link {{ $seccion === 'movimientos' ? 'active' : '' }}" data-bs-toggle="tab"
                    data-bs-target="#tab-movimientos" type="button">Movimientos</button></li>
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
                                            <input class="form-control form-control-sm @error(\'usuarios.avatar\') is-invalid @enderror" type="file"
                                                id="fotoPerfil" name="usuarios[avatar]" accept="image/*">
@error('usuarios.avatar')
    <div class="invalid-feedback d-block">{{ $message }}</div>
@enderror
                                            @error('usuarios.avatar')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="row g-3">
                                        <div class="col-12 col-md-6">
                                            <label class="form-label">Nombre</label>
                                            <input type="text" class="form-control @error(\'usuarios.nombre\') is-invalid @enderror" id="mgrNombre"
                                                name="usuarios[nombre]"
                                                value="{{ old('usuarios.nombre', $usuarios->
@error('usuarios.nombre')
    <div class="invalid-feedback d-block">{{ $message }}</div>
@enderrornombre) }}">
                                            @error('usuarios.nombre')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <label class="form-label">Apellido</label>
                                            <input type="text" class="form-control @error(\'usuarios.apellido\') is-invalid @enderror" id="mgrNombre"
                                                name="usuarios[apellido]"
                                                value="{{ old('usuarios.apellido', $usuarios->
@error('usuarios.apellido')
    <div class="invalid-feedback d-block">{{ $message }}</div>
@enderrorapellido) }}">
                                            @error('usuarios.apellido')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-12 col-md-6">
                                            <label class="form-label">Teléfono</label>
                                            <input type="tel" class="form-control @error(\'usuarios.telefono\') is-invalid @enderror" id="mgrTelefono"
                                                name="usuarios[telefono]"
                                                value="{{ old('usuarios.telefono', $usuarios->
@error('usuarios.telefono')
    <div class="invalid-feedback d-block">{{ $message }}</div>
@enderrortelefono) }}">
                                            @error('usuarios.telefono')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label">Correo</label>
                                            <input type="email" class="form-control @error(\'usuarios.correo\') is-invalid @enderror" id="mgrEmail"
                                                name="usuarios[correo]"
                                                value="{{ old('usuarios.correo', $usuarios->
@error('usuarios.correo')
    <div class="invalid-feedback d-block">{{ $message }}</div>
@enderrorcorreo) }}">
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
                                            <input type="password" class="form-control @error(\'usuarios.current_password\') is-invalid @enderror"
                                                name="usuarios[current_password]" autocomplete="current-password">
@error('usuarios.current_password')
    <div class="invalid-feedback d-block">{{ $message }}</div>
@enderror
                                            @error('usuarios.current_password')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <label class="form-label">Nueva contraseña</label>
                                            <input type="password" class="form-control @error(\'usuarios.password\') is-invalid @enderror" name="usuarios[password]"
                                                autocomplete="new-password">
@error('usuarios.password')
    <div class="invalid-feedback d-block">{{ $message }}</div>
@enderror
                                            @error('usuarios.password')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <label class="form-label">Confirmación</label>
                                            <input type="password" class="form-control @error(\'usuarios.password_confirmation\') is-invalid @enderror"
                                                name="usuarios[password_confirmation]" autocomplete="new-password">
@error('usuarios.password_confirmation')
    <div class="invalid-feedback d-block">{{ $message }}</div>
@enderror
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
                                            <input class="form-control form-control-sm @error(\'punto.foto\') is-invalid @enderror" type="file" id="fotoPunto"
                                                name="punto[foto]" accept="image/*">
@error('punto.foto')
    <div class="invalid-feedback d-block">{{ $message }}</div>
@enderror
                                            @error('punto.foto')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror

                                            <div class="small text-muted mt-2">Logo (opcional)</div>
                                            <input class="form-control form-control-sm @error(\'punto.logo\') is-invalid @enderror" type="file"
                                                name="punto[logo]" accept="image/*">
@error('punto.logo')
    <div class="invalid-feedback d-block">{{ $message }}</div>
@enderror
                                            @error('punto.logo')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="row g-3">
                                        <div class="col-12">
                                            <label class="form-label">Nombre del Punto</label>
                                            <input type="text" class="form-control @error(\'punto.nombre\') is-invalid @enderror" id="puntoNombre"
                                                name="punto[nombre]"
                                                value="{{ old('punto.nombre', $punto->
@error('punto.nombre')
    <div class="invalid-feedback d-block">{{ $message }}</div>
@enderrornombre) }}">
                                            @error('punto.nombre')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label">Dirección</label>
                                            <input type="text" class="form-control @error(\'punto.direccion\') is-invalid @enderror" id="puntoDireccion"
                                                name="punto[direccion]"
                                                value="{{ old('punto.direccion', $punto->
@error('punto.direccion')
    <div class="invalid-feedback d-block">{{ $message }}</div>
@enderrordireccion) }}">
                                            @error('punto.direccion')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-6">
                                            <label class="form-label">Ciudad</label>
                                            <input type="text" class="form-control @error(\'punto.ciudad\') is-invalid @enderror" id="puntoCiudad"
                                                name="punto[ciudad]"
                                                value="{{ old('punto.ciudad', $punto->
@error('punto.ciudad')
    <div class="invalid-feedback d-block">{{ $message }}</div>
@enderrorciudad) }}">
                                            @error('punto.ciudad')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-6">
                                            <label class="form-label">Localidad</label>
                                            <input type="text" class="form-control @error(\'punto.localidad\') is-invalid @enderror" id="puntoLocalidad"
                                                name="punto[localidad]"
                                                value="{{ old('punto.localidad', $punto->
@error('punto.localidad')
    <div class="invalid-feedback d-block">{{ $message }}</div>
@enderrorlocalidad) }}">
                                            @error('punto.localidad')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-12">
                                            <label class="form-label">Horario</label>
                                            <input type="text" class="form-control @error(\'punto.horario_atencion\') is-invalid @enderror" id="puntoHorario"
                                                name="punto[horario_atencion]"
                                                value="{{ old('punto.horario_atencion', $punto->
@error('punto.horario_atencion')
    <div class="invalid-feedback d-block">{{ $message }}</div>
@enderrorhorario_atencion) }}">
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

            <!-- MATERIALES -->
            <section class="tab-pane fade {{ $seccion === 'materiales' ? 'show active' : '' }}" id="tab-materiales"
                role="tabpanel" aria-labelledby="materiales-tab" data-url="{{ url('punto-eca/materiales') }}">
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
                        {{-- formulario para los filtros --}}
                        <form id="form-filtros" action="{{ route('eca.materiales.index') }}" method="get">
                        </form>

                        {{-- Panel: Categoría --}}
                        <div class="border rounded p-3 mb-3" id="f_categoria">
                            <div class="row g-3 align-items-end">
                                <div class="col-md-6">
                                    <label class="form-label">Categoría</label>
                                    <select class="form-select @error(\'categoria\') is-invalid @enderror" name="categoria" form="form-filtros">
                                        <option value="">Todas</option>
                                        @foreach ($categorias as $c)
                                            <option value="{{ $c->id }}" @selected(request('categoria') === $c->id)>
                                                {{ $c->nombre }}</option>
                                        @endforeach
                                    </select>
@error('categoria')
    <div class="invalid-feedback d-block">{{ $message }}</div>
@enderror
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
                                    <select class="form-select @error(\'tipo\') is-invalid @enderror" name="tipo" form="form-filtros">
                                        <option value="">Todos</option>
                                        @foreach ($tipos as $t)
                                            <option value="{{ $t->id }}" @selected(request('tipo') === $t->id)>
                                                {{ $t->nombre }}</option>
                                        @endforeach
                                    </select>
@error('tipo')
    <div class="invalid-feedback d-block">{{ $message }}</div>
@enderror
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
                                    <input class="form-control @error(\'nombre\') is-invalid @enderror" type="text" name="nombre"
                                        value="{{ request('nombre') }}" placeholder="Ej. Botella PET"
                                        form="form-filtros">
@error('nombre')
    <div class="invalid-feedback d-block">{{ $message }}</div>
@enderror
                                </div>
                                <div class="col-md-3">
                                    <button class="btn btn-success w-100" type="submit" form="form-filtros">Aplicar
                                        filtro</button>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <div>

                    <!-- Tabla de materiales disponibles (selección y registro) -->
                    <div class="table-responsive">
                        <table class="table table-sm align-middle">
                            <thead class="table-light">
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
                                >
                                @foreach ($materiales as $material)
                                    <form id="reg-{{ $material->id }}" action="{{ route('eca.inventario.store') }}"
                                        method="post" style="display:none;">
                                        @csrf
                                        <input type="hidden" name="material_id" value="{{ $material- class="@error('material_id') is-invalid @enderror">
@error('material_id')
    <div class="invalid-feedback d-block">{{ $message }}</div>
@enderrorid }}">
                                    </form>

                                    <tr>
                                        <td>
                                            <div class="text-muted small">{{ $material->nombre }}</div>
                                        </td>
                                        <td>{{ $material->categoria->nombre }}</td>
                                        <td>{{ $material->tipo->nombre }}</td>

                                        <td>
                                            <input class="form-control form-control-sm @error(\'capacidad_max\') is-invalid @enderror" type="number" step="0.001"
                                                name="capacidad_max" placeholder="0.000"
                                                form="reg-{{ $material->
@error('capacidad_max')
    <div class="invalid-feedback d-block">{{ $message }}</div>
@enderrorid }}">
                                        </td>

                                        <td>
                                            <select class="form-select form-select-sm @error(\'unidad_medida\') is-invalid @enderror" name="unidad_medida"
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

                                        <td><input class="form-control form-control-sm @error(\'stock_actual\') is-invalid @enderror" type="number" step="0.001"
                                                name="stock_actual" placeholder="0.000"
                                                form="reg-{{ $material->
@error('stock_actual')
    <div class="invalid-feedback d-block">{{ $message }}</div>
@enderrorid }}"></td>
                                        <td><input class="form-control form-control-sm @error(\'umbral_alerta\') is-invalid @enderror" type="number" step="0.001"
                                                name="umbral_alerta" placeholder="0.000"
                                                form="reg-{{ $material->
@error('umbral_alerta')
    <div class="invalid-feedback d-block">{{ $message }}</div>
@enderrorid }}"></td>
                                        <td><input class="form-control form-control-sm @error(\'umbral_critico\') is-invalid @enderror" type="number" step="0.001"
                                                name="umbral_critico" placeholder="0.000"
                                                form="reg-{{ $material->
@error('umbral_critico')
    <div class="invalid-feedback d-block">{{ $message }}</div>
@enderrorid }}"></td>
                                        <td><input class="form-control form-control-sm @error(\'precio_compra\') is-invalid @enderror" type="number" step="0.01"
                                                name="precio_compra" placeholder="0.00"
                                                form="reg-{{ $material->
@error('precio_compra')
    <div class="invalid-feedback d-block">{{ $message }}</div>
@enderrorid }}"></td>
                                        <td><input class="form-control form-control-sm @error(\'precio_venta\') is-invalid @enderror" type="number" step="0.01"
                                                name="precio_venta" placeholder="0.00"
                                                form="reg-{{ $material->
@error('precio_venta')
    <div class="invalid-feedback d-block">{{ $message }}</div>
@enderrorid }}"></td>
                                        <td>
                                            <select class="form-select form-select-sm @error(\'activo\') is-invalid @enderror" name="activo"
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

                        {{-- Filtros  --}}
                        <form id="form-consulta" action="{{ route('eca.materiales.index') }}" method="get">
                        </form>

                        <div class="border rounded p-3 mb-3" id="q_categoria">
                            <div class="row g-3 align-items-end">
                                <div class="col-md-6">
                                    <label class="form-label">Categoría</label>
                                    <select class="form-select @error(\'q_categoria\') is-invalid @enderror" name="q_categoria" form="form-consulta">
                                        <option value="">Todas</option>
                                        @foreach ($categorias as $c)
                                            <option value="{{ $c->id }}" @selected(request('q_categoria') === $c->id)>
                                                {{ $c->nombre }}</option>
                                        @endforeach
                                    </select>
@error('q_categoria')
    <div class="invalid-feedback d-block">{{ $message }}</div>
@enderror
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
                                    <select class="form-select @error(\'q_tipo\') is-invalid @enderror" name="q_tipo" form="form-consulta">
                                        <option value="">Todos</option>
                                        @foreach ($tipos as $t)
                                            <option value="{{ $t->id }}" @selected(request('q_tipo') === $t->id)>
                                                {{ $t->nombre }}</option>
                                        @endforeach
                                    </select>
@error('q_tipo')
    <div class="invalid-feedback d-block">{{ $message }}</div>
@enderror
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
                                    <input class="form-control @error(\'q_nombre\') is-invalid @enderror" type="text" name="q_nombre"
                                        value="{{ request('q_nombre') }}" placeholder="Ej. Botella PET"
                                        form="form-consulta">
@error('q_nombre')
    <div class="invalid-feedback d-block">{{ $message }}</div>
@enderror
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
                                        <form id="upd-{{ $inv->id }}"
                                            action="{{ route('eca.inventario.update', $inv->id) }}" method="post"
                                            style="display:none;">
                                            @csrf
                                            @method('PUT')
                                        </form>

                                        <form id="del-{{ $inv->id }}"
                                            action="{{ route('eca.inventario.destroy', $inv->id) }}" method="post"
                                            style="display:none;">
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
                                                <input class="form-control form-control-sm @error(\'capacidad_max\') is-invalid @enderror" type="number"
                                                    step="0.001" name="capacidad_max"
                                                    value="{{ $inv->
@error('capacidad_max')
    <div class="invalid-feedback d-block">{{ $message }}</div>
@enderrorcapacidad_max }}"
                                                    form="upd-{{ $inv->id }}">
                                            </td>

                                            <td>
                                                <select class="form-select form-select-sm @error(\'unidad_medida\') is-invalid @enderror" name="unidad_medida"
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
                                                <input class="form-control form-control-sm @error(\'stock_actual\') is-invalid @enderror" type="number"
                                                    step="0.001" name="stock_actual"
                                                    value="{{ $inv->
@error('stock_actual')
    <div class="invalid-feedback d-block">{{ $message }}</div>
@enderrorstock_actual }}" form="upd-{{ $inv->id }}">
                                            </td>

                                            <td>
                                                <input class="form-control form-control-sm @error(\'umbral_alerta\') is-invalid @enderror" type="number"
                                                    step="0.001" name="umbral_alerta"
                                                    value="{{ $inv->
@error('umbral_alerta')
    <div class="invalid-feedback d-block">{{ $message }}</div>
@enderrorumbral_alerta }}"
                                                    form="upd-{{ $inv->id }}">
                                            </td>

                                            <td>
                                                <input class="form-control form-control-sm @error(\'umbral_critico\') is-invalid @enderror" type="number"
                                                    step="0.001" name="umbral_critico"
                                                    value="{{ $inv->
@error('umbral_critico')
    <div class="invalid-feedback d-block">{{ $message }}</div>
@enderrorumbral_critico }}"
                                                    form="upd-{{ $inv->id }}">
                                            </td>

                                            <td>
                                                <input class="form-control form-control-sm @error(\'precio_compra\') is-invalid @enderror" type="number"
                                                    step="0.01" name="precio_compra"
                                                    value="{{ $inv->
@error('precio_compra')
    <div class="invalid-feedback d-block">{{ $message }}</div>
@enderrorprecio_compra }}"
                                                    form="upd-{{ $inv->id }}">
                                            </td>

                                            <td>
                                                <input class="form-control form-control-sm @error(\'precio_venta\') is-invalid @enderror" type="number"
                                                    step="0.01" name="precio_venta"
                                                    value="{{ $inv->
@error('precio_venta')
    <div class="invalid-feedback d-block">{{ $message }}</div>
@enderrorprecio_venta }}" form="upd-{{ $inv->id }}">
                                            </td>

                                            <td>
                                                <select class="form-select form-select-sm @error(\'activo\') is-invalid @enderror" name="activo"
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

            <!-- Entradas y Salidas de Material -->
            <section class="tab-pane fade {{ $seccion === 'movimientos' ? 'show active' : '' }}"
                id="tab-movimientos">
                <div class="row gy-4">
                    <div class="row g-4">

                        <!--  ENTRADA  -->
                        <div class="col-12 col-lg-6">
                            <div class="card card-hover h-100">
                                <div class="card-header bg-success text-white">
                                    <strong>Registrar ENTRADA (Compra)</strong>
                                </div>
                                <div class="card-body">
                                    <form action="{{ route('eca.movimientos.compra.store') }}" method="post"
                                        class="vstack gap-3">
                                        @csrf

                                        <label class="form-label">Inventario / Material</label>
                                        <select name="compra[inventario_id]" class="form-select @error(\'compra.inventario_id\') is-invalid @enderror" required>
                                            <option value="" disabled selected>— Selecciona —</option>
                                            @foreach ($inventario as $inv)
                                                <option value="{{ $inv->id }}" @selected(old('compra.inventario_id') == $inv->id)>
                                                    {{ $inv->material->nombre }} (Stock:
                                                    {{ $inv->stock_actual ?? 0 }}
                                                    {{ $inv->unidad_medida ?? '' }} | Precio:
                                                    {{ $inv->precio_compra }})

                                                </option>
                                            @endforeach
                                        </select>
@error('compra.inventario_id')
    <div class="invalid-feedback d-block">{{ $message }}</div>
@enderror
                                        @error('compra.inventario_id')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror

                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label">Fecha</label>
                                                <input type="date" name="compra[fecha]" class="form-control @error(\'compra.fecha\') is-invalid @enderror"
                                                    value="{{ old('compra.fecha') }}" required>
@error('compra.fecha')
    <div class="invalid-feedback d-block">{{ $message }}</div>
@enderror
                                                @error('compra.fecha')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Cantidad</label>
                                                <input type="number" name="compra[cantidad]" step="0.001"
                                                    min="0.001" class="form-control @error(\'compra.cantidad\') is-invalid @enderror"
                                                    value="{{ old('compra.cantidad') }}" required>
@error('compra.cantidad')
    <div class="invalid-feedback d-block">{{ $message }}</div>
@enderror
                                                @error('compra.cantidad')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <label class="form-label">Observaciones (opcional)</label>
                                        <textarea name="compra[observaciones]" rows="2" class="form-control @error(\'compra.observaciones\') is-invalid @enderror">{{ old('compra.observaciones') }}</textarea>
@error('compra.observaciones')
    <div class="invalid-feedback d-block">{{ $message }}</div>
@enderror
                                        <div class="text-end">
                                            <button type="submit" class="btn btn-success">Guardar
                                                entrada</button>
                                        </div>
                                    </form>
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
                                    <form action="{{ route('eca.movimientos.venta.store') }}" method="post"
                                        class="vstack gap-3">
                                        @csrf

                                        <label class="form-label">Inventario / Material</label>
                                        <select name="venta[inventario_id]" class="form-select @error(\'venta.inventario_id\') is-invalid @enderror" required>
                                            <option value="" disabled selected>— Selecciona —</option>
                                            @foreach ($inventario as $inv)
                                                <option value="{{ $inv->id }}" @selected(old('venta.inventario_id') == $inv->id)>
                                                    {{ $inv->material->nombre }} (Stock:
                                                    {{ $inv->stock_actual ?? 0 }}
                                                    {{ $inv->unidad_medida ?? '' }} | Precio:
                                                    {{ $inv->precio_compra }})
                                                </option>
                                            @endforeach
                                        </select>
@error('venta.inventario_id')
    <div class="invalid-feedback d-block">{{ $message }}</div>
@enderror
                                        @error('venta.inventario_id')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror

                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label">Fecha</label>
                                                <input type="date" name="venta[fecha]" class="form-control @error(\'venta.fecha\') is-invalid @enderror"
                                                    value="{{ old('venta.fecha') }}" required>
@error('venta.fecha')
    <div class="invalid-feedback d-block">{{ $message }}</div>
@enderror
                                                @error('venta.fecha')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Cantidad</label>
                                                <input type="number" name="venta[cantidad]" step="0.001"
                                                    min="0.001" class="form-control @error(\'venta.cantidad\') is-invalid @enderror"
                                                    value="{{ old('venta.cantidad') }}" required>
@error('venta.cantidad')
    <div class="invalid-feedback d-block">{{ $message }}</div>
@enderror
                                                @error('venta.cantidad')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>

                                        </div>

                                        <div class="row g-3">
                                            <div class="col-md-5">
                                                <label class="form-label">Centro de acopio (Opcional)</label>
                                                <select id="centro_global" class="form-select">
                                                    <option value="">— Selecciona —</option>
                                                    @foreach ($centrosGlobalesLista as $cag)
                                                        <option value="{{ $cag->id }}">
                                                            {{ $cag->nombre }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="col-md-7">
                                                <label class="form-label">Centro de acopios personales
                                                    (Opcional)</label>
                                                <select id="centro_propio" class="form-select">
                                                    <option value="">— Selecciona —</option>
                                                    @foreach ($centrosPropiosLista as $cag)
                                                        <option value="{{ $cag->id }}">
                                                            {{ $cag->nombre }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <!-- Hidden centro seleccionado -->
                                        <input type="hidden" name="venta[centro_acopio_id]" id="centro_hidden" class="@error('venta.centro_acopio_id') is-invalid @enderror">
@error('venta.centro_acopio_id')
    <div class="invalid-feedback d-block">{{ $message }}</div>
@enderror
                                        @error('venta.centro_acopio_id')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror


                                        <label class="form-label">Observaciones (opcional)</label>
                                        <textarea name="venta[observaciones]" rows="2" class="form-control @error(\'venta.observaciones\') is-invalid @enderror">{{ old('venta.observaciones') }}</textarea>
@error('venta.observaciones')
    <div class="invalid-feedback d-block">{{ $message }}</div>
@enderror

                                        <div class="text-end">
                                            <button type="submit" class="btn btn-danger">Guardar salida</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                    </div>



                    <div class="col-12">
                        <div class="card">
                            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                                <strong>Últimos movimientos</strong>
                                <span class="text-muted small">Compras y ventas recientes</span>
                            </div>

                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-sm align-middle">
                                        <thead class="table-light">
                                            <tr>
                                                <th style="width: 110px;">Fecha</th>
                                                <th style="width: 90px;">Tipo</th>
                                                <th>Material</th>
                                                <th class="text-end" style="width: 140px;">Cantidad</th>
                                                <th style="width: 80px;">Unidad</th>
                                                <th class="text-end" style="width: 160px;">Precio</th>
                                                <th>Observaciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($ultimosMovimientos as $m)
                                                <tr>
                                                    <td>{{ $m['fecha'] }}</td>
                                                    <td>
                                                        <span
                                                            class="badge {{ $m['tipo'] === 'compra' ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }}">
                                                            {{ ucfirst($m['tipo']) }}
                                                        </span>
                                                    </td>
                                                    <td>{{ $m['material'] }}</td>
                                                    <td class="text-end">
                                                        {{ number_format($m['cantidad'] ?? 0, 3) }}
                                                    </td>
                                                    <td>{{ $m['unidad'] }}</td>
                                                    <td class="text-end">
                                                        @if (!is_null($m['precio_unit']))
                                                            {{ number_format($m['precio_unit'], 2) }}
                                                        @else
                                                            —
                                                        @endif
                                                    </td>
                                                    <td class="text-truncate" style="max-width: 320px;">
                                                        {{ $m['observ'] ?? '—' }}
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="7" class="text-center text-muted">No hay
                                                        movimientos
                                                        recientes.</td>
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



            <!-- HISTORIAL GLOBAL -->
            <section class="tab-pane fade {{ $seccion === 'historial' ? 'show active' : '' }}" id="tab-historial">
                <ul class="nav nav-tabs" role="tablist">
                    <li class="nav-item">
                        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#hist-compras"
                            type="button">
                            Compras (Entradas)
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#hist-salidas" type="button">
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
                                <input name="hc_desde" type="date" class="form-control @error(\'hc_desde\') is-invalid @enderror"
                                    value="{{ request('hc_desde') }}">
@error('hc_desde')
    <div class="invalid-feedback d-block">{{ $message }}</div>
@enderror
                            </div>
                            <div class="col-md-3">
                                <input name="hc_hasta" type="date" class="form-control @error(\'hc_hasta\') is-invalid @enderror"
                                    value="{{ request('hc_hasta') }}">
@error('hc_hasta')
    <div class="invalid-feedback d-block">{{ $message }}</div>
@enderror
                            </div>
                            <div class="col-md-3">
                                <select name="hc_material" class="form-select @error(\'hc_material\') is-invalid @enderror">
                                    <option value="">Todos los materiales</option>
                                    @foreach ($materialesPunto ?? [] as $m)
                                        <option value="{{ $m->id }}" @selected(request('hc_material') === $m->id)>
                                            {{ $m->nombre }}</option>
                                    @endforeach
                                </select>
@error('hc_material')
    <div class="invalid-feedback d-block">{{ $message }}</div>
@enderror
                            </div>
                            <div class="col-md-3 d-grid">
                                <button class="btn btn-outline-success">Filtrar</button>
                            </div>
                        </form>

                        <div class="table-responsive">
                            <table class="table table-sm table-striped align-middle">
                                <thead class="table-light">
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
                                            <td class="text-end">{{ number_format($c->cantidad ?? 0, 3) }}</td>
                                            <td>{{ $c->inventario->unidad_medida ?? '' }}</td>
                                            <td class="text-end">
                                                {{ is_numeric($c->inventario->precio_compra) ? number_format($c->inventario->precio_compra, 2) : '—' }}
                                            </td>
                                            <td class="text-end">
                                                {{ number_format($c->precio_compra, 2) }}
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
                                <input name="hs_desde" type="date" class="form-control @error(\'hs_desde\') is-invalid @enderror"
                                    value="{{ request('hs_desde') }}">
@error('hs_desde')
    <div class="invalid-feedback d-block">{{ $message }}</div>
@enderror
                            </div>
                            <div class="col-md-3">
                                <input name="hs_hasta" type="date" class="form-control @error(\'hs_hasta\') is-invalid @enderror"
                                    value="{{ request('hs_hasta') }}">
@error('hs_hasta')
    <div class="invalid-feedback d-block">{{ $message }}</div>
@enderror
                            </div>
                            <div class="col-md-3">
                                <select name="hs_material" class="form-select @error(\'hs_material\') is-invalid @enderror">
                                    <option value="">Todos los materiales</option>
                                    @foreach ($materialesPunto ?? [] as $m)
                                        <option value="{{ $m->id }}" @selected(request('hs_material') === $m->id)>
                                            {{ $m->nombre }}</option>
                                    @endforeach
                                </select>
@error('hs_material')
    <div class="invalid-feedback d-block">{{ $message }}</div>
@enderror
                            </div>
                            <div class="col-md-3 d-grid">
                                <button class="btn btn-outline-success">Filtrar</button>
                            </div>
                        </form>

                        <div class="table-responsive">
                            <table class="table table-sm table-striped align-middle">
                                <thead class="table-light">
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
                                            <td class="text-end">{{ number_format($v->cantidad ?? 0, 3) }}</td>
                                            <td>{{ $v->inventario->unidad_medida ?? '' }}</td>
                                            <td class="text-end">
                                                {{ is_numeric($v->precio_venta) ? number_format($v->precio_venta, 2) : '—' }}
                                            </td>
                                            <td class="text-end">
                                                @php
                                                    $total =
                                                        (float) ($v->cantidad ?? 0) * (float) ($v->precio_venta ?? 0);
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

            <section class="tab-pane fade {{ $seccion === 'calendario' ? 'show active' : '' }}" id="tab-calendario">
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
                                        <select name="material_id" class="form-select @error(\'material_id\') is-invalid @enderror" required>
                                            <option value="" disabled selected>— selecciona —</option>
                                            @foreach ($inventario ?? [] as $inv)
                                                <option value="{{ $inv->material_id }}"
                                                    @selected(old('material_id') === $inv->material_id)>
                                                    {{ $inv->material->nombre ?? '—' }} (stock:
                                                    {{ $inv->stock_actual ?? 0 }} {{ $inv->unidad_medida ?? '' }})
                                                </option>
                                            @endforeach
                                        </select>
@error('material_id')
    <div class="invalid-feedback d-block">{{ $message }}</div>
@enderror
                                        @error('material_id')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div>
                                        <label class="form-label">centro de acopio</label>
                                        <select name="centro_acopio_id" class="form-select @error(\'centro_acopio_id\') is-invalid @enderror" required>
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
                                        @error('centro_acopio_id')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div>
                                        <label class="form-label">frecuencia</label>
                                        <select name="frecuencia" class="form-select @error(\'frecuencia\') is-invalid @enderror" required>
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
                                                <option value="{{ $v }}" @selected(old('frecuencia') === $v)>
                                                    {{ $t }}</option>
                                            @endforeach
                                        </select>
@error('frecuencia')
    <div class="invalid-feedback d-block">{{ $message }}</div>
@enderror
                                        @error('frecuencia')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="row g-3">
                                        <div class="col-6">
                                            <label class="form-label">fecha</label>
                                            <input type="date" name="fecha" class="form-control @error(\'fecha\') is-invalid @enderror"
                                                value="{{ old('fecha') ?? now()->
@error('fecha')
    <div class="invalid-feedback d-block">{{ $message }}</div>
@enderrortoDateString() }}" required>
                                        </div>
                                        <div class="col-6">
                                            <label class="form-label">hora</label>
                                            <input type="time" name="hora" class="form-control @error(\'hora\') is-invalid @enderror"
                                                value="{{ old('hora') ?? '10:00' }}" required>
@error('hora')
    <div class="invalid-feedback d-block">{{ $message }}</div>
@enderror
                                        </div>
                                    </div>

                                    <div>
                                        <label class="form-label">notas (opcional)</label>
                                        <textarea name="notas" class="form-control @error(\'notas\') is-invalid @enderror" rows="2" maxlength="300">{{ old('notas') }}</textarea>
@error('notas')
    <div class="invalid-feedback d-block">{{ $message }}</div>
@enderror
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
                                                        <div class="d-flex flex-wrap gap-2 align-items-center mb-1">
                                                            <span class="badge bg-success">{{ $hhmm }}</span>
                                                            <span><strong>{{ $ev['material'] ?? 'material' }}</strong></span>
                                                            <span class="text-muted">·
                                                                {{ $ev['centro'] ?? 'centro' }}</span>
                                                            <span class="text-muted">·
                                                                {{ $ev['frecuencia'] ?? '—' }}</span>
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





            <section class="tab-pane fade {{ $seccion === 'centros' ? 'show active' : '' }}" id="tab-centros">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0">Centros de acopio</h5>
                </div>

                {{-- FILTROS --}}
                <form class="row g-2 mb-4" method="get"
                    action="{{ route('eca.index', ['seccion' => 'centros']) }}">
                    <div class="col-12 col-md-3">
                        <input type="text" name="f_nombre" class="form-control @error(\'f_nombre\') is-invalid @enderror" placeholder="Buscar por nombre"
                            value="{{ request('f_nombre') }}">
@error('f_nombre')
    <div class="invalid-feedback d-block">{{ $message }}</div>
@enderror
                    </div>

                    <div class="col-6 col-md-2">
                        <select name="f_tipo" class="form-select @error(\'f_tipo\') is-invalid @enderror">
                            <option value="">Tipo (todos)</option>
                            @foreach (['Planta', 'Proveedor', 'Otro'] as $t)
                                <option value="{{ $t }}" @selected(request('f_tipo') === $t)>
                                    {{ $t }}</option>
                            @endforeach
                        </select>
@error('f_tipo')
    <div class="invalid-feedback d-block">{{ $message }}</div>
@enderror
                    </div>

                    <div class="col-6 col-md-2">
                        <select name="f_estado" class="form-select @error(\'f_estado\') is-invalid @enderror">
                            <option value="">Estado (todos)</option>
                            @foreach (['activo', 'inactivo', 'bloqueado'] as $e)
                                <option value="{{ $e }}" @selected(request('f_estado') === $e)>
                                    {{ ucfirst($e) }}</option>
                            @endforeach
                        </select>
@error('f_estado')
    <div class="invalid-feedback d-block">{{ $message }}</div>
@enderror
                    </div>

                    <div class="col-6 col-md-2">
                        <input type="text" name="f_localidad" class="form-control @error(\'f_localidad\') is-invalid @enderror" placeholder="Localidad"
                            value="{{ request('f_localidad') }}">
@error('f_localidad')
    <div class="invalid-feedback d-block">{{ $message }}</div>
@enderror
                    </div>

                    <div class="col-6 col-md-3">
                        <select name="f_materiales[]" class="form-select @error(\'f_materiales\') is-invalid @enderror" multiple size="4">
                            @foreach ($materialesPunto ?? [] as $m)
                                <option value="{{ $m->id }}" @selected(collect(request('f_materiales'))->contains($m->id))>
                                    {{ $m->nombre }}
                                </option>
                            @endforeach
                        </select>
@error('f_materiales')
    <div class="invalid-feedback d-block">{{ $message }}</div>
@enderror
                        <div class="form-text">Manten Ctrl para seleccionar varios con el mouse</div>
                    </div>

                    <div class="col-12 d-grid d-md-block">
                        <button class="btn btn-outline-success">Aplicar filtros</button>
                    </div>
                </form>

                <div class="row g-4">
                    <div class="col-12 col-lg-8">
                        {{-- Centros globales --}}
                        <div class="card card-hover mb-4">
                            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                                <strong>Centros globales</strong>
                                <span class="small text-muted">Catálogo general</span>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-sm table-striped align-middle">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Nombre</th>
                                                <th>Tipo</th>
                                                <th>Ciudad</th>
                                                <th>Materiales que recicla</th>
                                                <th>Contacto</th>
                                                <th>Teléfono</th>
                                                <th>Correo</th>
                                                <th>Estado</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse(($centrosGlobales ?? []) as $c)
                                                @php $nombresMat = ($c->materiales ?? collect())->pluck('nombre')->all(); @endphp
                                                <tr>
                                                    <td>{{ $c->nombre }}</td>
                                                    <td>{{ $c->tipo }}</td>
                                                    <td>{{ $c->ciudad ?? '—' }}</td>
                                                    <td class="text-truncate" style="max-width: 320px;">
                                                        {{ $nombresMat ? implode(', ', $nombresMat) : '—' }}
                                                    </td>
                                                    <td>{{ $c->contacto ?? '—' }}</td>
                                                    <td>{{ $c->telefono ?? '—' }}</td>
                                                    <td>{{ $c->correo ?? '—' }}</td>
                                                    <td>{{ ucfirst($c->estado) }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="8" class="text-center text-muted">Sin
                                                        resultados.
                                                    </td>
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
                            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                                <strong>Centros del Punto</strong>
                                <span class="small text-muted">Propios del Punto ECA</span>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-sm table-striped align-middle">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Nombre</th>
                                                <th>Tipo</th>
                                                <th>Ciudad</th>
                                                <th>Materiales que recicla</th>
                                                <th>Contacto</th>
                                                <th>Teléfono</th>
                                                <th>Estado</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse(($centrosPropios ?? []) as $c)
                                                @php $nombresMat = ($c->materiales ?? collect())->pluck('nombre')->all(); @endphp
                                                <tr>
                                                    <td>{{ $c->nombre }}</td>
                                                    <td>{{ $c->tipo }}</td>
                                                    <td>{{ $c->ciudad ?? '—' }}</td>
                                                    <td class="text-truncate" style="max-width: 320px;">
                                                        {{ $nombresMat ? implode(', ', $nombresMat) : '—' }}
                                                    </td>
                                                    <td>{{ $c->contacto ?? '—' }}</td>
                                                    <td>{{ $c->telefono ?? '—' }}</td>
                                                    <td>{{ ucfirst($c->estado) }}</td>
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
                    <div class="col-12 col-lg-4">
                        <div class="card card-hover h-100">
                            <div class="card-header bg-white">
                                <strong>Nuevo centro del punto</strong>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('eca.centros.store') }}" method="post"
                                    class="vstack gap-3">
                                    @csrf

                                    <div>
                                        <label class="form-label">Nombre</label>
                                        <input type="text" name="cac[nombre]" class="form-control @error(\'cac.nombre\') is-invalid @enderror"
                                            value="{{ old('cac.nombre') }}" required>
@error('cac.nombre')
    <div class="invalid-feedback d-block">{{ $message }}</div>
@enderror
                                        @error('cac.nombre')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Tipo</label>
                                            <select name="cac[tipo]" class="form-select @error(\'cac.tipo\') is-invalid @enderror" required>
                                                @foreach (['Planta', 'Proveedor', 'Otro'] as $t)
                                                    <option value="{{ $t }}"
                                                        @selected(old('cac.tipo') === $t)>
                                                        {{ $t }}</option>
                                                @endforeach
                                            </select>
@error('cac.tipo')
    <div class="invalid-feedback d-block">{{ $message }}</div>
@enderror
                                            @error('cac.tipo')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label">Materiales que recicla</label>
                                            <select name="cac[materiales][]" class="form-select @error(\'cac.materiales\') is-invalid @enderror" multiple
                                                size="6">
                                                @foreach ($materialesPunto ?? [] as $m)
                                                    <option value="{{ $m->id }}"
                                                        @selected(collect(old('cac.materiales', []))->contains($m->id))>
                                                        {{ $m->nombre }}
                                                    </option>
                                                @endforeach
                                            </select>
@error('cac.materiales')
    <div class="invalid-feedback d-block">{{ $message }}</div>
@enderror
                                            <div class="form-text">Selecciona uno o varios.</div>
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
                                        <input type="text" name="cac[descripcion]" class="form-control @error(\'cac.descripcion\') is-invalid @enderror"
                                            value="{{ old('cac.descripcion') }}">
@error('cac.descripcion')
    <div class="invalid-feedback d-block">{{ $message }}</div>
@enderror
                                        @error('cac.descripcion')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Contacto</label>
                                            <input type="text" name="cac[contacto]" class="form-control @error(\'cac.contacto\') is-invalid @enderror"
                                                value="{{ old('cac.contacto') }}">
@error('cac.contacto')
    <div class="invalid-feedback d-block">{{ $message }}</div>
@enderror
                                            @error('cac.contacto')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Teléfono</label>
                                            <input type="text" name="cac[telefono]" class="form-control @error(\'cac.telefono\') is-invalid @enderror"
                                                value="{{ old('cac.telefono') }}">
@error('cac.telefono')
    <div class="invalid-feedback d-block">{{ $message }}</div>
@enderror
                                            @error('cac.telefono')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Correo</label>
                                            <input type="email" name="cac[correo]" class="form-control @error(\'cac.correo\') is-invalid @enderror"
                                                value="{{ old('cac.correo') }}">
@error('cac.correo')
    <div class="invalid-feedback d-block">{{ $message }}</div>
@enderror
                                            @error('cac.correo')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Sitio web</label>
                                            <input type="url" name="cac[sitio_web]" class="form-control @error(\'cac.sitio_web\') is-invalid @enderror"
                                                value="{{ old('cac.sitio_web') }}">
@error('cac.sitio_web')
    <div class="invalid-feedback d-block">{{ $message }}</div>
@enderror
                                            @error('cac.sitio_web')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div>
                                        <label class="form-label">Horario de atención</label>
                                        <input type="text" name="cac[horario_atencion]" class="form-control @error(\'cac.horario_atencion\') is-invalid @enderror"
                                            value="{{ old('cac.horario_atencion') }}">
@error('cac.horario_atencion')
    <div class="invalid-feedback d-block">{{ $message }}</div>
@enderror
                                        @error('cac.horario_atencion')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Ciudad</label>
                                            <input type="text" name="cac[ciudad]" class="form-control @error(\'cac.ciudad\') is-invalid @enderror"
                                                value="{{ old('cac.ciudad') }}">
@error('cac.ciudad')
    <div class="invalid-feedback d-block">{{ $message }}</div>
@enderror
                                            @error('cac.ciudad')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Localidad</label>
                                            <input type="text" name="cac[localidad]" class="form-control @error(\'cac.localidad\') is-invalid @enderror"
                                                value="{{ old('cac.localidad') }}">
@error('cac.localidad')
    <div class="invalid-feedback d-block">{{ $message }}</div>
@enderror
                                            @error('cac.localidad')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div>
                                        <label class="form-label">Dirección</label>
                                        <input type="text" name="cac[direccion]" class="form-control @error(\'cac.direccion\') is-invalid @enderror"
                                            value="{{ old('cac.direccion') }}">
@error('cac.direccion')
    <div class="invalid-feedback d-block">{{ $message }}</div>
@enderror
                                        @error('cac.direccion')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="text-end">
                                        <button class="btn btn-success">Guardar centro</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

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
            <section class="tab-pane fade {{ $seccion === 'configuracion' ? 'show active' : '' }}"
                id="tab-config">
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

    <script>
        (function() {
            const globalSelect = document.getElementById('centro_global');
            const propioSelect = document.getElementById('centro_propio');
            const hiddenInput = document.getElementById('centro_hidden');

            function sync(from, other) {
                const val = from.value || '';
                hiddenInput.value = val;
                other.disabled = !!val;
                o
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

    <script>
        (function() {
            @if (isset($resumen))
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
                            const badge = (a.tipo === 'crítico') ? 'danger' :
                                (a.tipo === 'lleno') ? 'warning' :
                                'secondary';
                            div.innerHTML =
                                `<span class="badge bg-${badge} me-2 text-uppercase">${a.tipo}</span>${a.texto}`;
                            alertList.appendChild(div);
                        });
                    } else {
                        alertCount.textContent = '0';
                        alertList.textContent = 'Sin alertas.';
                    }
                }
            @endif

            @if (isset($config))
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


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>


</x-app-layout>
