<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>InfoRecicla — Registro Punto ECA</title>
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .card {
            max-width: 960px;
            margin: 3rem auto;
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 .75rem 1rem rgba(0, 0, 0, .08);
        }

        .card {
            transition: transform .2s ease, box-shadow .2s ease;
        }

        .section-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: #198754;
            margin-bottom: .5rem;
        }
    </style>
</head>

<body class="bg-success">

    <!-- NAVBAR -->
    <nav class=" navbar navbar-expand-lg navbar-dark bg-success">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('inicio') }}">
                <img src="/imagenes/logo.png" alt="Logo InfoRecicla" width="90" height="90" class="rounded">
                <span class="fs-1 fw-semibold">InfoRecicla</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#nav"
                aria-controls="nav" aria-expanded="false" aria-label="Alternar navegación">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div id="nav" class="collapse navbar-collapse">
                <ul class="navbar-nav ms-auto align-items-lg-center align-items-center gap-2">
                    <li class="nav-item"><a class="nav-link" href="{{ route('publicaciones') }}">Publicaciones</a></li>
                    <li class="nav-item"><a class="nav-link" href={{ route('mapa') }}>Mapa ECA</a></li>

                    <li class="nav-item dropdown">
                        <a class="btn btn-light text-success fw-semibold px-3 dropdown-toggle" href="#"
                            data-bs-toggle="dropdown" aria-expanded="true">Acceder</a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="{{ route('login') }}">Iniciar sesión</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item" href="{{ route('registro', 'ciudadano') }}">Registrarse
                                    (Ciudadano)</a>
                            </li>
                            <li><a class="dropdown-item" href="{{ route('registro', 'eca') }}">Registrar Punto ECA</a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <main class="container">
        <div class="card shadow-sm">
            <div class="card-body">
                <h1 class="h3 text-center mb-4">Registrar Punto ECA</h1>

                <!-- ALERTAS (reservado para tu script futuro) -->
                <div id="formAlert" class="alert d-none" role="alert"></div>

                <form method="POST" action="{{ route('registro.eca') }}" id="ecaForm" class="needs-validation"
                    novalidate enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="tipo" value="GestorECA">
                    <!-- FORMULARIO -->
                    <!-- ======= 1) Datos de cuenta (users) ======= -->
                    <p class="section-title">Datos de cuenta</p>
                    <input type="hidden" name="account_type" value="eca">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="correo" class="form-label">Correo electrónico (acceso)</label>
                            <input type="email" class="form-control" id="correo" name="correo"
                                autocomplete="email" required>
                            <div class="invalid-feedback">Ingresa un correo válido.</div>
                        </div>
                        <div class="col-md-3">
                            <label for="password" class="form-label">Contraseña</label>
                            <input type="password" class="form-control" id="password" name="password" minlength="8"
                                maxlength="64" required>
                            <div class="invalid-feedback">Mínimo 8 caracteres.</div>
                        </div>
                        <div class="col-md-3">
                            <label for="pass_confirmation" class="form-label">Confirmar</label>
                            <input type="password" class="form-control" id="pass_confirmation"
                                name="password_confirmation" required>
                            <div class="invalid-feedback">Las contraseñas no coinciden.</div>
                        </div>
                    </div>

                    @if (session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $e)
                                    <li>{{ $e }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <hr class="my-4">

                    <!-- ======= 2) Datos del gestor (user profile / eca_staff.owner) ======= -->
                    <p class="section-title">Datos del gestor (propietario del punto)</p>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="nombre" class="form-label">Nombres</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" required>
                            <div class="invalid-feedback">Campo obligatorio.</div>
                        </div>
                        <div class="col-md-6">
                            <label for="apellido" class="form-label">Apellidos</label>
                            <input type="text" class="form-control" id="apellido" name="apellido" required>
                            <div class="invalid-feedback">Campo obligatorio.</div>
                        </div>
                        <div class="col-md-4">
                            <label for="tipoDocumento" class="form-label">Tipo de documento</label>
                            <select id="tipoDocumento" name="tipoDocumento" class="form-select" required>
                                <option value="" selected disabled>Selecciona...</option>
                                <option value="Cédula de Ciudadanía">Cédula de Ciudadanía</option>
                                <option value="Cédula de Extranjería">Cédula de Extranjería</option>
                                <option value="Tarjeta de Identidad">Tarjeta de Identidad</option>
                                <option value="Pasaporte">Pasaporte</option>
                            </select>
                            <div class="invalid-feedback">Selecciona un tipo de documento.</div>
                        </div>
                        <div class="col-md-4">
                            <label for="numeroDocumento" class="form-label">Número de documento</label>
                            <input type="text" class="form-control" id="numeroDocumento" name="numeroDocumento"
                                minlength="5" maxlength="20" required>
                            <div class="invalid-feedback">Ingresa un número válido.</div>
                        </div>

                        <hr class="my-4">

                        <!-- ======= 3) Datos del Punto ECA (eca_points) ======= -->
                        <p class="section-title">Información de la estación</p>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="nombrePunto" class="form-label">Nombre del Punto ECA</label>
                                <input type="text" class="form-control" id="nombrePunto" name="nombrePunto"
                                    required>
                                <div class="invalid-feedback">Campo obligatorio.</div>
                            </div>
                            <div class="col-md-3">
                                <label for="nit" class="form-label">NIT (opcional)</label>
                                <input type="text" class="form-control" id="nit" name="nit"
                                    maxlength="20" placeholder="Si aplica">
                            </div>
                        </div>

                        <div class="row g-3 mt-1">
                            <div class="col-md-6">
                                <label for="horarioAtencion" class="form-label">Horario de atención (opcional)</label>
                                <input type="text" class="form-control" id="horarioAtencion"
                                    name="horarioAtencion" placeholder="Lun-Vie 8:00–17:00, Sáb 9:00–13:00">
                            </div>
                        </div>

                        <div class="row g-3 mt-1">
                            <div class="col-md-6">
                                <label for="correoPunto" class="form-label">Correo de contacto del punto</label>
                                <input type="email" class="form-control" id="correoPunto" name="correoPunto"
                                    placeholder="Puede ser distinto al de acceso" required>
                                <div class="invalid-feedback">Ingresa un correo válido.</div>
                            </div>
                            <div class="col-md-6">
                                <label for="telefonoPunto" class="form-label">Teléfono de contacto del punto</label>
                                <input type="tel" class="form-control" id="telefonoPunto" name="telefonoPunto"
                                    inputmode="tel" pattern="^[0-9\s()+-]{7,20}$" required>
                                <div class="invalid-feedback">Ingresa un teléfono válido.</div>
                            </div>
                        </div>

                        <div class="row g-3 mt-1">
                            <div class="col-md-6">
                                <label for="direccionPunto" class="form-label">Dirección del punto</label>
                                <input type="text" class="form-control" id="direccionPunto" name="direccionPunto"
                                    required>
                                <div class="invalid-feedback">Campo obligatorio.</div>
                            </div>
                            <div class="col-md-3">
                                <label for="ciudad" class="form-label">Ciudad</label>
                                <select id="ciudad" name="ciudad" class="form-select" required>
                                    <option value="" disabled selected>Selecciona...</option>
                                    <option value="Bogotá">Bogotá</option>
                                </select>
                                <div class="invalid-feedback">Selecciona una ciudad.</div>
                            </div>
                            <div class="col-md-3">
                                <label for="localidadPunto" class="form-label">Localidad</label>
                                <select id="localidadPunto" name="localidadPunto" class="form-select" required>
                                    <option value="" disabled selected>Selecciona localidad...</option>
                                    <option value="Usaquén">Usaquén</option>
                                    <option value="Chapinero">Chapinero</option>
                                    <option value="Santa Fe">Santa Fe</option>
                                    <option value="San Cristóbal">San Cristóbal</option>
                                    <option value="Usme">Usme</option>
                                    <option value="Tunjuelito">Tunjuelito</option>
                                    <option value="Bosa">Bosa</option>
                                    <option value="Kennedy">Kennedy</option>
                                    <option value="Fontibón">Fontibón</option>
                                    <option value="Engativá">Engativá</option>
                                    <option value="Suba">Suba</option>
                                    <option value="Barrios Unidos">Barrios Unidos</option>
                                    <option value="Teusaquillo">Teusaquillo</option>
                                    <option value="Los Mártires">Los Mártires</option>
                                    <option value="Antonio Nariño">Antonio Nariño</option>
                                    <option value="Puente Aranda">Puente Aranda</option>
                                    <option value="La Candelaria">La Candelaria</option>
                                    <option value="Rafael Uribe">Rafael Uribe</option>
                                    <option value="Ciudad Bolívar">Ciudad Bolívar</option>
                                    <option value="Sumapaz">Sumapaz</option>
                                </select>
                                <div class="invalid-feedback">Selecciona una localidad.</div>
                            </div>
                        </div>

                        <div class="mt-3">
                            <button type="button" id="geoBtn" class="btn btn-outline-secondary">Obtener mi
                                ubicación</button>
                            <div id="locationDisplay" class="form-text mt-2">Presiona para otorgar permiso de
                                ubicación.
                            </div>
                            <!-- Campos lat/lng (para tu script de geoloc) -->
                            <input type="hidden" id="latitud" name="latitud">
                            <input type="hidden" id="longitud" name="longitud">
                        </div>

                        <div class="row g-3 mt-1">

                            <div class="col-md-6">
                                <label for="logo" class="form-label">Logo del punto (opcional)</label>
                                <input type="file" class="form-control" id="logo" name="logo"
                                    accept="image/*">
                            </div>
                            <div class="col-md-6">
                                <label for="foto" class="form-label">Foto del punto (opcional)</label>
                                <input type="file" class="form-control" id="foto" name="foto"
                                    accept="image/*">
                            </div>
                            <div class="col-md-6">
                                <label for="sitioWeb" class="form-label">Sitio web (opcional)</label>
                                <input type="url" class="form-control" id="sitioWeb" name="sitioWeb"
                                    placeholder="https://">
                            </div>

                        </div>

                        <div class="form-check mt-4">
                            <input class="form-check-input" type="checkbox" value="1" id="mostrarMapa"
                                name="mostrarMapa" required>
                            <label class="form-check-label" for="mostrarMapa">
                                Mostrar el punto en el mapa público cuando sea aprobado.
                            </label>
                        </div>

                        <div class="form-check mt-2">
                            <input class="form-check-input" type="checkbox" value="1" id="recibeNotificaciones"
                                name="recibeNotificaciones" required>
                            <label class="form-check-label" for="recibeNotificaciones">
                                Deseo recibir notificaciones (aprobaciones, comentarios en publicaciones del punto,
                                etc.).
                            </label>
                        </div>

                        <div class="form-check mt-4">
                            <input class="form-check-input" type="checkbox" value="1" id="terms" required>
                            <label class="form-check-label" for="terms">
                                Acepto los términos y condiciones y el tratamiento de datos.
                            </label>
                            <div class="invalid-feedback">Debes aceptar los términos para continuar.</div>
                        </div>

                        <div class="d-grid mt-4">
                            <button type="submit" class="btn btn-success btn-lg">Registrar Punto ECA</button>
                        </div>

                        <p class="text-center mt-3 mb-0">
                            <small>¿Ya tienes cuenta? <a href="{{ route('login') }}">Inicia sesión</a></small>
                        </p>
                </form>
            </div>
        </div>
    </main>

    <footer class="bg-light border-top py-4">
        <div class="container">
            <ul class="nav justify-content-center gap-3">
                <li class="nav-item"><a class="nav-link text-muted" href="#">Acerca de</a></li>
                <li class="nav-item"><a class="nav-link text-muted" href="#">Soporte</a></li>
                <li class="nav-item"><a class="nav-link text-muted" href="#">Contacto</a></li>
            </ul>
            <p class="text-center text-muted mb-0 mt-2 small">&copy; <span id="year"></span> InfoRecicla</p>
        </div>
    </footer>

    <!-- Bootstrap Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('year').textContent = new Date().getFullYear();
    </script>
</body>

</html>
