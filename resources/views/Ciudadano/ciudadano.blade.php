<x-app-layout>
    <link rel="stylesheet" href="css/Ciudadano/styleCiudadano.css">



    <!-- ===== NAVBAR ===== -->
    <x-navbar-layout />


    <div class="cover"></div>


    <header class="container">
        <div class="profile-card">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-3">
                    <img src="/imagenes/perfil_default.png" alt="Avatar" class="avatar" id="userAvatar">
                    <div>

                        <h1 class="h4 mb-1" id="userName">{{ $usuario->nombre }} {{ $usuario->apellido }}</h1>
                        <div class="text-muted small">
                            <span id="userEmail">{{ $usuario->correo }}</span> ·
                            <span id="userLocalidad"></span>
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-3">

                    <div class="text-center stat">
                        <div class="fw-bold h5 mb-0" id="statFav"></div>
                        <div class="text-muted small">Guardados</div>
                    </div>
                    <button class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#editarPerfilModal">
                        Editar perfil
                    </button>
                </div>
            </div>
        </div>
    </header>


    <div class="container mt-4">
        <ul class="nav nav-tabs" id="citizenTabs" role="tablist">
            <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#guardados"
                    type="button">Guardados</button></li>
            <li class="nav-item" role="presentation">

            </li>
            <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#ajustes"
                    type="button">Ajustes</button></li>
        </ul>

        <div class="tab-content pt-3">


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
                    {{-- Bloque de comentario relacionado con publicaciones oculto temporalmente --}}
                    {{-- <a class="list-group-item list-group-item-action" href="#">
                        <div class="d-flex w-100 justify-content-between">
                            <h6 class="mb-1">Sería útil incluir más publicaciones sobre reciclaje de electrónicos.
                            </h6>
                            <small class="text-muted">Hace 1 semana</small>
                        </div>
                        <small class="text-muted">En: “Guía para separar residuos orgánicos”</small>
                    </a> --}}
                </div>
            </section>


            <section class="tab-pane fade" id="tab-chats" role="tabpanel">
                <div class="row g-3">

                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-header bg-success text-white py-2">
                                Conversaciones
                            </div>
                            <div class="card-body p-2">
                                <div class="list-group chat-sidebar" id="chatThreads">

                                    <button class="list-group-item list-group-item-action active" data-thread-id="1">
                                        <div class="fw-semibold">Punto ECA Suba</div>
                                        <small class="text-muted">Ayer • ¿Aceptan vidrio hoy?</small>
                                    </button>
                                    <button class="list-group-item list-group-item-action" data-thread-id="2">
                                        <div class="fw-semibold">Ecopunto Norte</div>
                                        <small class="text-muted">Hace 3 días • Horarios de atención</small>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="col-lg-8 d-flex flex-column">
                        <div class="card flex-grow-1">
                            <div class="card-header d-flex justify-content-between align-items-center py-2">
                                <div>
                                    <strong id="chatTitle">Punto ECA Suba</strong><br>
                                    <small class="text-muted" id="chatSubtitle">Conversación #1</small>
                                </div>

                                <a href="#" id="chatPointLink" class="btn btn-outline-success btn-sm">Ver
                                    punto</a>
                            </div>
                            <div class="card-body">
                                <div id="chatWindow" class="chat-window">

                                    <div class="msg them">¡Hola! ¿Aceptan vidrio hoy?</div>
                                    <div class="msg me">Hola, sí. Hasta las 5pm.</div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <div class="input-group">
                                    <input type="text" id="chatInput" class="form-control"
                                        placeholder="Escribe un mensaje…">
                                    <button class="btn btn-success" id="chatSend">Enviar</button>
                                </div>


                            </div>
                        </div>
                    </div>
                </div>
            </section>


            <section class="tab-pane fade" id="ajustes">
                <div class="card card-hover">
                    <div class="card-body">
                        <h5 class="card-title mb-3">Preferencias</h5>

                        <form method="POST" action="{{ route('ciudadano.perfil.update') }}">
                            @csrf
                            @method('PATCH')

                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" role="switch" id="prefNoti">
                            <label class="form-check-label" for="prefNoti">Recibir notificaciones de respuestas y
                                novedades</label>
                        </div>


                            <div class="mb-3">
                                <label class="form-label" for="nombre_usuario">Nombre de Usuario</label>
                                <input type="text" class="form-control" id="nombre_usuario" name="nombre_usuario"
                                    value="{{ $usuario->nombre_usuario }}" required>
                            </div>
                        <div class="mb-3">
                            <label class="form-label" for="displayName">Nombre de Usuario</label>
                            <input type="text" class="form-control" id="displayName"
                                placeholder="Como aparecerás al comentar" value="{{ $usuario->nombre_usuario }}">
                        </div>


                        <div class="text-end">
                            <button class="btn btn-success" id="btnGuardarAjustes">Guardar preferencias</button>
                        </div>
                    </div>
                </div>
            </section>

        </div>
    </div>


    <div class="modal fade" id="editarPerfilModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form class="modal-content" id="editarPerfilForm">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">Editar perfil</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">

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

                    <div class="mb-3">
                        <label class="form-label">Nombre</label>
                        <input type="text" class="form-control" id="editNombre" value="{{ $usuario->nombre }}"
                            required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Correo</label>
                        <input type="email" class="form-control" id="editCorreo" value="{{ $usuario->correo }}"
                            required>
                    </div>
                    <div class="mb-3">
                        <label for="nombre_usuario" id="editNombreUsuario" class="form-label">Nombre de
                            usuario</label>
                        <input type="text" class="form-control" id="editNombreUsuario"
                            value="{{ $usuario->nombre_usuario }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Localidad</label>
                        <input type="text" class="form-control" id="editLocalidad" value="">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Foto de perfil (opcional)</label>
                        <input type="file" class="form-control" id="editAvatar" accept="image/*">
                        <div class="form-text">Formatos aceptados: JPG, PNG, WEBP. Máx. 2 MB.</div>
                    </div>
                    <div>
                        <label for="old_password" class="form-label">Contraseña actual</label><input type="password"
                            class="form-control" id="old_password"></label>
                        <label for="password" class="form-label">Contraseña nueva</label><input type="password"
                            class="form-control" id="password"></label>
                        <label for="password_confirmation" class="form-label">Confirmar contraseña</label><input
                            type="password" class="form-control" id="password_confirmation">
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button class="btn btn-success" type="submit">Guardar</button>
                </div>
            </form>
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


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</x-app-layout>

