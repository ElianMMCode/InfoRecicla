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
      <form method="POST" action="{{ route('login.post') }}" novalidate>
  @csrf

  {{-- mensajes flash --}}
  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif
  



  {{-- OJO con los name: tu columna es "correo" --}}
  <div class="mb-3">
    <label class="form-label">Correo</label>
    <input type="email" name="correo" class="form-control" value="{{ old('correo') }}" required autofocus>
  </div>

  <div class="mb-3">
    <label class="form-label">Contraseña</label>
    <input type="password" name="password" class="form-control" required>
  </div>

  <div class="form-check mb-3">
    <input class="form-check-input" type="checkbox" name="remember" id="remember">
    <label class="form-check-label" for="remember">Mantener sesión iniciada</label>
  </div>

  <button type="submit" class="btn btn-success w-100">Entrar</button>
</form>

    </div>
  </div>
  @if(session('error'))
  <div class="alert alert-danger">{{ session('error') }}</div>
@endif

{{-- Muestra errores para saber si valida y si attempt falla --}}
@if(session('error')) <div class="alert alert-danger">{{ session('error') }}</div> @endif
@if ($errors->any())
  <div class="alert alert-danger"><ul>
    @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
  </ul></div>
@endif

  <!-- Bootstrap JS y validación personalizada -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>