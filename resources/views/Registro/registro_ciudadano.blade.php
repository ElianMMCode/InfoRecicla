<x-app-layout>

  <link rel="stylesheet" href="css/Registro/styleRegistro.css">

  <!-- NAVBAR -->
  <x-navbar-layout>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#nav" aria-controls="nav"
      aria-expanded="false" aria-label="Alternar navegación">
      <span class="navbar-toggler-icon"></span>
    </button>

  </x-navbar-layout>

  <div id="nav" class="collapse navbar-collapse">
    <ul class="navbar-nav ms-auto align-items-lg-center align-items-center gap-2">
      <li class="nav-item"><a class="nav-link" href="/publicaciones">Publicaciones</a></li>
      <li class="nav-item"><a class="nav-link" href="/mapa">Mapa ECA</a></li>

      <li class="nav-item dropdown">
        <a class="btn btn-light text-success fw-semibold px-3 dropdown-toggle" href="#" data-bs-toggle="dropdown"
          aria-expanded="true">Acceder</a>
        <ul class="dropdown-menu dropdown-menu-end">
          <li><a class="dropdown-item" href="/login">Iniciar sesión</a></li>
          <li>
            <hr class="dropdown-divider">
          </li>
          <li><a class="dropdown-item" href="/registro/ciudadano">Registrarse (Ciudadano)</a>
          </li>
          <li><a class="dropdown-item" href="/registro/eca">Registrar Punto ECA</a></li>
        </ul>
      </li>
    </ul>
  </div>
  </div>
  </nav>

  <div id="mainNav" class="collapse navbar-collapse">
    <ul class="navbar-nav ms-auto align-items-lg-center gap-2">
      <li class="nav-item"><a class="nav-link" href="/publicaciones">Publicaciones</a></li>
      <li class="nav-item"><a class="nav-link" href="/mapa">Mapa ECA</a></li>
      <li class="nav-item dropdown">
        <a class="btn btn-light text-success fw-semibold px-3 dropdown-toggle" href="#" data-bs-toggle="dropdown"
          aria-expanded="false">Acceder</a>
        <ul class="dropdown-menu dropdown-menu-end">
          <li><a class="dropdown-item" href="/login">Iniciar sesión</a></li>
          <li>
            <hr class="dropdown-divider">
          </li>
          <li><a class="dropdown-item active" href="/registro/ciudadano">Registrarse (Ciudadano)</a></li>
          <li><a class="dropdown-item" href="/registro/eca">Registrar Punto ECA</a></li>
        </ul>
      </li>
    </ul>
  </div>
  </div>
  </nav>

  <main class="container">
    <div class="card shadow-sm">
      <div class="card-body">
        <h1 class="h3 text-center mb-4">Crear cuenta — Ciudadano</h1>

        <!-- ALERTAS -->
        <div id="formAlert" class="alert d-none" role="alert"></div>

        <!-- FORMULARIO -->
        <form method="POST" action="/registro/ciudadano" id="citizenForm" class="needs-validation" novalidate enctype="multipart/form-data">
          @csrf
            <input type="hidden" name="tipo" value="Ciudadano">
          <!-- Datos de acceso (tabla users) -->
          <div class="row g-3">
            <div class="col-md-6">
              <label for="correo" class="form-label">Correo electrónico</label>
              <input type="email" class="form-control" id="correo" name="correo" autocomplete="email" required>
              <div class="invalid-feedback">Ingresa un correo válido.</div>
            </div>
            <div class="col-md-3">
              <label for="password" class="form-label">Contraseña</label>
              <input type="password" class="form-control" id="password" name="password" minlength="8" maxlength="64"
                required>
              <div class="invalid-feedback">Mínimo 8 caracteres.</div>
            </div>
            <div class="col-md-3">
              <label for="password_confirm" class="form-label">Confirmar</label>
              <input type="password" class="form-control" id="password_confirm" name="password_confirm" required>
              <div class="invalid-feedback">Las contraseñas no coinciden.</div>
            </div>
            <div class="col-md-6">
              <label for="nombre_usuario" class="form-label">Nombre de usuario</label>
              <input type="text" class="form-control" id="nombre_usuario" name="nombre_usuario" required>
              <div class="invalid-feedback">Campo obligatorio.</div>
            </div>
          </div>

          <hr class="my-4">

          <!-- Perfil ciudadano (tabla citizen_profiles) -->
          <div class="row g-3">
            <div class="col-md-6">
              <label for="nombre" class="form-label">Nombre</label>
              <input type="text" class="form-control" id="nombre" name="nombre" required>
              <div class="invalid-feedback">Campo obligatorio.</div>
            </div>
            <div class="col-md-6">
              <label for="apellido" class="form-label">Apellido</label>
              <input type="text" class="form-control" id="apellido" name="apellido" required>
              <div class="invalid-feedback">Campo obligatorio.</div>
            </div>

            <div class="col-md-6">
              <label for="fechaNacimiento" class="form-label">Fecha de nacimiento</label>
              <input type="date" class="form-control" id="fechaNacimiento" name="fechaNacimiento" required>
              <div class="invalid-feedback">Selecciona una fecha válida.</div>
            </div>

            <div class="col-md-6">
              <label for="avatar" class="form-label">Foto de perfil (opcional)</label>
              <input type="file" class="form-control" id="avatar" name="avatar" accept="image/*">
            </div>
            <div class="col-md-6">
              <label for="localidad" class="form-label">Localidad</label>
                                <select id="localidad" name="localidad" class="form-select" required>
                                  <option value="" disabled selected>Selecciona localidad...</option>
                                  <option value="Usaquen">Usaquén</option>
                                  <option value="Chapinero">Chapinero</option>
                                  <option value="Santa Fe">Santa Fe</option>
                                  <option value="San Cristobal">San Cristóbal</option>
                                  <option value="Usme">Usme</option>
                                  <option value="Tunjuelito">Tunjuelito</option>
                                  <option value="Bosa">Bosa</option>
                                  <option value="Kennedy">Kennedy</option>
                                  <option value="Fontibon">Fontibón</option>
                                  <option value="Engativa">Engativá</option>
                                  <option value="Suba">Suba</option>
                                  <option value="Barrios Unidos">Barrios Unidos</option>
                                  <option value="Teusaquillo">Teusaquillo</option>
                                  <option value="Los Martires">Los Mártires</option>
                                  <option value="Antonio Narino">Antonio Nariño</option>
                                  <option value="Puente Aranda">Puente Aranda</option>
                                  <option value="La Candelaria">La Candelaria</option>
                                  <option value="Rafael Uribe">Rafael Uribe</option>
                                  <option value="Ciudad Bolivar">Ciudad Bolívar</option>
                                  <option value="Sumapaz">Sumapaz</option>
                                </select>
                <div class="invalid-feedback">Selecciona una localidad.</div>
             </div>
            <div class="col-md-6">
              <label class="form-label d-block">Género (opcional)</label>
              <div class="d-flex gap-3">
                <div class="form-check"><input class="form-check-input" type="radio" name="genero" id="gM"
                    value="Masculino"><label class="form-check-label" for="gM">Masculino</label></div>
                <div class="form-check"><input class="form-check-input" type="radio" name="genero" id="gF"
                    value="Femenino"><label class="form-check-label" for="gF">Femenino</label></div>
                <div class="form-check"><input class="form-check-input" type="radio" name="genero" id="gNB"
                    value="Otro"><label class="form-check-label" for="gNB">Otro</label></div>
              </div>
            </div>

            

            <div class="form-check mt-4">
              <input type="hidden" name="recibeNotificaciones" value="0" id="recibeNotificaciones">
              <input class="form-check-input" type="checkbox" value="1" id="recibeNotificaciones"
                name="recibeNotificaciones">
              <label class="form-check-label" for="recibeNotificaciones">
                Deseo recibir notificaciones sobre publicaciones, comentarios y novedades.
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
              <button type="submit" class="btn btn-success btn-lg">Crear cuenta</button>
            </div>
            <div class="mt-3">
              <?php
              if (isset($_GET['mensaje'])) {
                echo "<div class='alert alert-info'>" . $_GET['mensaje'] . "</div>";
              }
              ?>

              <p class="text-center mt-3 mb-0">
                <small>¿Ya tienes cuenta? <a href="/login">Inicia sesión</a></small>
              </p>
        </form>
      </div>
    </div>
  </main>

  <script src="js/Registro/ciudadano.js"></script>

</x-app-layout>