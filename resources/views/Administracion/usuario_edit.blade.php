<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Editar usuario • InfoRecicla</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container py-4">

        <a href="{{ route('admin.usuarios.index') }}" class="btn btn-outline-secondary mb-3">&larr; Volver</a>
        <h1 class="h5 text-success mb-3">Editar usuario</h1>

        @if(session('ok'))
        <div class="alert alert-success">{{ session('ok') }}</div>
        @endif
        @if($errors->any())
        <div class="alert alert-danger">
            <ul class="m-0 ps-3">
                @foreach($errors->all() as $e)
                <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form action="{{ route('admin.usuarios.update', $usuario->id) }}" method="POST" class="row g-3">
            @csrf @method('PUT')

            <div class="col-md-6">
                <label class="form-label text-success">Nombre</label>
                <input name="nombre" class="form-control" value="{{ old('nombre',$usuario->nombre) }}" required>
            </div>

            <div class="col-md-6">
                <label class="form-label text-success">Apellido</label>
                <input name="apellido" class="form-control" value="{{ old('apellido',$usuario->apellido) }}" required>
            </div>

            <div class="col-md-6">
                <label class="form-label text-success">Correo</label>
                <input name="correo" type="email" class="form-control" value="{{ old('correo',$usuario->correo) }}" required>
            </div>

            <div class="col-md-6">
                <label class="form-label text-success">Usuario</label>
                <input name="nombre_usuario" class="form-control" value="{{ old('nombre_usuario',$usuario->nombre_usuario) }}">
            </div>

            <div class="col-md-4">
                <label class="form-label text-success">Rol</label>
                <select name="rol" class="form-select" required>
                    @foreach(['Administrador','GestorECA','Ciudadano'] as $r)
                    <option value="{{ $r }}" @selected(old('rol',$usuario->rol)==$r)>{{ $r }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-4">
                <label class="form-label text-success">Estado</label>
                <select name="estado" class="form-select">
                    @foreach(['activo','inactivo','bloqueado'] as $e)
                    <option value="{{ $e }}" @selected(old('estado',$usuario->estado)==$e)>{{ $e }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-4">
                <label class="form-label text-success">Género</label>
                <select name="genero" class="form-select">
                    @foreach(['Masculino','Femenino','Otro'] as $g)
                    <option value="{{ $g }}" @selected(old('genero',$usuario->genero)==$g)>{{ $g }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-6">
                <label class="form-label text-success">Nueva contraseña (opcional)</label>
                <input name="password" type="password" class="form-control">
                <div class="form-text">Déjala vacía si no deseas cambiarla.</div>
            </div>

            <div class="col-md-6">
                <label class="form-label text-success">Confirmar contraseña</label>
                <input name="password_confirmation" type="password" class="form-control">
            </div>

            <div class="col-md-6">
                <label class="form-label text-success">Tipo documento</label>
                <input name="tipo_documento" class="form-control" value="{{ old('tipo_documento',$usuario->tipo_documento) }}">
            </div>

            <div class="col-md-6">
                <label class="form-label text-success">Número documento</label>
                <input name="numero_documento" class="form-control" value="{{ old('numero_documento',$usuario->numero_documento) }}">
            </div>

            <div class="col-md-6">
                <label class="form-label text-success">Teléfono</label>
                <input name="telefono" class="form-control" value="{{ old('telefono',$usuario->telefono) }}">
            </div>

            <div class="col-md-6">
                <label class="form-label text-success">Fecha nacimiento</label>
                <input name="fecha_nacimiento" type="date" class="form-control" value="{{ old('fecha_nacimiento', optional($usuario->fecha_nacimiento)->format('Y-m-d')) }}">
            </div>

            <div class="col-md-12">
                <label class="form-label text-success">Avatar URL</label>
                <input name="avatar_url" type="url" class="form-control" value="{{ old('avatar_url',$usuario->avatar_url) }}">
            </div>

            <div class="col-12">
                <button class="btn btn-success">Guardar cambios</button>
                <a href="{{ route('admin.usuarios.index') }}" class="btn btn-outline-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</body>

</html>