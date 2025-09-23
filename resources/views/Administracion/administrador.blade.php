@if(isset($usuarioEdit))
<h2>Editar usuario</h2>
<form method="POST" action="{{ url('admin/usuario/edit/' . $usuarioEdit->id) }}">
    @csrf
    <div>
        <label for="nombre">Nombre:</label>
        <input type="text" name="nombre" id="nombre" value="{{ $usuarioEdit->nombre }}" required>
    </div>
    <div>
        <label for="correo">Correo:</label>
        <input type="email" name="correo" id="correo" value="{{ $usuarioEdit->correo }}" required>
    </div>
    <div>
        <label for="rol">Rol:</label>
        <select name="rol" id="rol" required>
            <option value="Administrador" @if($usuarioEdit->rol == 'Administrador') selected @endif>Administrador</option>
            <option value="GestorECA" @if($usuarioEdit->rol == 'GestorECA') selected @endif>GestorECA</option>
            <option value="Ciudadano" @if($usuarioEdit->rol == 'Ciudadano') selected @endif>Ciudadano</option>
        </select>
    </div>
    <button type="submit">Guardar</button>
</form>
@if(session('success'))
<div style="color: green;">{{ session('success') }}</div>
@endif
@endif
<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>InfoRecicla • Administración (Módulos)</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <x-navbar-layout />
    <div class="container-fluid">
        <div class="row min-vh-100">

            <!-- Sidebar (módulos) -->
            <aside
                class="col-12 col-lg-3 col-xl-2 bg-success-subtle border-end border-success-subtle p-3 position-sticky top-0"
                style="height:100vh; overflow:auto;">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <span class="badge rounded-pill text-bg-success">Admin</span>
                    <strong class="text-success">InfoRecicla</strong>
                </div>
                <div class="small text-success fw-semibold mb-2">Módulos</div>
                <div class="list-group">
                    <a href="#resumen"
                        class="list-group-item list-group-item-action active bg-success border-success">Resumen</a>
                    <a href="#mod-usuarios" class="list-group-item list-group-item-action">Usuarios</a>
                    <a href="#mod-ecas" class="list-group-item list-group-item-action">Puntos ECA</a>
                    <a href="#mod-materiales" class="list-group-item list-group-item-action">Materiales & Catálogos</a>
                    {{--
                    <a href="#mod-contenido" class="list-group-item list-group-item-action">Contenido</a>  Oculto temporalmente módulo Publicaciones 
                    <a href="#mod-mensajeria" class="list-group-item list-group-item-action">Mensajería</a>
                    <a href="#mod-reportes" class="list-group-item list-group-item-action">Reportes & Auditoría</a>
                    <a href="#mod-sistema" class="list-group-item list-group-item-action">Sistema</a>
                    --}}
                </div>
            </aside>

            <!-- Main -->
            <main class="col-12 col-lg-9 col-xl-10 p-3 p-lg-4">
                <!-- Topbar simple -->
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h1 class="h5 text-success m-0">Panel de Administración</h1>
                    <span class="badge text-bg-success">Elian</span>
                </div>

                <!-- ====== RESUMEN GLOBAL ====== -->
                <section id="resumen" class="mb-5">
                    <h2 class="h5 text-success mb-3">Resumen</h2>
                    <div class="row g-3 mb-3">
                        <div class="col-sm-6 col-xl-3">
                            <div class="card border-success h-100">
                                <div class="card-body">
                                    <div class="text-success">Usuarios</div>
                                    <div class="display-6 fw-semibold text-success">{{ $totalUsuarios }}</div>
                                    <div class="small text-success-50">Admins / Gestores / Ciudadanos</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-xl-3">
                            <div class="card border-success h-100">
                                <div class="card-body">
                                    <div class="text-success">Puntos ECA activos</div>
                                    <div class="display-6 fw-semibold text-success">{{ $totalPuntosECAactivos }}</div>
                                    <div class="small text-success-50">Con inventario</div>
                                </div>
                            </div>
                        </div>
                        {{--
                        <div class="col-sm-6 col-xl-3">
                            <div class="card border-success h-100">
                                <div class="card-body">
                                    <div class="text-success">Publicaciones</div>
                                    <div class="display-6 fw-semibold text-success">0</div>
                                    <div class="small text-success-50">Últimos 30 días</div>
                                </div>
                            </div>
                        </div>
                        --}}{{-- Tarjeta de publicaciones ocultada temporalmente --}}

                    </div>
                    <div class="row g-3">
                        <div class="col-12 col-xl-5">
                            <div class="card border-success h-100">
                                <div class="card-header bg-success-subtle border-success"><span
                                        class="fw-semibold text-success">Atajos</span></div>
                                <div class="card-body">
                                    <div class="row g-2">
                                        <div class="col-6 col-md-4"><a href="#usuarios-crear"
                                                class="btn btn-success w-100">Nuevo usuario</a></div>
                                        <div class="col-6 col-md-4"><a href="#ecas-crear"
                                                class="btn btn-success w-100">Nuevo ECA</a></div>
                                        <div class="col-6 col-md-4"><a href="#materiales-crear"
                                                class="btn btn-success w-100">Nuevo material</a></div>
                                        {{--
                                        <div class="col-6 col-md-4"><a href="#publicaciones-crear"
                                        class="btn btn-success w-100">Nueva publicación</a></div>
                                        Oculto temporalmente --}}

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- ====== MÓDULO: USUARIOS ====== -->
                <section id="mod-usuarios" class="mb-5">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h2 class="h5 text-success m-0">Usuarios</h2>
                    </div>
                    <ul class="nav nav-pills mb-3">
                        <li class="nav-item"><a href="#usuarios-resumen" class="nav-link active bg-success">Resumen</a>
                        </li>
                        <li class="nav-item"><a href="#usuarios-listado" class="nav-link">Listado</a></li>
                        <li class="nav-item"><a href="#usuarios-crear" class="nav-link">Crear</a></li>
                    </ul>

                    <!-- Resumen Usuarios -->
                    <div id="usuarios-resumen" class="mb-3">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="card border-success h-100">
                                    <div class="card-body">
                                        <div class="text-success">Administradores</div>
                                        <div class="display-6 text-success">{{ $totalAdmins }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card border-success h-100">
                                    <div class="card-body">
                                        <div class="text-success">Gestores ECA</div>
                                        <div class="display-6 text-success">{{ $totalGestores }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card border-success h-100">
                                    <div class="card-body">
                                        <div class="text-success">Ciudadanos</div>
                                        <div class="display-6 text-success">{{ $totalCiudadanos }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card border-success mt-3">
                            <div class="card-header bg-success-subtle border-success"><span
                                    class="fw-semibold text-success">Últimos registrados</span></div>
                            <div class="table-responsive">
                                <table class="table table-hover m-0">
                                    <thead class="table-success">
                                        <tr>
                                            <th>Nombre</th>
                                            <th>Correo</th>
                                            <th>Rol</th>
                                            <th>Estado</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($ultimosAdmins as $usuario)
                                        <tr>
                                            <td>{{ $usuario->nombre }}</td>
                                            <td>{{ $usuario->correo }}</td>
                                            <td>{{ $usuario->rol }}</td>
                                            <td><span class="badge text-bg-success">{{ $usuario->estado }}</span></td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Listado Usuarios -->
                    <div id="usuarios-listado" class="mb-3">
                        <div class="card border-success mb-2">
                            <div class="card-body">
                                <form class="row g-2" method="GET" action="{{ url('admin') }}">
                                    <div class="col-12 col-md">
                                        <label class="form-label text-success">Buscar</label>
                                        <input class="form-control" name="buscar" value="{{ request('buscar') }}" placeholder="nombre, correo…">
                                    </div>
                                    <div class="col-6 col-md-3">
                                        <label class="form-label text-success">Rol</label>
                                        <select class="form-select" name="rol">
                                            <option value="">Todos</option>
                                            <option value="Administrador" @if(request('rol')=='Administrador' ) selected @endif>Administrador</option>
                                            <option value="GestorECA" @if(request('rol')=='GestorECA' ) selected @endif>GestorECA</option>
                                            <option value="Ciudadano" @if(request('rol')=='Ciudadano' ) selected @endif>Ciudadano</option>
                                        </select>
                                    </div>
                                    <div class="col-6 col-md-3">
                                        <label class="form-label text-success">Estado</label>
                                        <select class="form-select" name="estado">
                                            <option value="">Todos</option>
                                            <option value="activo" @if(request('estado')=='activo' ) selected @endif>activo</option>
                                            <option value="inactivo" @if(request('estado')=='inactivo' ) selected @endif>inactivo</option>
                                            <option value="bloqueado" @if(request('estado')=='bloqueado' ) selected @endif>bloqueado</option>
                                        </select>
                                    </div>
                                    <div class="col-12 col-md-auto d-flex align-items-end">
                                        <button class="btn btn-outline-success" type="submit">Aplicar</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="card border-success">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle m-0">
                                    <thead class="table-success">
                                        <tr>
                                            <th>Nombre</th>
                                            <th>Correo</th>
                                            <th>Rol</th>
                                            <th>Estado</th>
                                            <th class="text-end">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($usuariosList as $usuario)
                                        <tr>
                                            <form method="POST" action="{{ url('admin/usuario/edit/' . $usuario->id) }}">
                                                @csrf
                                                <td><input type="text" name="nombre" value="{{ $usuario->nombre }}" class="form-control form-control-sm" required></td>
                                                <td><input type="email" name="correo" value="{{ $usuario->correo }}" class="form-control form-control-sm" required></td>
                                                <td>
                                                    <select name="rol" class="form-select form-select-sm" required>
                                                        <option value="Administrador" @if($usuario->rol == 'Administrador') selected @endif>Administrador</option>
                                                        <option value="GestorECA" @if($usuario->rol == 'GestorECA') selected @endif>GestorECA</option>
                                                        <option value="Ciudadano" @if($usuario->rol == 'Ciudadano') selected @endif>Ciudadano</option>
                                                    </select>
                                                </td>
                                                <td>
                                                    <select name="estado" class="form-select form-select-sm">
                                                        <option value="activo" @if($usuario->estado == 'activo') selected @endif>activo</option>
                                                        <option value="inactivo" @if($usuario->estado == 'inactivo') selected @endif>inactivo</option>
                                                        <option value="bloqueado" @if($usuario->estado == 'bloqueado') selected @endif>bloqueado</option>
                                                    </select>
                                                </td>
                                                <td class="text-end">
                                                    <button type="submit" class="btn btn-sm btn-success">Guardar</button>
                                                    <a class="btn btn-sm btn-outline-danger" href="#">Eliminar</a>
                                                </td>
                                            </form>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="card-footer d-flex justify-content-between align-items-center">
                                <span class="small text-success">Mostrando {{ $usuariosList->count() }} de {{ $usuariosList->total() }}</span>
                                <div>
                                    {{ $usuariosList->links('pagination::bootstrap-4') }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Crear Usuario -->
                    <div id="usuarios-crear">
                        <div class="card border-success">
                            <div class="card-body">
                                <form id="formUsuario" name="formUsuario" class="row g-3"
                                    action="{{ route('admin.usuarios.create') }}" method="POST">
                                    @csrf
                                    <div class="col-md-6"><label for="nombre"
                                            class="form-label text-success">Nombre</label><input id="nombre"
                                            name="nombre" type="text" class="form-control" required></div>
                                    <div class="col-md-6"><label for="apellido"
                                            class="form-label text-success">Apellido</label><input id="apellido"
                                            name="apellido" type="text" class="form-control" required></div>
                                    <div class="col-md-6"><label for="correo"
                                            class="form-label text-success">Correo</label><input id="correo"
                                            name="correo" type="email" class="form-control" required></div>
                                    <div class="col-md-6"><label for="password"
                                            class="form-label text-success">Contraseña</label><input id="password"
                                            name="password" type="password" class="form-control" required></div>
                                    <div class="col-md-4"><label for="rol"
                                            class="form-label text-success">Rol</label><select id="rol"
                                            name="rol" class="form-select" required>
                                            <option value="Administrador">Administrador</option>
                                            <option value="GestorECA">GestorECA</option>
                                            <option value="Ciudadano">Ciudadano</option>
                                        </select></div>
                                    <div class="col-md-4"><label for="estado"
                                            class="form-label text-success">Estado</label><select id="estado"
                                            name="estado" class="form-select">
                                            <option value="activo">activo</option>
                                            <option value="inactivo">inactivo</option>
                                            <option value="bloqueado">bloqueado</option>
                                        </select></div>
                                    <div class="col-md-4"><label for="genero"
                                            class="form-label text-success">Género</label><select id="genero"
                                            name="genero" class="form-select">
                                            <option value="Masculino">Masculino</option>
                                            <option value="Femenino">Femenino</option>
                                            <option value="Otro">Otro</option>
                                        </select></div>
                                    <div class="col-md-6"><label for="tipo_documento"
                                            class="form-label text-success">Tipo Documento</label><select
                                            id="tipo_documento" name="tipo_documento" class="form-select">
                                            <option value="Cédula de Ciudadanía">Cédula de Ciudadanía</option>
                                            <option value="Cédula de Extranjería">Cédula de Extranjería</option>
                                            <option value="Tarjeta de Identidad">Tarjeta de Identidad</option>
                                            <option value="Pasaporte">Pasaporte</option>
                                        </select></div>
                                    <div class="col-md-6"><label for="numero_documento"
                                            class="form-label text-success">Número Documento</label><input
                                            id="numero_documento" name="numero_documento" class="form-control"></div>
                                    <div class="col-md-6"><label for="telefono"
                                            class="form-label text-success">Teléfono</label><input id="telefono"
                                            name="telefono" class="form-control"></div>
                                    <div class="col-md-6"><label for="fecha_nacimiento"
                                            class="form-label text-success">Fecha Nacimiento</label><input
                                            id="fecha_nacimiento" name="fecha_nacimiento" type="date"
                                            class="form-control"></div>
                                    <div class="col-md-6"><label for="nombre_usuario"
                                            class="form-label text-success">Usuario</label><input id="nombre_usuario"
                                            name="nombre_usuario" class="form-control"></div>
                                    <div class="col-md-6"><label for="avatar_url"
                                            class="form-label text-success">Avatar URL</label><input id="avatar_url"
                                            name="avatar_url" type="url" class="form-control"></div>
                                    <div class="col-12"><button class="btn btn-success"
                                            type="submit">Guardar</button></div>
                                </form>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- ====== MÓDULO: PUNTOS ECA ====== -->
                <section id="mod-ecas" class="mb-5">
                    <h2 class="h5 text-success mb-2">Puntos ECA</h2>
                    <ul class="nav nav-pills mb-3">
                        <li class="nav-item"><a href="#ecas-resumen" class="nav-link active bg-success">Resumen</a>
                        </li>
                        <li class="nav-item"><a href="#ecas-listado" class="nav-link">Listado</a></li>
                        <li class="nav-item"><a href="#ecas-crear" class="nav-link">Crear</a></li>
                        <li class="nav-item"><a href="#programaciones" class="nav-link">Programaciones</a></li>
                    </ul>

                    <div id="ecas-resumen" class="mb-3">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="card border-success h-100">
                                    <div class="card-body">
                                        <div class="text-success">ECAs activos</div>
                                        <div class="display-6 text-success">{{ $totalPuntosECAactivos }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="ecas-listado" class="mb-3">
                        <div class="card border-success mb-2">
                            <div class="card-body">
                                <form method="GET" action="#mod-ecas" class="row g-2">
                                    <div class="col-12 col-md">
                                        <label class="form-label text-success">Buscar</label>
                                        <input class="form-control" name="buscar_eca" value="{{ request('buscar_eca') }}" placeholder="nombre, dirección…">
                                    </div>
                                    <div class="col-6 col-md-3">
                                        <label class="form-label text-success">Estado</label>
                                        <select class="form-select" name="estado_eca">
                                            <option value="">Todos</option>
                                            <option value="activo" @if(request('estado_eca')=='activo' ) selected @endif>activo</option>
                                            <option value="inactivo" @if(request('estado_eca')=='inactivo' ) selected @endif>inactivo</option>
                                            <option value="bloqueado" @if(request('estado_eca')=='bloqueado' ) selected @endif>bloqueado</option>
                                        </select>
                                    </div>
                                    <div class="col-6 col-md-3">
                                        <label class="form-label text-success">Gestor</label>
                                        <input class="form-control" name="gestor_eca" value="{{ request('gestor_eca') }}" placeholder="Nombre gestor">
                                    </div>
                                    <div class="col-12 col-md-auto d-flex align-items-end">
                                        <button type="submit" class="btn btn-outline-success">Aplicar</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="card border-success">
                            <div class="table-responsive">
                                <form method="POST" action="{{ route('admin.eca.update') }}">
                                    @csrf
                                    <table class="table table-hover m-0">
                                        <thead class="table-success">
                                            <tr>
                                                <th>Nombre</th>
                                                <th>Gestor</th>
                                                <th>Dirección</th>
                                                <th>Estado</th>
                                                <th class="text-end">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($puntosECA as $punto)
                                            <tr>
                                                <td><input type="text" name="nombre[{{ $punto->id }}]" value="{{ $punto->nombre }}" class="form-control form-control-sm"></td>
                                                <td>
                                                    @if($punto->gestor)
                                                    {{ $punto->gestor->nombre }} {{ $punto->gestor->apellido }}
                                                    @else
                                                    <span class="text-muted">Sin gestor</span>
                                                    @endif
                                                </td>
                                                <td><input type="text" name="direccion[{{ $punto->id }}]" value="{{ $punto->direccion }}" class="form-control form-control-sm"></td>
                                                <td>
                                                    <select name="estado[{{ $punto->id }}]" class="form-select form-select-sm">
                                                        <option value="activo" @if($punto->estado=='activo') selected @endif>activo</option>
                                                        <option value="inactivo" @if($punto->estado=='inactivo') selected @endif>inactivo</option>
                                                        <option value="bloqueado" @if($punto->estado=='bloqueado') selected @endif>bloqueado</option>
                                                    </select>
                                                </td>
                                                <td class="text-end">
                                                    <button type="submit" name="editar" value="{{ $punto->id }}" class="btn btn-sm btn-outline-success">Guardar</button>
                                                    <button type="submit" name="eliminar" value="{{ $punto->id }}" class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Eliminar este punto ECA?')">Eliminar</button>
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="5" class="text-center">No hay puntos ECA registrados.</td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </form>
                                <div class="p-2">
                                    {{ $puntosECA->links('pagination::bootstrap-4') }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="ecas-crear" class="mb-3">
                        <div class="card border-success">
                            <div class="card-body">
                                <form method="POST" action="{{ route('admin.eca.store') }}" id="ecaForm" novalidate
                                    enctype="multipart/form-data" class="vstack gap-4">
                                    @csrf
                                    <input type="hidden" name="tipo" value="GestorECA">
                                    <input type="hidden" name="account_type" value="eca">

                                    <p class="small text-success fw-bold mb-1">Datos de cuenta</p>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label for="correo" class="form-label small fw-semibold">Correo electrónico
                                                (acceso)</label>
                                            <input type="email"
                                                class="form-control form-control-sm @error('correo') is-invalid @enderror"
                                                id="correo" name="correo" value="{{ old('correo') }}"
                                                autocomplete="email" required>
                                            @error('correo')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-3">
                                            <label for="password" class="form-label small fw-semibold">Contraseña</label>
                                            <input type="password"
                                                class="form-control form-control-sm @error('password') is-invalid @enderror"
                                                id="password" name="password" minlength="8" maxlength="64" required>
                                            @error('password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-3">
                                            <label for="password_confirmation"
                                                class="form-label small fw-semibold">Confirmar</label>
                                            <input type="password"
                                                class="form-control form-control-sm @error('password_confirmation') is-invalid @enderror"
                                                id="password_confirmation" name="password_confirmation" required>
                                            @error('password_confirmation')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <p class="small text-success fw-bold mb-1 mt-2">Datos del gestor (propietario del punto)
                                    </p>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label for="nombre" class="form-label small fw-semibold">Nombres</label>
                                            <input type="text"
                                                class="form-control form-control-sm @error('nombre') is-invalid @enderror"
                                                id="nombre" name="nombre" value="{{ old('nombre') }}" required>
                                            @error('nombre')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6">
                                            <label for="apellido" class="form-label small fw-semibold">Apellidos</label>
                                            <input type="text"
                                                class="form-control form-control-sm @error('apellido') is-invalid @enderror"
                                                id="apellido" name="apellido" value="{{ old('apellido') }}" required>
                                            @error('apellido')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-4">
                                            <label for="tipoDocumento" class="form-label small fw-semibold">Tipo de
                                                documento</label>
                                            <select id="tipoDocumento" name="tipoDocumento"
                                                class="form-select form-select-sm @error('tipoDocumento') is-invalid @enderror"
                                                required>
                                                <option value="" disabled
                                                    {{ old('tipoDocumento') ? '' : 'selected' }}>Selecciona...</option>
                                                @php($docs = ['Cédula de Ciudadanía', 'Cédula de Extranjería', 'Tarjeta de Identidad', 'Pasaporte'])
                                                @foreach ($docs as $doc)
                                                <option value="{{ $doc }}"
                                                    {{ old('tipoDocumento') === $doc ? 'selected' : '' }}>
                                                    {{ $doc }}
                                                </option>
                                                @endforeach
                                            </select>
                                            @error('tipoDocumento')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-4">
                                            <label for="numeroDocumento" class="form-label small fw-semibold">Número de
                                                documento</label>
                                            <input type="text"
                                                class="form-control form-control-sm @error('numeroDocumento') is-invalid @enderror"
                                                id="numeroDocumento" name="numeroDocumento"
                                                value="{{ old('numeroDocumento') }}" minlength="5" maxlength="20"
                                                required>
                                            @error('numeroDocumento')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <p class="small text-success fw-bold mb-1 mt-2">Información de la estación</p>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label for="nombrePunto" class="form-label small fw-semibold">Nombre del Punto
                                                ECA</label>
                                            <input type="text"
                                                class="form-control form-control-sm @error('nombrePunto') is-invalid @enderror"
                                                id="nombrePunto" name="nombrePunto" value="{{ old('nombrePunto') }}"
                                                required>
                                            @error('nombrePunto')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-3">
                                            <label for="nit" class="form-label small fw-semibold">NIT
                                                (opcional)</label>
                                            <input type="text"
                                                class="form-control form-control-sm @error('nit') is-invalid @enderror"
                                                id="nit" name="nit" value="{{ old('nit') }}"
                                                maxlength="20" placeholder="Si aplica">
                                            @error('nit')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row g-3 mt-1">
                                        <div class="col-md-6">
                                            <label for="horarioAtencion" class="form-label small fw-semibold">Horario de
                                                atención (opcional)</label>
                                            <input type="text"
                                                class="form-control form-control-sm @error('horarioAtencion') is-invalid @enderror"
                                                id="horarioAtencion" name="horarioAtencion"
                                                value="{{ old('horarioAtencion') }}"
                                                placeholder="Lun-Vie 8:00–17:00, Sáb 9:00–13:00">
                                            @error('horarioAtencion')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row g-3 mt-1">
                                        <div class="col-md-6">
                                            <label for="correoPunto" class="form-label small fw-semibold">Correo de
                                                contacto del punto</label>
                                            <input type="email"
                                                class="form-control form-control-sm @error('correoPunto') is-invalid @enderror"
                                                id="correoPunto" name="correoPunto" value="{{ old('correoPunto') }}"
                                                placeholder="Puede ser distinto al de acceso" required>
                                            @error('correoPunto')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6">
                                            <label for="telefonoPunto" class="form-label small fw-semibold">Teléfono de
                                                contacto del punto</label>
                                            <input type="tel"
                                                class="form-control form-control-sm @error('telefonoPunto') is-invalid @enderror"
                                                id="telefonoPunto" name="telefonoPunto"
                                                value="{{ old('telefonoPunto') }}" inputmode="tel" pattern="^\d{10}$"
                                                maxlength="10" placeholder="Ej: 3XXXXXXXXX" required>
                                            @error('telefonoPunto')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row g-3 mt-1">
                                        <div class="col-md-6">
                                            <label for="direccionPunto" class="form-label small fw-semibold">Dirección del
                                                punto</label>
                                            <input type="text"
                                                class="form-control form-control-sm @error('direccionPunto') is-invalid @enderror"
                                                id="direccionPunto" name="direccionPunto"
                                                value="{{ old('direccionPunto') }}" required>
                                            @error('direccionPunto')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-3">
                                            <label for="ciudad" class="form-label small fw-semibold">Ciudad</label>
                                            <select id="ciudad" name="ciudad"
                                                class="form-select form-select-sm @error('ciudad') is-invalid @enderror"
                                                required>
                                                <option value="" disabled {{ old('ciudad') ? '' : 'selected' }}>
                                                    Selecciona...</option>
                                                <option value="Bogotá" {{ old('ciudad') === 'Bogotá' ? 'selected' : '' }}>
                                                    Bogotá</option>
                                            </select>
                                            @error('ciudad')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-3">
                                            <label for="localidadPunto"
                                                class="form-label small fw-semibold">Localidad</label>
                                            <select id="localidadPunto" name="localidadPunto"
                                                class="form-select form-select-sm @error('localidadPunto') is-invalid @enderror"
                                                required>
                                                <option value="" disabled
                                                    {{ old('localidadPunto') ? '' : 'selected' }}>Selecciona localidad...
                                                </option>
                                                @php($locs = ['Usaquén', 'Chapinero', 'Santa Fe', 'San Cristóbal', 'Usme', 'Tunjuelito', 'Bosa', 'Kennedy', 'Fontibón', 'Engativá', 'Suba', 'Barrios Unidos', 'Teusaquillo', 'Los Mártires', 'Antonio Nariño', 'Puente Aranda', 'La Candelaria', 'Rafael Uribe', 'Ciudad Bolívar', 'Sumapaz'])
                                                @foreach ($locs as $l)
                                                <option value="{{ $l }}"
                                                    {{ old('localidadPunto') === $l ? 'selected' : '' }}>
                                                    {{ $l }}
                                                </option>
                                                @endforeach
                                            </select>
                                            @error('localidadPunto')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="mt-2">
                                        <label class="form-label small fw-semibold mb-1">Ubicación (opcional)</label>
                                        <div class="row g-2 align-items-start">
                                            <div class="col-12 col-md-4">
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text">Lat</span>
                                                    <input type="text" class="form-control" id="latitudVisible"
                                                        placeholder="Ej: 4.609710" value="{{ old('latitud') }}">
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-4">
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text">Lng</span>
                                                    <input type="text" class="form-control" id="longitudVisible"
                                                        placeholder="Ej: -74.081749" value="{{ old('longitud') }}">
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-4 d-grid d-md-block">
                                                <button type="button" id="geoBtn"
                                                    class="btn btn-outline-success btn-sm w-100">GPS</button>
                                            </div>
                                        </div>
                                        <div id="locationDisplay" class="form-text mt-1">Pulsa GPS para solicitar permiso
                                            al navegador y capturar tu ubicación precisa.</div>
                                        <input type="hidden" id="latitud" name="latitud"
                                            value="{{ old('latitud') }}">
                                        <input type="hidden" id="longitud" name="longitud"
                                            value="{{ old('longitud') }}">
                                    </div>
                                    <div class="row g-3 mt-1">
                                        <div class="col-md-6">
                                            <label for="logo" class="form-label small fw-semibold">Logo del punto
                                                (opcional)</label>
                                            <input type="file"
                                                class="form-control form-control-sm @error('logo') is-invalid @enderror"
                                                id="logo" name="logo" accept="image/*">
                                            @error('logo')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6">
                                            <label for="foto" class="form-label small fw-semibold">Foto del punto
                                                (opcional)</label>
                                            <input type="file"
                                                class="form-control form-control-sm @error('foto') is-invalid @enderror"
                                                id="foto" name="foto" accept="image/*">
                                            @error('foto')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6">
                                            <label for="sitioWeb" class="form-label small fw-semibold">Sitio web
                                                (opcional)</label>
                                            <input type="url"
                                                class="form-control form-control-sm @error('sitioWeb') is-invalid @enderror"
                                                id="sitioWeb" name="sitioWeb" value="{{ old('sitioWeb') }}"
                                                placeholder="https://">
                                            @error('sitioWeb')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="form-check mt-3">
                                        <input class="form-check-input" type="checkbox" value="1" id="mostrarMapa"
                                            name="mostrarMapa" {{ old('mostrarMapa') ? 'checked' : '' }} required>
                                        <label class="form-check-label small" for="mostrarMapa">Mostrar el punto en el
                                            mapa público cuando sea aprobado.</label>
                                    </div>
                                    <div class="form-check mt-2">
                                        <input class="form-check-input" type="checkbox" value="1"
                                            id="recibeNotificaciones" name="recibeNotificaciones"
                                            {{ old('recibeNotificaciones') ? 'checked' : '' }} required>
                                        <label class="form-check-label small" for="recibeNotificaciones">Deseo recibir
                                            notificaciones (aprobaciones, comentarios, etc.).</label>
                                    </div>

                                    <div class="d-grid mt-3">
                                        <button type="submit" class="btn btn-success">Registrar Punto ECA</button>
                                    </div>
                                    <p class="text-center mt-3 mb-0 small">¿Ya tienes cuenta? <a
                                            href="{{ route('login') }}">Inicia sesión</a></p>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div id="programaciones">
                    </div>
                </section>

                <!-- ====== MÓDULO: MATERIALES & CATÁLOGOS ====== -->
                <section id="mod-materiales" class="mb-5">
                    <h2 class="h5 text-success mb-2">Materiales & Catálogos</h2>
                    <ul class="nav nav-pills mb-3">
                        <li class="nav-item"><a href="#materiales-resumen"
                                class="nav-link active bg-success">Resumen</a></li>
                        <li class="nav-item"><a href="#materiales-listado" class="nav-link">Materiales</a></li>
                        <li class="nav-item"><a href="#materiales-crear" class="nav-link">Crear</a></li>
                        <li class="nav-item"><a href="#tipos" class="nav-link">Tipos</a></li>
                        <li class="nav-item"><a href="#categorias-material" class="nav-link">Categorías</a></li>
                    </ul>

                    <div id="materiales-resumen" class="row g-3 mb-3">
                        <div class="col-md-4">
                            <div class="card border-success h-100">
                                <div class="card-body">
                                    <div class="text-success">Materiales</div>
                                    <div class="display-6 text-success">{{ $totalMateriales }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card border-success h-100">
                                <div class="card-body">
                                    <div class="text-success">Categorías</div>
                                    <div class="display-6 text-success">{{ $totalCategorias }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card border-success h-100">
                                <div class="card-body">
                                    <div class="text-success">Tipos</div>
                                    <div class="display-6 text-success">{{ $totalTipos }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="materiales-listado" class="card border-success mb-3">
                        <div class="card-body">
                            <form class="row g-2" method="GET" action="{{ url('admin') }}">
                                <div class="col-12 col-md">
                                    <label class="form-label text-success">Buscar</label>
                                    <input class="form-control" name="buscar_material" value="{{ request('buscar_material') }}" placeholder="nombre, descripción…">
                                </div>
                                <div class="col-6 col-md-3">
                                    <label class="form-label text-success">Categoría</label>
                                    <select class="form-select" name="categoria_material">
                                        <option value="">Todas</option>
                                        @foreach($categorias as $c)
                                        <option value="{{ $c->id }}" @if(request('categoria_material')==$c->id) selected @endif>{{ $c->nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-6 col-md-3">
                                    <label class="form-label text-success">Tipo</label>
                                    <select class="form-select" name="tipo_material">
                                        <option value="">Todos</option>
                                        @foreach($tipos as $t)
                                        <option value="{{ $t->id }}" @if(request('tipo_material')==$t->id) selected @endif>{{ $t->nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-12 col-md-auto d-flex align-items-end">
                                    <button class="btn btn-outline-success" type="submit">Aplicar</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="card border-success mb-3">
                        <div class="table-responsive">
                            <table class="table table-hover m-0">
                                <thead class="table-success">
                                    <tr>
                                        <th>Nombre</th>
                                        <th>Descripción</th>
                                        <th>Categoría</th>
                                        <th>Tipo</th>
                                        <th>Imagen URL</th>
                                        <th class="text-end">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($materiales as $material)
                                    <tr>
                                        <form method="POST" action="{{ url('materiales/update/' . $material->id) }}">
                                            @csrf
                                            <td><input type="text" name="nombre" value="{{ $material->nombre }}" class="form-control form-control-sm" required></td>
                                            <td><input type="text" name="descripcion" value="{{ $material->descripcion }}" class="form-control form-control-sm"></td>
                                            <td>
                                                <select name="categoria_id" class="form-select form-select-sm">
                                                    @foreach($categorias as $c)
                                                    <option value="{{ $c->id }}" @if($material->categoria_id == $c->id) selected @endif>{{ $c->nombre }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <select name="tipo_id" class="form-select form-select-sm">
                                                    @foreach($tipos as $t)
                                                    <option value="{{ $t->id }}" @if($material->tipo_id == $t->id) selected @endif>{{ $t->nombre }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td><input type="url" name="imagen_url" value="{{ $material->imagen_url }}" class="form-control form-control-sm"></td>
                                            <td class="text-end">
                                                <button type="submit" class="btn btn-sm btn-success">Guardar</button>
                                                <a class="btn btn-sm btn-outline-danger" href="#">Eliminar</a>
                                            </td>
                                        </form>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <div class="card-footer d-flex justify-content-between align-items-center">
                                <span class="small text-success">Mostrando {{ $materiales->count() }} de {{ $materiales->total() }}</span>
                                <div>
                                    {{ $materiales->links('pagination::bootstrap-4') }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="materiales-crear" class="card border-success mb-3">
                        <div class="card-body">
                            <form id="formMaterial" name="formMaterial" class="row g-3" method="POST" action="{{ route('materiales.store') }}">
                                @csrf
                                <div class="col-md-6"><label for="nombre_material"
                                        class="form-label text-success">Nombre</label><input id="nombre_material"
                                        name="nombre" class="form-control" required></div>
                                <div class="col-md-6"><label for="descripcion_material"
                                        class="form-label text-success">Descripción</label>
                                    <textarea id="descripcion_material" name="descripcion" class="form-control"></textarea>
                                </div>
                                <div class="col-md-6"><label for="tipo_id"
                                        class="form-label text-success">Tipo</label><select id="tipo_id" class="form-select" name="tipo_id" required>
                                        <option value="" disabled selected>Selecciona…</option>
                                        @foreach ($tipos as $t)
                                        <option value="{{ $t->id }}">{{ $t->nombre }}</option>
                                        @endforeach
                                    </select></div>
                                <div class="col-md-6"><label for="categoria_id"
                                        class="form-label text-success">Categoría</label><select id="categoria_id" class="form-select" name="categoria_id" required>
                                        <option value="" disabled selected>Selecciona…</option>
                                        @foreach ($categorias as $c)
                                        <option value="{{ $c->id }}">{{ $c->nombre }}</option>
                                        @endforeach
                                    </select></div>

                                <div class="col-md-6"><label for="imagen_url" class="form-label text-success">Imagen
                                        URL</label><input id="imagen_url" name="imagen_url" type="url"
                                        class="form-control"></div>
                                <div class="col-12"><button class="btn btn-success" type="submit">Guardar</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Tipos -->
                    <div id="tipos" class="card border-success mb-3">
                        <div class="card-header bg-success-subtle border-success"><span class="fw-semibold text-success">Tipos de material</span></div>
                        <div class="card-body">
                            <form class="row g-2 mb-3" method="GET" action="{{ url('admin') }}">
                                <div class="col-md-10">
                                    <input type="text" name="buscar_tipo" class="form-control" value="{{ request('buscar_tipo') }}" placeholder="Buscar tipo por nombre...">
                                </div>
                                <div class="col-md-2 d-grid">
                                    <button type="submit" class="btn btn-outline-success">Buscar</button>
                                </div>
                            </form>
                            <form class="row g-2 mb-3" method="POST" action="{{ url('tipos/store') }}">
                                @csrf
                                <div class="col-md-5">
                                    <input type="text" name="nombre" class="form-control" placeholder="Nombre del tipo" required>
                                </div>
                                <div class="col-md-5">
                                    <input type="text" name="descripcion" class="form-control" placeholder="Descripción" required>
                                </div>
                                <div class="col-md-2 d-grid">
                                    <button type="submit" class="btn btn-success">Agregar</button>
                                </div>
                            </form>
                            <div class="table-responsive">
                                <table class="table table-hover m-0">
                                    <thead class="table-success">
                                        <tr>
                                            <th>Nombre</th>
                                            <th>Descripción</th>
                                            <th class="text-end">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($tipos as $tipo)
                                        <tr>
                                            <form method="POST" action="{{ url('tipos/update/' . $tipo->id) }}" class="d-flex">
                                                @csrf
                                                <td><input type="text" name="nombre" value="{{ $tipo->nombre }}" class="form-control form-control-sm" required></td>
                                                <td><input type="text" name="descripcion" value="{{ $tipo->descripcion }}" class="form-control form-control-sm" required></td>
                                                <td class="text-end">
                                                    <button type="submit" class="btn btn-sm btn-success">Guardar</button>
                                                    <form method="POST" action="{{ url('tipos/delete/' . $tipo->id) }}" style="display:inline-block;">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-outline-danger">Eliminar</button>
                                                    </form>
                                                </td>
                                            </form>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                <div class="card-footer d-flex justify-content-between align-items-center">
                                    <span class="small text-success">Mostrando {{ $tipos->count() }} de {{ $tipos->total() }}</span>
                                    <div>
                                        {{ $tipos->links('pagination::bootstrap-4') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Categorías de material -->
                    <div id="categorias-material" class="card border-success mb-3">
                        <div class="card-header bg-success-subtle border-success"><span class="fw-semibold text-success">Categorías de material</span></div>
                        <div class="card-body">
                            <form class="row g-2 mb-3" method="GET" action="{{ url('admin') }}">
                                <div class="col-md-10">
                                    <input type="text" name="buscar_categoria" class="form-control" value="{{ request('buscar_categoria') }}" placeholder="Buscar categoría por nombre...">
                                </div>
                                <div class="col-md-2 d-grid">
                                    <button type="submit" class="btn btn-outline-success">Buscar</button>
                                </div>
                            </form>
                            <form class="row g-2 mb-3" method="POST" action="{{ url('categorias/store') }}">
                                @csrf
                                <div class="col-md-5">
                                    <input type="text" name="nombre" class="form-control" placeholder="Nombre de la categoría" required>
                                </div>
                                <div class="col-md-5">
                                    <input type="text" name="descripcion" class="form-control" placeholder="Descripción" required>
                                </div>
                                <div class="col-md-2 d-grid">
                                    <button type="submit" class="btn btn-success">Agregar</button>
                                </div>
                            </form>
                            <div class="table-responsive">
                                <table class="table table-hover m-0">
                                    <thead class="table-success">
                                        <tr>
                                            <th>Nombre</th>
                                            <th>Descripción</th>
                                            <th class="text-end">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($categorias as $categoria)
                                        <tr>
                                            <form method="POST" action="{{ url('categorias/update/' . $categoria->id) }}" class="d-flex">
                                                @csrf
                                                <td><input type="text" name="nombre" value="{{ $categoria->nombre }}" class="form-control form-control-sm" required></td>
                                                <td><input type="text" name="descripcion" value="{{ $categoria->descripcion }}" class="form-control form-control-sm" required></td>
                                                <td class="text-end">
                                                    <button type="submit" class="btn btn-sm btn-success">Guardar</button>
                                                    <form method="POST" action="{{ url('categorias/delete/' . $categoria->id) }}" style="display:inline-block;">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-outline-danger">Eliminar</button>
                                                    </form>
                                                </td>
                                            </form>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                <div class="card-footer d-flex justify-content-between align-items-center">
                                    <span class="small text-success">Mostrando {{ $categorias->count() }} de {{ $categorias->total() }}</span>
                                    <div>
                                        {{ $categorias->links('pagination::bootstrap-4') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Matriz Centro ⇄ Material -->
                    {{-- Apartado de Matriz Centro ⇄ Material eliminado por solicitud --}}
                </section>

                {{--
                ====== MÓDULO: CONTENIDO (Publicaciones) ocultado temporalmente ======
                <section id="mod-contenido" class="mb-5">
                    ... contenido original del módulo de Publicaciones comentado para desactivar temporalmente ...
                </section>
                --}}

                {{--<!-- ====== MÓDULO: MENSAJERÍA ====== -->
                <section id="mod-mensajeria" class="mb-5">
                    <h2 class="h5 text-success mb-2">Mensajería</h2>
                    <ul class="nav nav-pills mb-3">
                        <li class="nav-item"><a href="#mensajeria-resumen"
                                class="nav-link active bg-success">Resumen</a></li>
                        <li class="nav-item"><a href="#mensajeria-conversaciones" class="nav-link">Conversaciones</a>
                        </li>
                        <li class="nav-item"><a href="#mensajeria-mensajes" class="nav-link">Mensajes</a></li>
                    </ul>

                    <div id="mensajeria-resumen" class="row g-3 mb-3">
                        <div class="col-md-4">
                            <div class="card border-success h-100">
                                <div class="card-body">
                                    <div class="text-success">Abiertas</div>
                                    <div class="display-6 text-success">0</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card border-success h-100">
                                <div class="card-body">
                                    <div class="text-success">Asignadas</div>
                                    <div class="display-6 text-success">0</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card border-success h-100">
                                <div class="card-body">
                                    <div class="text-success">Resueltas</div>
                                    <div class="display-6 text-success">0</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="mensajeria-conversaciones" class="card border-success mb-3">
                        <div class="table-responsive">
                            <table class="table table-hover m-0">
                                <thead class="table-success">
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Usuario</th>
                                        <th>Acción</th>
                                        <th>Entidad</th>
                                        <th>Detalle</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>—</td>
                                        <td>—</td>
                                        <td>—</td>
                                        <td>—</td>
                                        <td class="text-muted">—</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>

                <!--======MÓDULO: SISTEMA======-->
                <section id="mod-sistema" class="mb-5">
                    <h2 class="h5 text-success mb-2">Sistema</h2>
                    <div class="row g-3">
                        <div class="col-12 col-xl-4">
                            <div class="card border-success h-100">
                                <div class="card-header bg-success-subtle border-success"><span
                                        class="fw-semibold text-success">Jobs</span></div>
                                <div class="table-responsive">
                                    <table class="table table-hover m-0">
                                        <thead class="table-success">
                                            <tr>
                                                <th>Job</th>
                                                <th>Estado</th>
                                                <th>Intentos</th>
                                                <th class="text-end">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>—</td>
                                                <td>—</td>
                                                <td>—</td>
                                                <td class="text-end"><a class="btn btn-sm btn-outline-success"
                                                        href="#">Reintentar</a></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-xl-4">
                            <div class="card border-success h-100">
                                <div class="card-header bg-success-subtle border-success"><span
                                        class="fw-semibold text-success">Sesiones activas</span></div>
                                <div class="table-responsive">
                                    <table class="table table-hover m-0">
                                        <thead class="table-success">
                                            <tr>
                                                <th>Usuario</th>
                                                <th>IP</th>
                                                <th>Inicio</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>—</td>
                                                <td>—</td>
                                                <td>—</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-xl-4">
                            <div class="card border-success h-100">
                                <div class="card-header bg-success-subtle border-success"><span
                                        class="fw-semibold text-success">Migrations</span></div>
                                <div class="table-responsive">
                                    <table class="table table-hover m-0">
                                        <thead class="table-success">
                                            <tr>
                                                <th>Batch</th>
                                                <th>Nombre</th>
                                                <th>Fecha</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>—</td>
                                                <td>—</td>
                                                <td>—</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                --}}

                <footer class="text-center small text-success-50 pt-3">© InfoRecicla — Panel de Administración modular
                </footer>
            </main>

        </div>
    </div>
</body>

</html>