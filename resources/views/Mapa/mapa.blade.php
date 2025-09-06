<!-- Leaflet (sin llave) -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<link rel="stylesheet" href="/css/Mapa/styleMapa.css') }}">

<x-app-layout>
    <!-- Leaflet (sin llave) -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="css/Mapa/styleMapa.css">

    <!-- NAVBAR -->
    <x-navbar-layout>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#nav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div id="nav" class="collapse navbar-collapse">
            <ul class="navbar-nav ms-auto align-items-lg-center gap-2">
                <li class="nav-item"><a class="nav-link"
                        href="../Publicaciones/publicaciones/publicaciones.html">Publicaciones</a></li>
                <li class="nav-item"><a class="nav-link active" href="#">Mapa ECA</a></li>
                <li class="nav-item"><a class="btn btn-light text-success" href="../Inicio/index.html">Inicio</a>
                </li>
            </ul>
        </div>
    </x-navbar-layout>

    <!-- CONTENIDO -->
    <div class="container-fluid py-3">
        <div class="row layout g-3">
            <!-- Lista vertical (10%) -->
            <aside class="col-lg-3 col-md-4 list-col">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h5 class="mb-0">Puntos ECA</h5>
                    <button class="btn btn-sm btn-outline-success" id="btnCentrar">Centrar Bogotá</button>
                </div>

                <!-- Buscador simple (cliente) -->
                <div class="input-group mb-3">
                    <span class="input-group-text">🔎</span>
                    <input type="search" id="filtro" class="form-control" placeholder="Buscar por nombre/localidad…">
                </div>

                <div id="lista" class="vstack gap-2">
                    <!-- Aquí se pintan las cards -->
                </div>
            </aside>

            <!-- Mapa (90%) -->
            <section class="col-lg-9 col-md-8 map-col">
                <div id="map" class="shadow-sm"></div>
            </section>
        </div>
    </div>

    <!-- MODAL: detalles del punto + mensaje -->
    <div class="modal fade" id="modalPunto" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="modalTitle">Punto ECA</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-5">
                            <img id="modalImg" src="../images/eca-default.png" alt="Foto del punto"
                                class="img-fluid rounded">
                            <div class="mt-2">
                                <span class="badge text-bg-light border tag" id="modalCategoria">Reciclaje</span>
                                <span class="badge text-bg-light border tag" id="modalLocalidad">Suba</span>
                            </div>
                        </div>
                        <div class="col-md-7">
                            <dl class="row mb-0">
                                <dt class="col-sm-4">Dirección</dt>
                                <dd class="col-sm-8" id="modalDireccion">—</dd>
                                <dt class="col-sm-4">Horario</dt>
                                <dd class="col-sm-8" id="modalHorario">—</dd>
                                <dt class="col-sm-4">Contacto</dt>
                                <dd class="col-sm-8">
                                    <div id="modalContacto">—</div>
                                    <div id="modalCorreo" class="text-muted small">—</div>
                                </dd>
                                <dt class="col-sm-4">Teléfono</dt>
                                <dd class="col-sm-8" id="modalTelefono">—</dd>
                                <dt class="col-sm-4">Materiales</dt>
                                <dd class="col-sm-8" id="modalMateriales">—</dd>
                                <dt class="col-sm-4">Web</dt>
                                <dd class="col-sm-8"><a id="modalWeb" href="#" target="_blank" rel="noopener">—</a></dd>
                            </dl>
                        </div>
                    </div>

                    <hr>
                    <h6 class="mb-2">Enviar mensaje al punto</h6>
                    <form id="formMsg">
                        <div class="row g-2">
                            <div class="col-md-6">
                                <label class="form-label">Tu nombre</label>
                                <input type="text" class="form-control" id="msgNombre" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Tu correo</label>
                                <input type="email" class="form-control" id="msgCorreo" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Mensaje</label>
                                <textarea class="form-control" id="msgTexto" rows="3" required
                                    placeholder="Hola, ¿aceptan vidrio hoy?"></textarea>
                            </div>
                        </div>
                        <div class="text-end mt-3">
                            <button class="btn btn-success" type="submit">Enviar</button>
                        </div>
                        <div id="msgOk" class="text-success small mt-2 d-none">Mensaje enviado (simulado).</div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap + Leaflet -->

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <script src="js/Mapa/mapa.js"></script>

</x-app-layout>