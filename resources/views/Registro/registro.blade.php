<x-app-layout>

    <body>

        <!-- NAVBAR -->
        <x-navbar-layout />
        <main>
            <!-- Encabezado -->
            <section class="py-5 bg-light border-bottom">
                <div class="container">
                    <div class="text-center">
                        <h1 class="fw-bold">Crea tu cuenta</h1>
                        <p class="text-muted mb-0">Selecciona el tipo de registro para continuar.</p>
                    </div>
                </div>
            </section>

            <!-- Pantalla dividida en dos -->
            <section class="py-5">
                <div class="container">
                    <div class="row g-4">
                        <!-- Tarjeta: Ciudadano -->
                        <div class="col-md-6">
                            <div class="card h-100 shadow-sm">
                                <div class="card-body d-flex flex-column">
                                    <div class="d-flex align-items-center mb-2">
                                        <span class="badge bg-success me-2">Ciudadano</span>
                                        <h2 class="h4 mb-0">Publicar y comentar</h2>
                                    </div>
                                    <p class="text-muted mb-3">Crea tu perfil para participar en la comunidad.</p>
                                    <ul class="small text-muted ps-3 mb-4">
                                        <li>Publica y comenta artículos.</li>
                                        <li>Guarda publicaciones y sigue temas.</li>
                                        <li>Recibe notificaciones relevantes.</li>
                                    </ul>
                                    <div class="mt-auto">
                                        <a href="{{ route('registro', 'ciudadano') }}"
                                            class="btn btn-success w-100">Registrarme como
                                            Ciudadano</a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tarjeta: Punto ECA -->
                        <div class="col-md-6">
                            <div class="card h-100 shadow-sm">
                                <div class="card-body d-flex flex-column">
                                    <div class="d-flex align-items-center mb-2">
                                        <span class="badge bg-success me-2">Punto ECA</span>
                                        <h2 class="h4 mb-0">Registro de estación</h2>
                                    </div>
                                    <p class="text-muted mb-3">Da de alta tu estación para aparecer en el mapa y
                                        gestionar
                                        inventario.</p>
                                    <ul class="small text-muted ps-3 mb-4">
                                        <li>Ubicación, contacto y materiales aceptados.</li>
                                        <li>Capacidad e inventario en tiempo real.</li>
                                        <li>Flujo de aprobación por administrador.</li>
                                    </ul>
                                    <div class="mt-auto">
                                        <a href="{{ route('registro', 'eca') }}"
                                            class="btn btn-outline-success w-100">Registrar Punto
                                            ECA</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Fin tarjetas -->
                    </div>

                    <!-- Aviso de acceso -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="alert alert-info mb-0" role="alert">
                                ¿Ya tienes cuenta? <a href="{{ route('login') }}" class="alert-link">Inicia sesión
                                    aquí</a>.
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </main>



</x-app-layout>
