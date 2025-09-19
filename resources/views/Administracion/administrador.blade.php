<!doctype html>
<html lang="es">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>InfoRecicla • Administración (Módulos)</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
  <div class="container-fluid">
    <div class="row min-vh-100">

      {{-- ================== SIDEBAR ================== --}}
      <aside class="col-12 col-lg-3 col-xl-2 bg-success-subtle border-end border-success-subtle p-3 position-sticky top-0" style="height:100vh; overflow:auto;">
        <div class="d-flex align-items-center gap-2 mb-3">
          <span class="badge rounded-pill text-bg-success">Admin</span>
          <strong class="text-success">InfoRecicla</strong>
        </div>
        <div class="small text-success fw-semibold mb-2">Módulos</div>
        <div class="list-group">
          <a href="#resumen" class="list-group-item list-group-item-action active bg-success border-success">Resumen</a>
          <a href="#mod-usuarios" class="list-group-item list-group-item-action">Usuarios</a>
          <a href="#mod-ecas" class="list-group-item list-group-item-action">Puntos ECA</a>
          <a href="#mod-materiales" class="list-group-item list-group-item-action">Materiales & Catálogos</a>
          <a href="#mod-contenido" class="list-group-item list-group-item-action">Contenido</a>
          <a href="#mod-mensajeria" class="list-group-item list-group-item-action">Mensajería</a>
          <a href="#mod-reportes" class="list-group-item list-group-item-action">Reportes & Auditoría</a>
          <a href="#mod-sistema" class="list-group-item list-group-item-action">Sistema</a>
        </div>
      </aside>

      {{-- Menu --}}
      <main class="col-12 col-lg-9 col-xl-10 p-3 p-lg-4">

        {{-- TOPBAR --}}
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h1 class="h5 text-success m-0">Panel de Administración</h1>
          <span class="badge text-bg-success">Admin</span>
        </div>

        {{-- ERRORES --}}
        @if(session('ok'))
        <div class="alert alert-success">{{ session('ok') }}</div>
        @endif
        @if($errors->any())
        <div class="alert alert-danger">
          <ul class="m-0 ps-3">
            @foreach($errors->all() as $e)
            <li>{{ $e }}</li>
            @endforeach
          </ul>
        </div>
        @endif

        {{--RESUMEN GLOBAL--}}
        <section id="resumen" class="mb-5">
          <h2 class="h5 text-success mb-3">Resumen</h2>
          <div class="row g-3 mb-3">
            <div class="col-sm-6 col-xl-3">
              <div class="card border-success h-100">
                <div class="card-body">
                  <div class="text-success">Usuarios</div>
                  <div class="display-6 fw-semibold text-success">{{ $totalUsuarios ?? 0 }}</div>
                  <div class="small text-success-50">Admins / Gestores / Ciudadanos</div>
                </div>
              </div>
            </div>
            <div class="col-sm-6 col-xl-3">
              <div class="card border-success h-100">
                <div class="card-body">
                  <div class="text-success">Puntos ECA activos</div>
                  <div class="display-6 fw-semibold text-success">{{ $totalEcas ?? 0 }}</div>
                  <div class="small text-success-50">Con inventario</div>
                </div>
              </div>
            </div>
            <div class="col-sm-6 col-xl-3">
              <div class="card border-success h-100">
                <div class="card-body">
                  <div class="text-success">Publicaciones</div>
                  <div class="display-6 fw-semibold text-success">0</div>
                  <div class="small text-success-50">Últimos 30 días</div>
                </div>
              </div>
            </div>
            <div class="col-sm-6 col-xl-3">
              <div class="card border-success h-100">
                <div class="card-body">
                  <div class="text-success">Alertas</div>
                  <div class="display-6 fw-semibold text-success">0</div>
                  <span class="badge text-bg-success">OK</span>
                </div>
              </div>
            </div>
          </div>

          {{-- Actividad reciente --}}
          <div class="row g-3">
            <div class="col-12 col-xl-7">
              <div class="card border-success h-100">
                <div class="card-header bg-success-subtle border-success d-flex justify-content-between align-items-center">
                  <span class="fw-semibold text-success">Actividad reciente</span>
                  <a href="#mod-reportes" class="btn btn-sm btn-outline-success">Ver todo</a>
                </div>
                <div class="table-responsive">
                  <table class="table table-hover align-middle m-0">
                    <thead class="table-success">
                      <tr>
                        <th>Fecha</th>
                        <th>Módulo</th>
                        <th>Acción</th>
                        <th>Usuario</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                        <td>—</td>
                        <td>—</td>
                        <td>—</td>
                        <td>—</td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>

            <div class="col-12 col-xl-5">
              <div class="card border-success h-100">
                <div class="card-header bg-success-subtle border-success"><span class="fw-semibold text-success">Atajos</span></div>
                <div class="card-body">
                  <div class="row g-2">
                    <div class="col-6 col-md-4"><a href="#usuarios-crear" class="btn btn-success w-100">Nuevo usuario</a></div>
                    <div class="col-6 col-md-4"><a href="#ecas-crear" class="btn btn-success w-100">Nuevo ECA</a></div>
                    <div class="col-6 col-md-4"><a href="#materiales-crear" class="btn btn-success w-100">Nuevo material</a></div>
                    <div class="col-6 col-md-4"><a href="#publicaciones-crear" class="btn btn-success w-100">Nueva publicación</a></div>
                    <div class="col-6 col-md-4"><a href="#programaciones" class="btn btn-outline-success w-100">Programar recolección</a></div>
                    <div class="col-6 col-md-4"><a href="#mod-reportes" class="btn btn-outline-success w-100">Exportar datos</a></div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </section>

        {{--MÓDULO: USUARIOS--}}
        <section id="mod-usuarios" class="mb-5">
          <div class="d-flex justify-content-between align-items-center mb-2">
            <h2 class="h5 text-success m-0">Usuarios</h2>
          </div>

          <ul class="nav nav-pills mb-3">
            <li class="nav-item"><a href="#usuarios-resumen" class="nav-link active bg-success">Resumen</a></li>
            <li class="nav-item"><a href="#usuarios-listado" class="nav-link">Listado</a></li>
            <li class="nav-item"><a href="#usuarios-crear" class="nav-link">Crear</a></li>
          </ul>

          {{-- Resumen Usuarios --}}
          <div id="usuarios-resumen" class="mb-3">
            <div class="row g-3">
              <div class="col-md-4">
                <div class="card border-success h-100">
                  <div class="card-body">
                    <div class="text-success">Administradores</div>
                    <div class="display-6 text-success">{{ $totalAdmins ?? 0 }}</div>
                  </div>
                </div>
              </div>
              <div class="col-md-4">
                <div class="card border-success h-100">
                  <div class="card-body">
                    <div class="text-success">Gestores ECA</div>
                    <div class="display-6 text-success">{{ $totalGestores ?? 0 }}</div>
                  </div>
                </div>
              </div>
              <div class="col-md-4">
                <div class="card border-success h-100">
                  <div class="card-body">
                    <div class="text-success">Ciudadanos</div>
                    <div class="display-6 text-success">{{ $totalCiudadanos ?? 0 }}</div>
                  </div>
                </div>
              </div>
            </div>

            {{-- Últimos registrados --}}
            @isset($ultimos)
            <div class="card border-success mt-3">
              <div class="card-header bg-success-subtle border-success"><span class="fw-semibold text-success">Últimos registrados</span></div>
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
                    @forelse($ultimos as $u)
                    <tr>
                      <td>{{ $u->nombre }} {{ $u->apellido }}</td>
                      <td>{{ $u->correo }}</td>
                      <td>{{ $u->rol }}</td>
                      <td><span class="badge text-bg-success">{{ $u->estado }}</span></td>
                    </tr>
                    @empty
                    <tr>
                      <td colspan="4" class="text-center text-muted">Sin registros</td>
                    </tr>
                    @endforelse
                  </tbody>
                </table>
              </div>
            </div>
            @endisset
          </div>

          {{-- Listado Usuarios --}}
          <div id="usuarios-listado" class="mb-3">
            <div class="card border-success mb-2">
              <div class="card-body">
                <form class="row g-2" method="GET" action="{{ route('admin.usuarios.index') }}">
                  <div class="col-12 col-md">
                    <label class="form-label text-success">Buscar</label>
                    <input class="form-control" name="q" value="{{ request('q') }}" placeholder="nombre, correo…">
                  </div>
                  <div class="col-6 col-md-3">
                    <label class="form-label text-success">Rol</label>
                    <select class="form-select" name="rol">
                      <option value="">Todos</option>
                      @foreach(['Administrador','GestorECA','Ciudadano'] as $r)
                      <option value="{{ $r }}" @selected(request('rol')===$r)>{{ $r }}</option>
                      @endforeach
                    </select>
                  </div>
                  <div class="col-6 col-md-3">
                    <label class="form-label text-success">Estado</label>
                    <select class="form-select" name="estado">
                      <option value="">Todos</option>
                      @foreach(['activo','inactivo','bloqueado'] as $e)
                      <option value="{{ $e }}" @selected(request('estado')===$e)>{{ $e }}</option>
                      @endforeach
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
                    @forelse(($usuarios ?? []) as $u)
                    <tr>
                      <td>{{ $u->nombre }} {{ $u->apellido }}</td>
                      <td>{{ $u->correo }}</td>
                      <td>{{ $u->rol }}</td>
                      <td><span class="badge text-bg-success">{{ $u->estado }}</span></td>
                      <td class="text-end">
                        <a class="btn btn-sm btn-outline-success" href="{{ route('admin.usuarios.edit',$u->id) }}">Editar</a>
                        <form action="{{ route('admin.usuarios.destroy',$u->id) }}" method="POST" class="d-inline">
                          @csrf @method('DELETE')
                          <button class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Eliminar?')">Eliminar</button>
                        </form>
                      </td>
                    </tr>
                    @empty
                    <tr>
                      <td colspan="5" class="text-center text-muted">Sin usuarios</td>
                    </tr>
                    @endforelse
                  </tbody>
                </table>
              </div>

              @isset($usuarios)
              <div class="card-footer d-flex justify-content-between align-items-center">
                <span class="small text-success">Mostrando {{ $usuarios->count() }} de {{ $usuarios->total() }}</span>
                {{ $usuarios->onEachSide(1)->links() }}
              </div>
              @endisset
            </div>
          </div>

          {{-- Crear Usuario --}}
          <div id="usuarios-crear">
            <div class="card border-success">
              <div class="card-body">
                <form id="formUsuario" name="formUsuario" class="row g-3" action="{{ route('admin.usuarios.store') }}" method="POST">
                  @csrf
                  <div class="col-md-6">
                    <label for="nombre" class="form-label text-success">Nombre</label>
                    <input id="nombre" name="nombre" type="text" class="form-control" required>
                  </div>
                  <div class="col-md-6">
                    <label for="apellido" class="form-label text-success">Apellido</label>
                    <input id="apellido" name="apellido" type="text" class="form-control" required>
                  </div>
                  <div class="col-md-6">
                    <label for="correo" class="form-label text-success">Correo</label>
                    <input id="correo" name="correo" type="email" class="form-control" required>
                  </div>
                  <div class="col-md-6">
                    <label for="password" class="form-label text-success">Contraseña</label>
                    <input id="password" name="password" type="password" class="form-control" required>
                  </div>
                  <div class="col-md-4">
                    <label for="rol" class="form-label text-success">Rol</label>
                    <select id="rol" name="rol" class="form-select" required>
                      <option value="Administrador">Administrador</option>
                      <option value="GestorECA">GestorECA</option>
                      <option value="Ciudadano">Ciudadano</option>
                    </select>
                  </div>
                  <div class="col-md-4">
                    <label for="estado" class="form-label text-success">Estado</label>
                    <select id="estado" name="estado" class="form-select">
                      <option value="activo">activo</option>
                      <option value="inactivo">inactivo</option>
                      <option value="bloqueado">bloqueado</option>
                    </select>
                  </div>
                  <div class="col-md-4">
                    <label for="genero" class="form-label text-success">Género</label>
                    <select id="genero" name="genero" class="form-select">
                      <option value="Masculino">Masculino</option>
                      <option value="Femenino">Femenino</option>
                      <option value="Otro">Otro</option>
                    </select>
                  </div>
                  <div class="col-md-6">
                    <label for="tipo_documento" class="form-label text-success">Tipo Documento</label>
                    <select id="tipo_documento" name="tipo_documento" class="form-select">
                      <option value="">—</option>
                      <option value="Cédula de Ciudadanía">Cédula de Ciudadanía</option>
                      <option value="Cédula de Extranjería">Cédula de Extranjería</option>
                      <option value="Tarjeta de Identidad">Tarjeta de Identidad</option>
                      <option value="Pasaporte">Pasaporte</option>
                    </select>
                  </div>
                  <div class="col-md-6">
                    <label for="numero_documento" class="form-label text-success">Número Documento</label>
                    <input id="numero_documento" name="numero_documento" class="form-control">
                  </div>
                  <div class="col-md-6">
                    <label for="telefono" class="form-label text-success">Teléfono</label>
                    <input id="telefono" name="telefono" class="form-control">
                  </div>
                  <div class="col-md-6">
                    <label for="fecha_nacimiento" class="form-label text-success">Fecha Nacimiento</label>
                    <input id="fecha_nacimiento" name="fecha_nacimiento" type="date" class="form-control">
                  </div>
                  <div class="col-md-6">
                    <label for="nombre_usuario" class="form-label text-success">Usuario</label>
                    <input id="nombre_usuario" name="nombre_usuario" class="form-control">
                  </div>
                  <div class="col-md-6">
                    <label for="avatar_url" class="form-label text-success">Avatar URL</label>
                    <input id="avatar_url" name="avatar_url" type="url" class="form-control">
                  </div>
                  <div class="col-12">
                    <button class="btn btn-success" type="submit">Guardar</button>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </section>

        {{-- ================== MÓDULO: PUNTOS ECA ================== --}}
        <section id="mod-ecas" class="mb-5">
          <h2 class="h5 text-success mb-2">Puntos ECA</h2>
          <ul class="nav nav-pills mb-3">
            <li class="nav-item"><a href="#ecas-resumen" class="nav-link active bg-success">Resumen</a></li>
            <li class="nav-item"><a href="#ecas-listado" class="nav-link">Listado</a></li>
            <li class="nav-item"><a href="#ecas-crear" class="nav-link">Crear</a></li>
            <li class="nav-item"><a href="#programaciones" class="nav-link">Programaciones</a></li>
          </ul>

          {{-- ... (secciones de resumen/listado) ... --}}

          <div id="ecas-crear" class="mb-3">
            <div class="card border-success">
              <div class="card-body">
                <form id="formEca" name="formEca" class="row g-3" action="{{ route('admin.ecas.store') }}" method="POST">
                  @csrf
                  <div class="col-md-6"><label for="nombre" class="form-label text-success">Nombre</label><input id="nombre" name="nombre" class="form-control" required></div>
                  <div class="col-md-6"><label for="gestor_id" class="form-label text-success">Gestor</label><select id="gestor_id" name="gestor_id" class="form-select"></select></div>
                  <div class="col-12"><label for="descripcion" class="form-label text-success">Descripción</label><textarea id="descripcion" name="descripcion" class="form-control"></textarea></div>
                  <div class="col-12"><label for="direccion" class="form-label text-success">Dirección</label><input id="direccion" name="direccion" class="form-control"></div>
                  <div class="col-md-6"><label for="telefonoPunto" class="form-label text-success">Teléfono</label><input id="telefonoPunto" name="telefonoPunto" class="form-control"></div>
                  <div class="col-md-6"><label for="correoPunto" class="form-label text-success">Correo</label><input id="correoPunto" name="correoPunto" type="email" class="form-control"></div>
                  <div class="col-md-6"><label for="ciudad" class="form-label text-success">Ciudad</label><input id="ciudad" name="ciudad" class="form-control"></div>
                  <div class="col-md-6"><label for="localidad" class="form-label text-success">Localidad</label><input id="localidad" name="localidad" class="form-control"></div>
                  <div class="col-md-6"><label for="latitud" class="form-label text-success">Latitud</label><input id="latitud" name="latitud" type="number" step="0.000001" class="form-control"></div>
                  <div class="col-md-6"><label for="longitud" class="form-label text-success">Longitud</label><input id="longitud" name="longitud" type="number" step="0.000001" class="form-control"></div>
                  <div class="col-md-6"><label for="nit" class="form-label text-success">NIT</label><input id="nit" name="nit" class="form-control"></div>
                  <div class="col-md-6"><label for="horario_atencion" class="form-label text-success">Horario Atención</label><input id="horario_atencion" name="horario_atencion" class="form-control"></div>
                  <div class="col-md-6"><label for="sitio_web" class="form-label text-success">Sitio Web</label><input id="sitio_web" name="sitio_web" type="url" class="form-control"></div>
                  <div class="col-md-6"><label for="logo_url" class="form-label text-success">Logo URL</label><input id="logo_url" name="logo_url" type="url" class="form-control"></div>
                  <div class="col-md-6"><label for="foto_url" class="form-label text-success">Foto URL</label><input id="foto_url" name="foto_url" type="url" class="form-control"></div>
                  <div class="col-md-6">
                    <label for="estado" class="form-label text-success">Estado</label>
                    <select id="estado" name="estado" class="form-select">
                      <option value="activo">activo</option>
                      <option value="inactivo">inactivo</option>
                      <option value="bloqueado">bloqueado</option>
                    </select>
                  </div>
                  <div class="col-12"><button class="btn btn-success" type="submit">Guardar</button></div>
                </form>
              </div>
            </div>
          </div>

        </section>

        <footer class="text-center small text-success-50 pt-3">© InfoRecicla — Panel de Administración modular</footer>
      </main>
    </div>
  </div>
</body>

</html>