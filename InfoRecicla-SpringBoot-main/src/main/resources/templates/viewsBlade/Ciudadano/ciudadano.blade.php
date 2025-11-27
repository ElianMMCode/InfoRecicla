<x-app-layout>
    <link rel="stylesheet" href="css/Ciudadano/styleCiudadano.css">

    <nav class="navbar navbar-expand-lg navbar-dark bg-success">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2" href="/">
                <img src="/imagenes/logo.png" alt="Logo InfoRecicla" width="90" height="90" class="rounded">
                <span class="fs-1 fw-semibold">InfoRecicla</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#nav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div id="nav" class="collapse navbar-collapse">
                <ul class="navbar-nav ms-auto align-items-lg-center gap-2">
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
        </div>
    </nav>

    <div class="cover"></div>

    <header class="container">
        <div class="profile-card">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-3">
                    <img src="/imagenes/perfil_default.png" alt="Avatar" class="avatar" id="userAvatar">
                    <div>
                        <h1 class="h4 mb-1" id="userName">{{ $usuario->nombre_usuario }}</h1>
                        <div class="text-muted small">
                            <span id="userNameAndLastname">{{ $usuario->nombre }} {{ $usuario->apellido }}</span>
                            <br>
                            <span id="userEmail">{{ $usuario->correo }}</span>
                            <span id="userLocalidad"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <div class="container mt-4">
        <ul class="nav nav-tabs" id="citizenTabs" role="tablist">
            {{-- <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#guardados"
                    type="button">Guardados</button></li>
            <li class="nav-item" role="presentation"> --}}

            </li>
            <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#ajustes"
                    type="button">Ajustes</button></li>
        </ul>

        <div class="tab-content pt-3">
            <!-- ===== GUARDADOS ===== -->
            <section class="tab-pane fade" id="guardados">
                <div class="row g-4">
                    <div class="col-md-6">
                        <h5 class="mb-3">Puntos ECA Guardados</h5>
                        <div class="list-group">
                            <a href="#"
                                class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                Recicladora y Chatarrería JJ — Cra 7 #109-06 Sur
                                <span class="badge bg-success rounded-pill">Ver</span>
                            </a>
                            <a href="#"
                                class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                Ecopunto Norte — Calle 170 con Autopista
                                <span class="badge bg-success rounded-pill">Ver</span>
                            </a>
                        </div>
                    </div>
                </div>
            </section>

            <!-- ===== COMENTARIOS ===== -->
            <section class="tab-pane fade" id="comentarios">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h5 class="mb-0">Mis Comentarios</h5>
                    <select class="form-select form-select-sm" style="width:auto">
                        <option value="todos">Todos</option>
                        {{-- <option value="publicaciones">En publicaciones</option> --}}
                        <option value="puntos">En puntos ECA</option>
                    </select>
                </div>

                <div class="list-group">
                    <a class="list-group-item list-group-item-action" href="#">
                        <div class="d-flex w-100 justify-content-between">
                            <h6 class="mb-1">Excelente iniciativa en el nuevo punto ECA de Suba.</h6>
                            <small class="text-muted">Hace 3 días</small>
                        </div>
                        <small class="text-muted">En: “Punto ECA Suba reabrió”</small>
                    </a>
                    {{-- Publicaciones off --}}
                    {{-- <a class="list-group-item list-group-item-action" href="#">
                        <div class="d-flex w-100 justify-content-between">
                            <h6 class="mb-1">Sería útil incluir más publicaciones sobre reciclaje de electrónicos.</h6>
                            <small class="text-muted">Hace 1 semana</small>
                        </div>
                        <small class="text-muted">En: “Guía para separar residuos orgánicos”</small>
                    </a> --}}
                </div>
            </section>

            <!-- ===== AJUSTES (FORM REAL) ===== -->
            <section class="tab-pane fade" id="ajustes">
                <div class="card card-hover">
                    <div class="card-body">
                        <h5 class="card-title mb-3">Preferencias</h5>

                        <form method="POST" action="{{ route('ciudadano.perfil.update') }}">
                            @csrf
                            @method('PATCH')

                            <!-- Notificaciones (opcional; si no las guardas, quita el name) -->
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" role="switch" id="prefNoti"
                                    name="recibe_notificaciones" value="1"
                                    {{ $usuario->recibe_notificaciones ? 'checked' : '' }}>
                                <label class="form-check-label" for="prefNoti">
                                    Recibir notificaciones de publicaciones, respuestas y novedades
                                </label>
                            </div>
                            <h5 class="card-title mb-3">Actualizacion de Datos</h5>

                            <div class="mb-3">
                                <label class="form-label" for="nombre_usuario">Nombre de Usuario</label>
                                <input type="text" class="form-control" id="nombre_usuario" name="nombre_usuario"
                                    value="{{ $usuario->nombre_usuario }}" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Nombre</label>
                                <input type="text" class="form-control" id="editNombre" name="nombre"
                                    value="{{ $usuario->nombre }}" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Correo</label>
                                <input type="email" class="form-control" id="editCorreo" name="correo"
                                    value="{{ $usuario->correo }}" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Localidad</label>
                                <input type="text" class="form-control" id="editLocalidad" name="localidad"
                                    value="{{ $usuario->localidad }}">
                            </div>
                            <div>
                                <label for="old_password" class="form-label">Contraseña actual</label>
                                <input type="password" class="form-control" id="old_password" name="old_password">

                                <label for="password" class="form-label">Contraseña nueva</label>
                                <input type="password" class="form-control" id="password" name="password">

                                <label for="password_confirmation" class="form-label">Confirmar contraseña</label>
                                <input type="password" class="form-control" id="password_confirmation"
                                    name="password_confirmation">
                                <br>
                                <div class="d-flex justify-content-end gap-2">
                                    <button class="btn btn-outline-secondary" type="button">Cancelar</button>
                                    <button class="btn btn-success" type="submit">Guardar</button>
                                </div>
                            </div>
                        </form>

                    </div>
                </div>
            </section>
        </div>
    </div>

    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1080;">
        <div id="toastOK" class="toast align-items-center text-white bg-success border-0" role="alert"
            aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body" id="toastText">Acción realizada.</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                    aria-label="Cerrar"></button>
            </div>
        </div>
    </div>
    {{-- comentario --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</x-app-layout>
