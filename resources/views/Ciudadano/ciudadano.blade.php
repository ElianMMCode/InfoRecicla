<x-app-layout>
    <link rel="stylesheet" href="css/Ciudadano/styleCiudadano.css">



    <!-- ===== NAVBAR ===== -->
    <x-navbar-layout />

    <!-- ===== COVER ===== -->
    <div class="cover"></div>

    <!-- ===== PROFILE HEADER ===== -->
    <header class="container">
        <div class="profile-card">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-3">
                    <img src="/imagenes/perfil_default.png" alt="Avatar" class="avatar" id="userAvatar">
                    <div>
                        <!-- Estos datos vendrán de la BD -->
                        <h1 class="h4 mb-1" id="userName">Juan Rodríguez</h1>
                        <div class="text-muted small">
                            <span id="userEmail">juan.rodri@email.com</span> ·
                            <span id="userLocalidad">Engativá, Bogotá</span>
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-3">
                    <!-- Stats rápidas (contadores desde BD) -->
                    <div class="text-center stat">
                        <div class="fw-bold h5 mb-0" id="statFav">8</div>
                        <div class="text-muted small">Guardados</div>
                    </div>
                    <button class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#editarPerfilModal">
                        Editar perfil
                    </button>
                </div>
            </div>
        </div>
    </header>

    <!-- ===== TABS ===== -->
    <div class="container mt-4">
        <ul class="nav nav-tabs" id="citizenTabs" role="tablist">
            <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#guardados"
                    type="button">Guardados</button></li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-chats" type="button"
                    role="tab">Chats</button>
            </li>
            <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#ajustes"
                    type="button">Ajustes</button></li>
        </ul>

        <div class="tab-content pt-3">

            <!-- ===== TAB: GUARDADOS (Publicaciones ocultas temporalmente) ===== -->
            <section class="tab-pane fade" id="guardados">
                <div class="row g-4">
                    <div class="col-md-6">
                        <h5 class="mb-3">Puntos ECA Guardados</h5>
                        <div class="list-group">
                            <!-- Items desde BD -->
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

            <!-- ===== TAB: COMENTARIOS (referencias a publicaciones ocultas) ===== -->
            <section class="tab-pane fade" id="comentarios">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h5 class="mb-0">Mis Comentarios</h5>
                    <!-- Filtro ejemplo; a futuro puede venir de la BD -->
                    <select class="form-select form-select-sm" style="width:auto">
                        <option value="todos">Todos</option>
                        {{-- <option value="publicaciones">En publicaciones</option> --}}
                        <option value="puntos">En puntos ECA</option>
                    </select>
                </div>

                <div class="list-group">
                    <!-- Items desde BD -->
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
                            <h6 class="mb-1">Sería útil incluir más publicaciones sobre reciclaje de electrónicos.</h6>
                            <small class="text-muted">Hace 1 semana</small>
                        </div>
                        <small class="text-muted">En: “Guía para separar residuos orgánicos”</small>
                    </a> --}}
                </div>
            </section>

            <!-- ====== CHATS ====== -->
            <section class="tab-pane fade" id="tab-chats" role="tabpanel">
                <div class="row g-3">
                    <!-- Lista de conversaciones con Puntos ECA -->
                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-header bg-success text-white py-2">
                                Conversaciones
                            </div>
                            <div class="card-body p-2">
                                <div class="list-group chat-sidebar" id="chatThreads">
                                    <!-- TODO: Renderizar con datos de BD (GET /api/conversations?role=citizen) -->
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

                    <!-- Ventana de chat -->
                    <div class="col-lg-8 d-flex flex-column">
                        <div class="card flex-grow-1">
                            <div class="card-header d-flex justify-content-between align-items-center py-2">
                                <div>
                                    <strong id="chatTitle">Punto ECA Suba</strong><br>
                                    <small class="text-muted" id="chatSubtitle">Conversación #1</small>
                                </div>
                                <!-- (Opcional) botón para ver el perfil del punto -->
                                <a href="#" id="chatPointLink" class="btn btn-outline-success btn-sm">Ver
                                    punto</a>
                            </div>
                            <div class="card-body">
                                <div id="chatWindow" class="chat-window">
                                    <!-- TODO: Renderizar mensajes (GET /api/conversations/{id}/messages) -->
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
                                <!-- (Opcional) Adjuntos a futuro
                <div class="form-text">Puedes adjuntar imágenes o documentos (próximamente).</div>
                -->
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- ===== TAB: AJUSTES (preferencias del usuario) ===== -->
            <section class="tab-pane fade" id="ajustes">
                <div class="card card-hover">
                    <div class="card-body">
                        <h5 class="card-title mb-3">Preferencias</h5>

                        <!-- Notificaciones (se guarda en citizen_profiles.receive_notifications) -->
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" role="switch" id="prefNoti">
                            <label class="form-check-label" for="prefNoti">Recibir notificaciones de respuestas y
                                novedades</label>
                        </div>

                        <!-- Nombre visible (display_name) -->
                        <div class="mb-3">
                            <label class="form-label" for="displayName">Nombre visible</label>
                            <input type="text" class="form-control" id="displayName"
                                placeholder="Como aparecerás al comentar">
                        </div>

                        <!-- Botón de guardado -->
                        <div class="text-end">
                            <button class="btn btn-success" id="btnGuardarAjustes">Guardar preferencias</button>
                        </div>
                    </div>
                </div>
            </section>

        </div>
    </div>

    <!-- ===== MODAL: Editar Perfil ===== -->
    <div class="modal fade" id="editarPerfilModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form class="modal-content" id="editarPerfilForm">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">Editar perfil</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <!-- A futuro: precargar desde BD -->
                    <div class="mb-3">
                        <label class="form-label">Nombre</label>
                        <input type="text" class="form-control" id="editNombre" value="Juan Rodríguez" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Correo</label>
                        <input type="email" class="form-control" id="editCorreo" value="juan.rodri@email.com"
                            required>
                    </div>
                    <div class="mb-3">
                        <label for="nombre_usuario" id="editNombreUsuario" class="form-label">Nombre de
                            usuario</label>
                        <input type="text" class="form-control" id="editNombreUsuario" value="juanrodriguez"
                            required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Localidad</label>
                        <input type="text" class="form-control" id="editLocalidad" value="Engativá">
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

    <!-- ===== TOAST: feedback corto ===== -->
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

    <!-- ===== Bootstrap + JS mínimo ===== -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // ========= Utilidad: toast =========
        const toast = new bootstrap.Toast(document.getElementById('toastOK'), {
            delay: 1800
        });
        const showToast = (msg) => {
            document.getElementById('toastText').textContent = msg;
            toast.show();
        };

        // ========= Modal: guardar perfil (simulado) =========
        document.getElementById('editarPerfilForm').addEventListener('submit', (e) => {
            e.preventDefault();

            // (A futuro) Validaciones + envío a tu API (PATCH /api/citizens/profile)
            const nombre = document.getElementById('editNombre').value.trim();
            const correo = document.getElementById('editCorreo').value.trim();
            const loc = document.getElementById('editLocalidad').value.trim();
            // const avatarFile = document.getElementById('editAvatar').files[0];

            // Reflejar en encabezado (optimistic UI)
            document.getElementById('userName').textContent = nombre || '—';
            document.getElementById('userEmail').textContent = correo || '—';
            document.getElementById('userLocalidad').textContent = loc ? (loc + ', Bogotá') : '—';

            // Cerrar modal y toast
            bootstrap.Modal.getInstance(document.getElementById('editarPerfilModal')).hide();
            showToast('Perfil actualizado');
        });

        // ========= Ajustes: guardar preferencias (simulado) =========
        document.getElementById('btnGuardarAjustes').addEventListener('click', () => {
            const prefs = {
                receive_notifications: document.getElementById('prefNoti').checked ? 1 : 0,
                display_name: document.getElementById('displayName').value.trim()
            };
            // (A futuro) POST/PATCH /api/citizens/preferences
            console.log('Preferencias:', prefs);
            showToast('Preferencias guardadas');
        });

        // ========= Carga inicial simulada desde "BD" =========
        // Aquí irían tus fetch reales para rellenar todo con datos del usuario
        (function seedFromDB() {
            // Ejemplo: marcar preferencia desde perfil
            document.getElementById('prefNoti').checked = true;
            document.getElementById('displayName').value = 'JuanR';
            // TODO: fetch publicaciones/guardados/comentarios y renderizar
        })();
    </script>

</x-app-layout>
