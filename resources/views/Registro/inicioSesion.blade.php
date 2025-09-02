<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Iniciar Sesión | InfoRecicla</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="css/Registro/styleInicioSesion.css">
</head>

<body class="bg-success">
  <div class="card login-card shadow-sm">
    <div class="card-body">
      <h4 class="card-title text-center mb-4">Iniciar Sesión</h4>
      <form id="loginForm" novalidate>
        <div class="mb-3">
          <label for="email" class="form-label">Correo Electrónico</label>
          <input type="email" class="form-control" id="email" name="email" required>
          <div class="invalid-feedback">
            Ingresa un correo con formato válido (ejemplo@dominio.com).
          </div>
        </div>
        <div class="mb-3">
          <label for="password" class="form-label">Contraseña</label>
          <input type="password" class="form-control" id="password" name="password" required minlength="6">
          <div class="invalid-feedback">
            La contraseña debe tener al menos 6 caracteres.
          </div>
        </div>
        <div id="loginError" class="alert alert-danger mt-3" style="display:none;">
          Correo o contraseña incorrectos.
        </div>
        <div class="d-grid mb-3">
          <button type="submit" class="btn btn-success">Entrar</button>
        </div>
        <div class="text-center">
          <small>¿No tienes cuenta? <a href="/registro">Regístrate aquí</a></small>
        </div>
      </form>
    </div>
  </div>

  <!-- Bootstrap JS y validación personalizada -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="js/login.js"></script>
</body>

</html>