{{-- Publicacion off --}}
{{-- <x-app-layout>

    <link rel="stylesheet" href="/css/Publicaciones/stylePublicacion.css">
    <!-- ========= NAVBAR ========= -->
    <x-navbar-layout />

    <main class="container my-4">
        <div class="row g-4">
            <!-- =================== COLUMNA PRINCIPAL =================== -->
            <div class="col-lg-8">

                <!-- ===== ENCABEZADO DE LA PUBLICACIÓN ===== -->
                <header class="mb-3">
                    <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                        <!-- categoría (de la BD) -->
                        <span class="badge badge-cat" id="postCategory" data-category="reciclaje">Reciclaje</span>
                        <span class="post-meta"><small id="postDate">24 ago 2025</small></span>
                        <span class="post-meta">· <small id="postAuthor">por Equipo InfoRecicla</small></span>
                    </div>
                    <h1 class="h3 mb-2" id="postTitle">Guía completa para separar residuos en casa</h1>

                    <!-- Controles: like/dislike -->
                    <div class="d-flex align-items-center gap-3">
                        <button class="btn btn-outline-success btn-sm d-flex align-items-center gap-2" id="btnLike"
                            aria-label="Me gusta">
                            <span class="bi bi-hand-thumbs-up"></span> 👍
                            <span class="badge text-bg-success" id="likeCount">24</span>
                        </button>
                        <button class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-2" id="btnDislike"
                            aria-label="No me gusta">
                            <span class="bi bi-hand-thumbs-down"></span> 👎
                            <span class="badge text-bg-secondary" id="dislikeCount">3</span>
                        </button>
                        <span class="text-muted small">Compartir:</span>
                        <div class="btn-group btn-group-sm" role="group" aria-label="Compartir">
                            <a class="btn btn-outline-secondary" id="shareWhats" target="_blank">WhatsApp</a>
                            <a class="btn btn-outline-secondary" id="shareX" target="_blank">X</a>
                            <a class="btn btn-outline-secondary" id="shareFB" target="_blank">Facebook</a>
                        </div>
                    </div>
                </header>

                <!-- ===== CUERPO DE LA PUBLICACIÓN ===== -->
                <article class="post-body">
                    <!-- Carrusel de imágenes -->
                    <div id="postCarousel" class="carousel slide post-cover mb-4" data-bs-ride="carousel">
                        <div class="carousel-inner">
                            <div class="carousel-item active">
                                <img src="/imagenes/hero1.jpg" class="d-block w-100" alt="Imagen 1">
                            </div>
                            <div class="carousel-item">
                                <img src="/imagenes/hero2.jpg" class="d-block w-100" alt="Imagen 2">
                            </div>
                            <div class="carousel-item">
                                <img src="/imagenes/hero3.jpg" class="d-block w-100" alt="Imagen 3">
                            </div>
                        </div>
                        <button class="carousel-control-prev" type="button" data-bs-target="#postCarousel"
                            data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span><span
                                class="visually-hidden">Anterior</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#postCarousel"
                            data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span><span
                                class="visually-hidden">Siguiente</span>
                        </button>
                    </div>

                    <!-- Texto (desde la BD) -->
                    <div class="mb-4" id="postContent">
                        <p>Separar residuos correctamente es clave para aumentar el aprovechamiento en tu barrio. En
                            esta guía verás tips prácticos para plásticos, papel y cartón, vidrio y metales, además de
                            qué hacer con los residuos orgánicos.</p>
                        <p>Recuerda verificar los horarios y materiales aceptados de tu Punto ECA más cercano antes de
                            llevar tus residuos.</p>
                    </div>

                    <!-- Video (ejemplo YouTube) -->
                    <div class="mb-4">
                        <div class="ratio ratio-16x9 rounded shadow-sm">
                            <iframe src="https://www.youtube.com/embed/ysz5S6PUM-U"
                                title="Video: Separación de residuos" allowfullscreen></iframe>
                        </div>
                    </div>

                    <!-- Documentos y Enlaces -->
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <div class="card card-hover h-100">
                                <div class="card-body">
                                    <h6 class="card-title">Documentos</h6>
                                    <ul class="list-group list-group-flush">
                                        <!-- En producción: href al archivo real -->
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            Protocolo de separación (PDF)
                                            <a class="btn btn-outline-success btn-sm" href="#"
                                                download>Descargar</a>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            Guía rápida (DOCX)
                                            <a class="btn btn-outline-success btn-sm" href="#"
                                                download>Descargar</a>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            Infografía (PNG)
                                            <a class="btn btn-outline-success btn-sm" href="#"
                                                download>Descargar</a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card card-hover h-100">
                                <div class="card-body">
                                    <h6 class="card-title">Enlaces útiles</h6>
                                    <div class="list-group">
                                        <a class="list-group-item list-group-item-action" href="#"
                                            target="_blank">
                                            Localiza tu Punto ECA más cercano
                                        </a>
                                        <a class="list-group-item list-group-item-action" href="#"
                                            target="_blank">
                                            Calendario de recolección por localidad
                                        </a>
                                        <a class="list-group-item list-group-item-action" href="#"
                                            target="_blank">
                                            Normativa local de residuos aprovechables
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Etiquetas (opcional) -->
                    <div class="mb-4">
                        <span class="badge text-bg-light border me-1">#plástico</span>
                        <span class="badge text-bg-light border me-1">#papel</span>
                        <span class="badge text-bg-light border me-1">#vidrio</span>
                    </div>
                </article>

                <hr class="my-4">

                <!-- ===== COMENTARIOS ===== -->
                <section id="comments">
                    <h5 class="mb-3">Comentarios (<span id="commentCount">2</span>)</h5>

                    <!-- Lista de comentarios -->
                    <div class="vstack gap-3 mb-3" id="commentList">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <strong>María G.</strong>
                                    <small class="text-muted">Hace 2 días</small>
                                </div>
                                <p class="mb-0">¡Muy útil! No sabía lo del papel térmico.</p>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <strong>Ricardo P.</strong>
                                    <small class="text-muted">Ayer</small>
                                </div>
                                <p class="mb-0">¿Tienen una lista de precios de metales actualizada?</p>
                            </div>
                        </div>
                    </div>

                    <!-- Formulario para agregar comentario -->
                    <div class="card">
                        <div class="card-body">
                            <form id="commentForm">
                                <div class="mb-3">
                                    <label for="commentText" class="form-label">Agregar un comentario</label>
                                    <textarea id="commentText" class="form-control" rows="3" required placeholder="Escribe tu comentario…"></textarea>
                                    <div class="form-text">Sé respetuoso y evita compartir datos personales.</div>
                                </div>
                                <div class="d-flex justify-content-end">
                                    <button class="btn btn-success" type="submit">Publicar</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </section>

            </div>

            <!-- =================== SIDEBAR DERECHA =================== -->
            <aside class="col-lg-4">
                <div class="sidebar-sticky">

                    <!-- Buscador -->
                    <div class="card mb-3">
                        <div class="card-body">
                            <form id="searchForm" class="input-group">
                                <input type="search" class="form-control" placeholder="Buscar publicaciones…"
                                    aria-label="Buscar">
                                <button class="btn btn-success" type="submit">Buscar</button>
                            </form>
                        </div>
                    </div>

                    <!-- Categorías -->
                    <div class="card mb-3">
                        <div class="card-body">
                            <h6 class="card-title">Categorías</h6>
                            <div class="d-flex flex-wrap gap-2">
                                <a class="badge text-bg-light border text-decoration-none"
                                    href="publicaciones.html#cat-reciclaje">Reciclaje</a>
                                <a class="badge text-bg-light border text-decoration-none"
                                    href="publicaciones.html#cat-medioambiente">Medio Ambiente</a>
                                <a class="badge text-bg-light border text-decoration-none"
                                    href="publicaciones.html#cat-tecnologia">Tecnología Verde</a>
                                <a class="badge text-bg-light border text-decoration-none"
                                    href="publicaciones.html#cat-eventos">Eventos</a>
                            </div>
                        </div>
                    </div>

                    <!-- Relacionadas -->
                    <div class="card">
                        <div class="card-body">
                            <h6 class="card-title">Relacionadas</h6>

                            <!-- item relacionado -->
                            <a class="d-flex gap-3 align-items-center text-decoration-none p-2 rounded-3 card-hover"
                                href="publicacion.html?id=200" title="Separar residuos en oficinas">
                                <img src="/imagenes/cards/reciclaje2.jpg" alt=""
                                    class="rounded flex-shrink-0" width="96" height="64"
                                    style="object-fit:cover;">
                                <div>
                                    <div class="fw-semibold clamp-2">Separar residuos en oficinas</div>
                                    <small class="text-muted">Reciclaje</small>
                                </div>
                            </a>

                            <a class="d-flex gap-3 align-items-center text-decoration-none p-2 rounded-3 card-hover"
                                href="publicacion.html?id=201" title="Reciclaje de vidrio: mitos y verdades">
                                <img src="/imagenes/cards/reciclaje3.jpg" alt=""
                                    class="rounded flex-shrink-0" width="96" height="64"
                                    style="object-fit:cover;">
                                <div>
                                    <div class="fw-semibold clamp-2">Reciclaje de vidrio: mitos y verdades</div>
                                    <small class="text-muted">Reciclaje</small>
                                </div>
                            </a>

                            <a class="d-flex gap-3 align-items-center text-decoration-none p-2 rounded-3 card-hover"
                                href="publicacion.html?id=202" title="Guía de compostaje en casa">
                                <img src="/imagenes/cards/ma2.jpg" alt="" class="rounded flex-shrink-0"
                                    width="96" height="64" style="object-fit:cover;">
                                <div>
                                    <div class="fw-semibold clamp-2">Guía de compostaje en casa</div>
                                    <small class="text-muted">Medio Ambiente</small>
                                </div>
                            </a>

                        </div>
                    </div>

                </div>
            </aside>
        </div>
    </main>
    <script src="/js/Publicaciones/publicacion.js"></script>


 </x-app-layout> --}}
