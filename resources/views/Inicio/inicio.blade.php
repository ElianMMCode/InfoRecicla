<x-app-layout>

    <body>

        <!-- NAVBAR -->
        <x-navbar-layout>
        </x-navbar-layout>
        <main>

            <!-- HERO -->
            <section class="py-5">
                <div class="container">
                    <div class="row align-items-center g-4">
                        <div class="col-lg-6">
                            <h1 class="display-5 fw-bold lh-sm">Bienvenido a InfoRecicla</h1>
                            <p class="lead mt-3">
                                Una plataforma para compartir información sobre reciclaje y apoyar la gestión de Puntos
                                ECA.
                            </p>
                            <div class="d-flex gap-3 mt-4">
                                {{-- <a href="/publicaciones" class="btn btn-success btn-lg">Ver publicaciones</a> --}}
                                {{-- <a href="/mapa" class="btn btn-outline-success btn-lg">Mapa de Puntos ECA</a> --}}
                            </div>
                        </div>
                        <div class="col-lg-6 text-center">
                            <img src="/imagenes/ParejaReciclando.png" alt="Personas separando elementos para reciclar"
                                class="img-fluid hero-img">
                        </div>
                    </div>
                </div>
            </section>

            <!-- MÓDULOS DESTACADOS -->
            <section class="bg-success text-white py-5">
                <div class="container">
                    <div class="row g-4">
                        {{-- Publicaciones off --}}
                        {{-- <div class="col-md-6">
                            <a href="/publicaciones" class="text-decoration-none text-white">
                                <div class="card h-100 bg-transparent shadow-lg border-white">
                                    <div class="card-body text-center text-white">
                                        <h2 class="h2 fw-semibold mb-3">Publicaciones</h2>
                                        <img src="/imagenes/publicaciones.png" alt="Ilustración de publicaciones"
                                            class="img-fluid mb-3" style="max-width:210px;">
                                        <p class="mb-0">
                                            Aprende y descubre la actualidad del reciclaje con nosotros.
                                        </p>
                                    </div>
                                </div>
                            </a>
                        </div> --}}
                        <!-- Mapa ECA -->
                        {{-- <div class="col-md-6"> --}}
                        <div class="col-md-12"></div>
                        <a href="/mapa" class="text-decoration-none text-white">
                            <div class="card h-100 bg-transparent shadow-lg border-white">
                                <div class="card-body text-center text-white">
                                    <h2 class="h2 fw-semibold mb-3">Mapa de Puntos ECA</h2>
                                    <img src="/imagenes/mapaECA.png" alt="Mapa de estaciones de reciclaje"
                                        class="img-fluid mb-3" style="max-width:210px;">
                                    <p class="mb-0">
                                        Visualiza estaciones en Bogotá, filtra por proximidad y consulta contacto y
                                        capacidad.
                                    </p>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
                </div>
            </section>

            <section class="py-5 bg-light border-top">
                <div class="container">
                    <div class="text-center mb-4">
                        <h2 class="fw-bold">Crea tu cuenta</h2>
                        <p class="text-muted mb-0">Elige cómo quieres participar en InfoRecicla.</p>
                    </div>

                    <div class="row g-4">
                        <!-- Registro Ciudadano -->
                        <div class="col-md-6">
                            <div class="card h-100 shadow-md">
                                <div class="card-body d-flex flex-column">
                                    <div class="d-flex align-items-center mb-3">
                                        <span class="badge bg-success me-2">Ciudadano</span>
                                        <h3 class="h5 mb-0">Cuenta para Publicar y Comentar</h3>
                                    </div>
                                    <ul class="mb-4 small text-muted ps-3">
                                        {{-- <li>Guarda publicaciones y comenta artículos.</li> --}}
                                        {{-- <li>Sigue temas y recibe notificaciones.</li> --}}
                                        <li>Busca y guarda puntos ECA.</li>
                                        <li>Participa con buenas prácticas de reciclaje.</li>
                                    </ul>
                                    <div class="mt-auto">
                                        <a href="/registro" class="btn btn-outline-success w-100">Registrarme como
                                            Ciudadano</a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Registro Punto ECA -->
                        <div class="col-md-6">
                            <div class="card h-100 shadow-md">
                                <div class="card-body d-flex flex-column">
                                    <div class="d-flex align-items-center mb-3">
                                        <span class="badge bg-success me-2">Punto ECA</span>
                                        <h3 class="h5 mb-0">Registro de Estación y Gestión</h3>
                                    </div>
                                    <ul class="mb-4 small text-muted ps-3">
                                        <li>Publica tu punto en el mapa con datos de contacto.</li>
                                        <li>Controla inventario y capacidad de materiales.</li>
                                        <li>Muestra disponibilidad actual a los ciudadanos.</li>
                                    </ul>
                                    <div class="mt-auto">
                                        <a href="/registro" class="btn btn-outline-success w-100">Registrar Punto
                                            ECA</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

        </main>



        <!-- Modal Login -->
        <div class="modal fade" id="loginModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content shadow">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title">Iniciar Sesión</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Cerrar"></button>
                    </div>

                    <div class="modal-body">
                        <!-- Muestra errores -->
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form id="loginForm" action="{{ route('login.post') }}" method="POST" novalidate>
                            @csrf

                            <div class="mb-3">
                                <label for="loginEmail" class="form-label">Correo electrónico</label>
                                <input type="email" class="form-control" id="loginEmail" name="correo"
                                    {{-- o name="email" si tu campo se llama así --}} value="{{ old('correo') }}" placeholder="ejemplo@correo.com"
                                    required>
                            </div>

                            <div class="mb-3">
                                <label for="loginPassword" class="form-label">Contraseña</label>
                                <input type="password" class="form-control" id="loginPassword" name="password"
                                    placeholder="••••••••" required>
                            </div>

                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="remember" name="remember"
                                        {{ old('remember') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="remember">Recordarme</label>
                                </div>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-success">Ingresar</button>
                            </div>
                        </form>
                    </div>

                    <div class="modal-footer justify-content-between">
                        <small class="text-muted">¿No tienes cuenta?</small>
                        <a href="/registro" class="btn btn-outline-success btn-sm">Registrarse</a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Reabrir el modal si hubo errores de validación --}}
        @if ($errors->any())
            <script>
                document.addEventListener('DOMContentLoaded', () => {
                    const modal = new bootstrap.Modal(document.getElementById('loginModal'));
                    modal.show();
                });
            </script>
        @endif


</x-app-layout>
