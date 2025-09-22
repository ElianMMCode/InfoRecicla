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
                    <a href="#mod-contenido" class="list-group-item list-group-item-action">Contenido</a>
                    <a href="#mod-mensajeria" class="list-group-item list-group-item-action">Mensajería</a>
                    <a href="#mod-reportes" class="list-group-item list-group-item-action">Reportes & Auditoría</a>
                    <a href="#mod-sistema" class="list-group-item list-group-item-action">Sistema</a>
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


                    </div>
                    <div class="row g-3">
                        <div class="col-12 col-xl-7">
                            <div class="card border-success h-100">
                                <div
                                    class="card-header bg-success-subtle border-success d-flex justify-content-between align-items-center">
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
                                        <div class="col-6 col-md-4"><a href="#publicaciones-crear"
                                                class="btn btn-success w-100">Nueva publicación</a></div>
                                        <div class="col-6 col-md-4"><a href="#programaciones"
                                                class="btn btn-outline-success w-100">Programar recolección</a></div>
                                        <div class="col-6 col-md-4"><a href="#mod-reportes"
                                                class="btn btn-outline-success w-100">Exportar datos</a></div>
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
                        <li class="nav-item"><a href="#usuarios-resumen"
                                class="nav-link active bg-success">Resumen</a></li>
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
                                        <div class="display-6 text-success">0</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card border-success h-100">
                                    <div class="card-body">
                                        <div class="text-success">Gestores ECA</div>
                                        <div class="display-6 text-success">0</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card border-success h-100">
                                    <div class="card-body">
                                        <div class="text-success">Ciudadanos</div>
                                        <div class="display-6 text-success">0</div>
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
                                        <tr>
                                            <td>—</td>
                                            <td>—</td>
                                            <td>—</td>
                                            <td><span class="badge text-bg-success">activo</span></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Listado Usuarios -->
                    <div id="usuarios-listado" class="mb-3">
                        <div class="card border-success mb-2">
                            <div class="card-body">
                                <form class="row g-2">
                                    <div class="col-12 col-md">
                                        <label class="form-label text-success">Buscar</label>
                                        <input class="form-control" placeholder="nombre, correo…">
                                    </div>
                                    <div class="col-6 col-md-3">
                                        <label class="form-label text-success">Rol</label>
                                        <select class="form-select">
                                            <option value="">Todos</option>
                                            <option>Administrador</option>
                                            <option>GestorECA</option>
                                            <option>Ciudadano</option>
                                        </select>
                                    </div>
                                    <div class="col-6 col-md-3">
                                        <label class="form-label text-success">Estado</label>
                                        <select class="form-select">
                                            <option value="">Todos</option>
                                            <option>activo</option>
                                            <option>inactivo</option>
                                            <option>bloqueado</option>
                                        </select>
                                    </div>
                                    <div class="col-12 col-md-auto d-flex align-items-end"><a
                                            class="btn btn-outline-success" href="#">Aplicar</a></div>
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
                                        <tr>
                                            <td>—</td>
                                            <td>—</td>
                                            <td>—</td>
                                            <td><span class="badge text-bg-success">—</span></td>
                                            <td class="text-end"><a class="btn btn-sm btn-outline-success"
                                                    href="#usuarios-crear">Editar</a> <a
                                                    class="btn btn-sm btn-outline-danger" href="#">Eliminar</a>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="card-footer d-flex justify-content-between align-items-center"><span
                                    class="small text-success">Mostrando 0 de 0</span>
                                <ul class="pagination pagination-sm m-0">
                                    <li class="page-item disabled"><span class="page-link">Anterior</span></li>
                                    <li class="page-item active"><span
                                            class="page-link bg-success border-success">1</span></li>
                                    <li class="page-item disabled"><span class="page-link">Siguiente</span></li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Crear Usuario -->
                    <div id="usuarios-crear">
                        <div class="card border-success">
                            <div class="card-body">
                                <form id="formUsuario" name="formUsuario" class="row g-3"
                                    action="{{ route('admin.usuarios.create') }}" method="POST">
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
                                        <div class="display-6 text-success">0</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card border-success h-100">
                                    <div class="card-body">
                                        <div class="text-success">Stock total (kg)</div>
                                        <div class="display-6 text-success">0.000</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card border-success h-100">
                                    <div class="card-body">
                                        <div class="text-success">Recolecciones esta semana</div>
                                        <div class="display-6 text-success">0</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="ecas-listado" class="mb-3">
                        <div class="card border-success mb-2">
                            <div class="card-body">
                                <form class="row g-2">
                                    <div class="col-12 col-md"><label
                                            class="form-label text-success">Buscar</label><input class="form-control"
                                            placeholder="nombre, dirección…"></div>
                                    <div class="col-6 col-md-3"><label
                                            class="form-label text-success">Estado</label><select class="form-select">
                                            <option value="">Todos</option>
                                            <option>activo</option>
                                            <option>inactivo</option>
                                            <option>bloqueado</option>
                                        </select></div>
                                    <div class="col-6 col-md-3"><label
                                            class="form-label text-success">Gestor</label><select class="form-select">
                                            <option value="">Cualquiera</option>
                                        </select></div>
                                    <div class="col-12 col-md-auto d-flex align-items-end"><a
                                            class="btn btn-outline-success" href="#">Aplicar</a></div>
                                </form>
                            </div>
                        </div>
                        <div class="card border-success">
                            <div class="table-responsive">
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
                                        <tr>
                                            <td>—</td>
                                            <td>—</td>
                                            <td>—</td>
                                            <td><span class="badge text-bg-success">—</span></td>
                                            <td class="text-end"><a class="btn btn-sm btn-outline-success"
                                                    href="#ecas-crear">Editar</a> <a
                                                    class="btn btn-sm btn-outline-danger" href="#">Eliminar</a>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div id="ecas-crear" class="mb-3">
                        <div class="card border-success">
                            <div class="card-body">
                                <form id="formEca" name="formEca" class="row g-3">
                                    <div class="col-md-6"><label for="nombre"
                                            class="form-label text-success">Nombre</label><input id="nombre"
                                            name="nombre" class="form-control" required></div>
                                    <div class="col-md-6"><label for="gestor_id"
                                            class="form-label text-success">Gestor</label><select id="gestor_id"
                                            name="gestor_id" class="form-select"></select></div>
                                    <div class="col-12"><label for="descripcion"
                                            class="form-label text-success">Descripción</label>
                                        <textarea id="descripcion" name="descripcion" class="form-control"></textarea>
                                    </div>
                                    <div class="col-12"><label for="direccion"
                                            class="form-label text-success">Dirección</label><input id="direccion"
                                            name="direccion" class="form-control"></div>
                                    <div class="col-md-6"><label for="telefonoPunto"
                                            class="form-label text-success">Teléfono</label><input id="telefonoPunto"
                                            name="telefonoPunto" class="form-control"></div>
                                    <div class="col-md-6"><label for="correoPunto"
                                            class="form-label text-success">Correo</label><input id="correoPunto"
                                            name="correoPunto" type="email" class="form-control"></div>
                                    <div class="col-md-6"><label for="ciudad"
                                            class="form-label text-success">Ciudad</label><input id="ciudad"
                                            name="ciudad" class="form-control"></div>
                                    <div class="col-md-6"><label for="localidad"
                                            class="form-label text-success">Localidad</label><input id="localidad"
                                            name="localidad" class="form-control"></div>
                                    <div class="col-md-6"><label for="latitud"
                                            class="form-label text-success">Latitud</label><input id="latitud"
                                            name="latitud" type="number" step="0.000001" class="form-control">
                                    </div>
                                    <div class="col-md-6"><label for="longitud"
                                            class="form-label text-success">Longitud</label><input id="longitud"
                                            name="longitud" type="number" step="0.000001" class="form-control">
                                    </div>
                                    <div class="col-md-6"><label for="nit"
                                            class="form-label text-success">NIT</label><input id="nit"
                                            name="nit" class="form-control"></div>
                                    <div class="col-md-6"><label for="horario_atencion"
                                            class="form-label text-success">Horario Atención</label><input
                                            id="horario_atencion" name="horario_atencion" class="form-control"></div>
                                    <div class="col-md-6"><label for="sitio_web"
                                            class="form-label text-success">Sitio Web</label><input id="sitio_web"
                                            name="sitio_web" type="url" class="form-control"></div>
                                    <div class="col-md-6"><label for="logo_url" class="form-label text-success">Logo
                                            URL</label><input id="logo_url" name="logo_url" type="url"
                                            class="form-control"></div>
                                    <div class="col-md-6"><label for="foto_url" class="form-label text-success">Foto
                                            URL</label><input id="foto_url" name="foto_url" type="url"
                                            class="form-control"></div>
                                    <div class="col-md-6"><label for="estado"
                                            class="form-label text-success">Estado</label><select id="estado"
                                            name="estado" class="form-select">
                                            <option value="activo">activo</option>
                                            <option value="inactivo">inactivo</option>
                                            <option value="bloqueado">bloqueado</option>
                                        </select></div>
                                    <div class="col-12"><button class="btn btn-success"
                                            type="submit">Guardar</button></div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div id="programaciones">
                        <div class="card border-success">
                            <div class="card-header bg-success-subtle border-success"><span
                                    class="fw-semibold text-success">Programaciones de recolección</span></div>
                            <div class="card-body">
                                <form id="formProgramacion" name="formProgramacion" class="row g-3">
                                    <div class="col-md-4"><label for="punto_eca_id"
                                            class="form-label text-success">Punto ECA</label><select id="punto_eca_id"
                                            name="punto_eca_id" class="form-select"></select></div>
                                    <div class="col-md-4"><label for="material_id"
                                            class="form-label text-success">Material</label><select id="material_id"
                                            name="material_id" class="form-select"></select></div>
                                    <div class="col-md-4"><label for="centro_acopio_id"
                                            class="form-label text-success">Centro de acopio</label><select
                                            id="centro_acopio_id" name="centro_acopio_id"
                                            class="form-select"></select></div>
                                    <div class="col-md-4"><label for="fecha"
                                            class="form-label text-success">Fecha</label><input id="fecha"
                                            name="fecha" type="date" class="form-control" required></div>
                                    <div class="col-md-4"><label for="hora"
                                            class="form-label text-success">Hora</label><input id="hora"
                                            name="hora" type="time" class="form-control"></div>
                                    <div class="col-md-4"><label for="frecuencia"
                                            class="form-label text-success">Frecuencia</label><select id="frecuencia"
                                            name="frecuencia" class="form-select">
                                            <option value="manual">manual</option>
                                            <option value="semanal">semanal</option>
                                            <option value="quincenal">quincenal</option>
                                            <option value="mensual">mensual</option>
                                            <option value="unico">unico</option>
                                        </select></div>
                                    <div class="col-12"><label for="notas"
                                            class="form-label text-success">Notas</label>
                                        <textarea id="notas" name="notas" class="form-control" rows="3"></textarea>
                                    </div>
                                    <div class="col-12"><button class="btn btn-success"
                                            type="submit">Guardar</button></div>
                                </form>
                            </div>
                        </div>
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
                        <li class="nav-item"><a href="#matriz-centro-material" class="nav-link">Matriz</a></li>
                    </ul>

                    <div id="materiales-resumen" class="row g-3 mb-3">
                        <div class="col-md-4">
                            <div class="card border-success h-100">
                                <div class="card-body">
                                    <div class="text-success">Materiales</div>
                                    <div class="display-6 text-success">0</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card border-success h-100">
                                <div class="card-body">
                                    <div class="text-success">Categorías</div>
                                    <div class="display-6 text-success">0</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card border-success h-100">
                                <div class="card-body">
                                    <div class="text-success">Tipos</div>
                                    <div class="display-6 text-success">0</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="materiales-listado" class="card border-success mb-3">
                        <div class="card-body">
                            <form class="row g-2">
                                <div class="col-12 col-md"><label class="form-label text-success">Buscar</label><input
                                        class="form-control" placeholder="nombre, descripción…"></div>
                                <div class="col-6 col-md-3"><label
                                        class="form-label text-success">Categoría</label><select class="form-select">
                                        <option value="">Todas</option>
                                    </select></div>
                                <div class="col-6 col-md-3"><label class="form-label text-success">Tipo</label><select
                                        class="form-select">
                                        <option value="">Todos</option>
                                    </select></div>
                                <div class="col-12 col-md-auto d-flex align-items-end"><a
                                        class="btn btn-outline-success" href="#">Aplicar</a></div>
                            </form>
                        </div>
                    </div>
                    <div class="card border-success mb-3">
                        <div class="table-responsive">
                            <table class="table table-hover m-0">
                                <thead class="table-success">
                                    <tr>
                                        <th>Nombre</th>
                                        <th>Categoría</th>
                                        <th>Tipo</th>
                                        <th>Estado</th>
                                        <th class="text-end">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>—</td>
                                        <td>—</td>
                                        <td>—</td>
                                        <td><span class="badge text-bg-success">Activo</span></td>
                                        <td class="text-end"><a class="btn btn-sm btn-outline-success"
                                                href="#materiales-crear">Editar</a> <a
                                                class="btn btn-sm btn-outline-danger" href="#">Eliminar</a></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div id="materiales-crear" class="card border-success mb-3">
                        <div class="card-body">
                            <form id="formMaterial" name="formMaterial" class="row g-3">
                                <div class="col-md-6"><label for="nombre_material"
                                        class="form-label text-success">Nombre</label><input id="nombre_material"
                                        name="nombre" class="form-control" required></div>
                                <div class="col-md-6"><label for="descripcion_material"
                                        class="form-label text-success">Descripción</label>
                                    <textarea id="descripcion_material" name="descripcion" class="form-control"></textarea>
                                </div>
                                <div class="col-md-6"><label for="tipo_id"
                                        class="form-label text-success">Tipo</label><select id="tipo_id"
                                        name="tipo_id" class="form-select"></select></div>
                                <div class="col-md-6"><label for="categoria_id"
                                        class="form-label text-success">Categoría</label><select id="categoria_id"
                                        name="categoria_id" class="form-select"></select></div>
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
                        <div class="card-header bg-success-subtle border-success"><span
                                class="fw-semibold text-success">Tipos de material</span></div>
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
                                    <tr>
                                        <td>—</td>
                                        <td>—</td>
                                        <td class="text-end"><a class="btn btn-sm btn-outline-success"
                                                href="#">Editar</a> <a class="btn btn-sm btn-outline-danger"
                                                href="#">Eliminar</a></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Categorías de material -->
                    <div id="categorias-material" class="card border-success mb-3">
                        <div class="card-header bg-success-subtle border-success"><span
                                class="fw-semibold text-success">Categorías de material</span></div>
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
                                    <tr>
                                        <td>—</td>
                                        <td>—</td>
                                        <td class="text-end"><a class="btn btn-sm btn-outline-success"
                                                href="#">Editar</a> <a class="btn btn-sm btn-outline-danger"
                                                href="#">Eliminar</a></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Matriz Centro ⇄ Material -->
                    <div id="matriz-centro-material" class="card border-success">
                        <div class="card-header bg-success-subtle border-success"><span
                                class="fw-semibold text-success">Matriz Centro ⇄ Material</span></div>
                        <div class="table-responsive">
                            <table class="table table-hover m-0">
                                <thead class="table-success">
                                    <tr>
                                        <th>Centro</th>
                                        <th>Material</th>
                                        <th>Acepta</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>—</td>
                                        <td>—</td>
                                        <td><span class="badge text-bg-secondary">No</span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>

                <!-- ====== MÓDULO: CONTENIDO ====== -->
                <section id="mod-contenido" class="mb-5">
                    <h2 class="h5 text-success mb-2">Contenido</h2>
                    <ul class="nav nav-pills mb-3">
                        <li class="nav-item"><a href="#publicaciones-resumen"
                                class="nav-link active bg-success">Resumen</a></li>
                        <li class="nav-item"><a href="#publicaciones-listado" class="nav-link">Publicaciones</a></li>
                        <li class="nav-item"><a href="#publicaciones-crear" class="nav-link">Crear</a></li>
                        <li class="nav-item"><a href="#multimedia" class="nav-link">Multimedia</a></li>
                        <li class="nav-item"><a href="#comentarios" class="nav-link">Comentarios</a></li>
                    </ul>

                    <div id="publicaciones-resumen" class="row g-3 mb-3">
                        <div class="col-md-4">
                            <div class="card border-success h-100">
                                <div class="card-body">
                                    <div class="text-success">Publicadas</div>
                                    <div class="display-6 text-success">0</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card border-success h-100">
                                <div class="card-body">
                                    <div class="text-success">Borradores</div>
                                    <div class="display-6 text-success">0</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card border-success h-100">
                                <div class="card-body">
                                    <div class="text-success">Comentarios pendientes</div>
                                    <div class="display-6 text-success">0</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="publicaciones-listado" class="card border-success mb-3">
                        <div class="card-body">
                            <form class="row g-2">
                                <div class="col-12 col-md"><label class="form-label text-success">Buscar</label><input
                                        class="form-control" placeholder="título, autor…"></div>
                                <div class="col-6 col-md-3"><label
                                        class="form-label text-success">Categoría</label><select class="form-select">
                                        <option value="">Todas</option>
                                    </select></div>
                                <div class="col-6 col-md-3"><label
                                        class="form-label text-success">Estado</label><select class="form-select">
                                        <option value="">Todos</option>
                                        <option>publicado</option>
                                        <option>borrador</option>
                                        <option>archivado</option>
                                    </select></div>
                                <div class="col-12 col-md-auto d-flex align-items-end"><a
                                        class="btn btn-outline-success" href="#">Aplicar</a></div>
                            </form>
                        </div>
                    </div>
                    <div class="card border-success mb-3">
                        <div class="table-responsive">
                            <table class="table table-hover m-0">
                                <thead class="table-success">
                                    <tr>
                                        <th>Título</th>
                                        <th>Categoría</th>
                                        <th>Estado</th>
                                        <th>Vistas</th>
                                        <th class="text-end">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>—</td>
                                        <td>—</td>
                                        <td><span class="badge text-bg-secondary">borrador</span></td>
                                        <td>0</td>
                                        <td class="text-end"><a class="btn btn-sm btn-outline-success"
                                                href="#publicaciones-crear">Editar</a> <a
                                                class="btn btn-sm btn-outline-danger" href="#">Eliminar</a></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div id="publicaciones-crear" class="card border-success mb-3">
                        <div class="card-body">
                            <form id="formPublicacion" name="formPublicacion" class="row g-3">
                                <div class="col-md-6"><label for="usuario_id"
                                        class="form-label text-success">Autor</label><select id="usuario_id"
                                        name="usuario_id" class="form-select"></select></div>
                                <div class="col-md-6"><label for="categoria_id"
                                        class="form-label text-success">Categoría</label><select id="categoria_id"
                                        name="categoria_id" class="form-select"></select></div>
                                <div class="col-12"><label for="titulo"
                                        class="form-label text-success">Título</label><input id="titulo"
                                        name="titulo" class="form-control" required></div>
                                <div class="col-12"><label for="contenido"
                                        class="form-label text-success">Contenido</label>
                                    <textarea id="contenido" name="contenido" class="form-control" rows="6" required></textarea>
                                </div>
                                <div class="col-md-6"><label for="estado"
                                        class="form-label text-success">Estado</label><select id="estado"
                                        name="estado" class="form-select">
                                        <option value="publicado">publicado</option>
                                        <option value="borrador">borrador</option>
                                        <option value="archivado">archivado</option>
                                    </select></div>
                                <div class="col-12"><button class="btn btn-success" type="submit">Guardar</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Multimedia -->
                    <div id="multimedia" class="card border-success mb-3">
                        <div class="card-header bg-success-subtle border-success"><span
                                class="fw-semibold text-success">Multimedia</span></div>
                        <div class="table-responsive">
                            <table class="table table-hover m-0">
                                <thead class="table-success">
                                    <tr>
                                        <th>Archivo</th>
                                        <th>Tipo</th>
                                        <th>Tamaño</th>
                                        <th>Publicación</th>
                                        <th class="text-end">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>—</td>
                                        <td>—</td>
                                        <td>—</td>
                                        <td>—</td>
                                        <td class="text-end"><a class="btn btn-sm btn-outline-danger"
                                                href="#">Eliminar</a></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Comentarios -->
                    <div id="comentarios" class="card border-success">
                        <div class="card-header bg-success-subtle border-success"><span
                                class="fw-semibold text-success">Comentarios</span></div>
                        <div class="table-responsive">
                            <table class="table table-hover m-0">
                                <thead class="table-success">
                                    <tr>
                                        <th>Publicación</th>
                                        <th>Usuario</th>
                                        <th>Comentario</th>
                                        <th>Estado</th>
                                        <th class="text-end">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>—</td>
                                        <td>—</td>
                                        <td class="text-muted">—</td>
                                        <td><span class="badge text-bg-secondary">pendiente</span></td>
                                        <td class="text-end"><a class="btn btn-sm btn-outline-success"
                                                href="#">Aprobar</a> <a class="btn btn-sm btn-outline-warning"
                                                href="#">Ocultar</a> <a class="btn btn-sm btn-outline-danger"
                                                href="#">Eliminar</a></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>

                <!-- ====== MÓDULO: MENSAJERÍA ====== -->
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
                                        <th>Asunto</th>
                                        <th>Participantes</th>
                                        <th>Último</th>
                                        <th class="text-end">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>—</td>
                                        <td>—</td>
                                        <td>—</td>
                                        <td class="text-end"><a class="btn btn-sm btn-outline-success"
                                                href="#mensajeria-mensajes">Ver</a></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div id="mensajeria-mensajes" class="card border-success">
                        <div class="card-header bg-success-subtle border-success"><span
                                class="fw-semibold text-success">Mensajes</span></div>
                        <div class="card-body text-success-50">Selecciona una conversación.</div>
                    </div>
                </section>

                <!-- ====== MÓDULO: REPORTES & AUDITORÍA ====== -->
                <section id="mod-reportes" class="mb-5">
                    <h2 class="h5 text-success mb-2">Reportes & Auditoría</h2>
                    <div class="card border-success mb-3">
                        <div class="card-header bg-success-subtle border-success"><span
                                class="fw-semibold text-success">Movimientos de inventario</span></div>
                        <div class="card-body text-success-50">(Tabla / Gráfico placeholder sin JS)</div>
                    </div>
                    <div class="card border-success">
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

                <!-- ====== MÓDULO: SISTEMA ====== -->
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

                <footer class="text-center small text-success-50 pt-3">© InfoRecicla — Panel de Administración modular
                </footer>
            </main>

        </div>
    </div>
</body>

</html>