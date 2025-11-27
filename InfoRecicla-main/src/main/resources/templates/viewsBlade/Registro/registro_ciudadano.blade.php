<x-app-layout>
    <x-navbar-layout />

    <div class="bg-success d-flex flex-column justify-content-center flex-grow-1 py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-12 col-lg-10 col-xl-8">
                    <div class="card shadow">
                        <div class="card-header bg-success text-white text-center">
                            <h5 class="mb-0">Crear cuenta — Ciudadano</h5>
                        </div>
                        <div class="card-body">
                            @if (session('success'))
                                <div class="alert alert-success small mb-3">{{ session('success') }}</div>
                            @endif
                            @if (session('error'))
                                <div class="alert alert-danger small mb-3">{{ session('error') }}</div>
                            @endif
                            @if ($errors->any())
                                <div class="alert alert-danger small mb-3">
                                    <ul class="mb-0 ps-3">
                                        @foreach ($errors->all() as $e)
                                            <li>{{ $e }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <form method="POST" action="{{ route('registro.ciudadano') }}" id="citizenForm" novalidate
                                enctype="multipart/form-data" class="vstack gap-4">
                                @csrf
                                <input type="hidden" name="tipo" value="Ciudadano">

                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="correo" class="form-label small fw-semibold">Correo
                                            electrónico</label>
                                        <input type="email"
                                            class="form-control form-control-sm @error('correo') is-invalid @enderror"
                                            id="correo" name="correo" value="{{ old('correo') }}"
                                            autocomplete="email" required>
                                        @error('correo')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-3">
                                        <label for="password" class="form-label small fw-semibold">Contraseña</label>
                                        <input type="password"
                                            class="form-control form-control-sm @error('password') is-invalid @enderror"
                                            id="password" name="password" minlength="8" maxlength="64" required>
                                        @error('password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-3">
                                        <label for="password_confirmation"
                                            class="form-label small fw-semibold">Confirmar</label>
                                        <input type="password"
                                            class="form-control form-control-sm @error('password_confirmation') is-invalid @enderror"
                                            id="password_confirmation" name="password_confirmation" required>
                                        @error('password_confirmation')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="nombre_usuario" class="form-label small fw-semibold">Nombre de
                                            usuario</label>
                                        <input type="text"
                                            class="form-control form-control-sm @error('nombre_usuario') is-invalid @enderror"
                                            id="nombre_usuario" name="nombre_usuario"
                                            value="{{ old('nombre_usuario') }}" required>
                                        @error('nombre_usuario')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <hr class="my-2">

                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="nombre" class="form-label small fw-semibold">Nombre</label>
                                        <input type="text"
                                            class="form-control form-control-sm @error('nombre') is-invalid @enderror"
                                            id="nombre" name="nombre" value="{{ old('nombre') }}" required>
                                        @error('nombre')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="apellido" class="form-label small fw-semibold">Apellido</label>
                                        <input type="text"
                                            class="form-control form-control-sm @error('apellido') is-invalid @enderror"
                                            id="apellido" name="apellido" value="{{ old('apellido') }}" required>
                                        @error('apellido')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="fechaNacimiento" class="form-label small fw-semibold">Fecha de
                                            nacimiento</label>
                                        <input type="date"
                                            class="form-control form-control-sm @error('fechaNacimiento') is-invalid @enderror"
                                            id="fechaNacimiento" name="fechaNacimiento"
                                            value="{{ old('fechaNacimiento') }}" required>
                                        @error('fechaNacimiento')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="avatar" class="form-label small fw-semibold">Foto de perfil
                                            (opcional)</label>
                                        <input type="file"
                                            class="form-control form-control-sm @error('avatar') is-invalid @enderror"
                                            id="avatar" name="avatar" accept="image/*">
                                        @error('avatar')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="localidad" class="form-label small fw-semibold">Localidad</label>
                                        <select id="localidad" name="localidad"
                                            class="form-select form-select-sm @error('localidad') is-invalid @enderror"
                                            required>
                                            <option value="" disabled {{ old('localidad') ? '' : 'selected' }}>
                                                Selecciona localidad...</option>
                                            @php($locs = ['Usaquen' => 'Usaquén', 'Chapinero' => 'Chapinero', 'Santa Fe' => 'Santa Fe', 'San Cristobal' => 'San Cristóbal', 'Usme' => 'Usme', 'Tunjuelito' => 'Tunjuelito', 'Bosa' => 'Bosa', 'Kennedy' => 'Kennedy', 'Fontibon' => 'Fontibón', 'Engativa' => 'Engativá', 'Suba' => 'Suba', 'Barrios Unidos' => 'Barrios Unidos', 'Teusaquillo' => 'Teusaquillo', 'Los Martires' => 'Los Mártires', 'Antonio Narino' => 'Antonio Nariño', 'Puente Aranda' => 'Puente Aranda', 'La Candelaria' => 'La Candelaria', 'Rafael Uribe' => 'Rafael Uribe', 'Ciudad Bolivar' => 'Ciudad Bolívar', 'Sumapaz' => 'Sumapaz'])
                                            @foreach ($locs as $val => $txt)
                                                <option value="{{ $val }}"
                                                    {{ old('localidad') === $val ? 'selected' : '' }}>
                                                    {{ $txt }}</option>
                                            @endforeach
                                        </select>
                                        @error('localidad')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-semibold d-block">Género (opcional)</label>
                                        <div class="d-flex gap-3">
                                            @php($generos = ['Masculino' => 'Masculino', 'Femenino' => 'Femenino', 'Otro' => 'Otro'])
                                            @foreach ($generos as $gVal => $gTxt)
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="genero"
                                                        id="g_{{ $gVal }}" value="{{ $gVal }}"
                                                        {{ old('genero') === $gVal ? 'checked' : '' }}>
                                                    <label class="form-check-label"
                                                        for="g_{{ $gVal }}">{{ $gTxt }}</label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-check mt-2">
                                            <input type="hidden" name="recibeNotificaciones" value="0">
                                            <input class="form-check-input" type="checkbox" value="1"
                                                id="recibeNotificaciones" name="recibeNotificaciones"
                                                {{ old('recibeNotificaciones') ? 'checked' : '' }}>
                                            <label class="form-check-label small" for="recibeNotificaciones">Deseo
                                                recibir notificaciones sobre publicaciones, comentarios y
                                                novedades.</label>
                                        </div>
                                        <div class="form-check mt-3">
                                            <input class="form-check-input" type="checkbox" value="1"
                                                id="terms" required>
                                            <label class="form-check-label small" for="terms">Acepto los términos y
                                                condiciones y el tratamiento de datos.</label>
                                            <div class="invalid-feedback">Debes aceptar los términos para continuar.
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-grid mt-2">
                                    <button type="submit" class="btn btn-success">Crear cuenta</button>
                                </div>
                                <p class="text-center mt-3 mb-0 small">¿Ya tienes cuenta? <a href="/login">Inicia
                                        sesión</a></p>
                            </form>
                        </div>
                        <div class="card-footer text-center small text-muted">InfoRecicla · Registro ciudadano</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // Enfocar primer campo inválido si existe
            const invalid = document.querySelector('.is-invalid');
            if (invalid) {
                invalid.focus();
            }
            // Validación básica cliente
            (function() {
                const form = document.getElementById('citizenForm');
                if (!form) return;
                const pwd = document.getElementById('password');
                const pwd2 = document.getElementById('password_confirmation'); // confirmación
                const terms = document.getElementById('terms');
                const correo = document.getElementById('correo');

                function setInvalid(field, msg) {
                    if (!field) return;
                    field.classList.add('is-invalid');
                    let fb = field.parentElement.querySelector('.invalid-feedback');
                    if (!fb) {
                        fb = document.createElement('div');
                        fb.className = 'invalid-feedback';
                        field.parentElement.appendChild(fb);
                    }
                    if (msg) fb.textContent = msg;
                }

                function clearInvalid(field) {
                    if (!field) return;
                    field.classList.remove('is-invalid');
                }

                function validarPasswords() {
                    clearInvalid(pwd2);
                    clearInvalid(pwd);
                    if (pwd.value.length < 8) {
                        setInvalid(pwd, 'Mínimo 8 caracteres.');
                        return false;
                    }
                    if (pwd.value !== pwd2.value) {
                        setInvalid(pwd2, 'Las contraseñas no coinciden.');
                        return false;
                    }
                    return true;
                }

                function validarCorreo() {
                    clearInvalid(correo);
                    if (!correo.value.includes('@') || !correo.value.includes('.')) {
                        setInvalid(correo, 'Correo no válido.');
                        return false;
                    }
                    return true;
                }

                function validarTerms() {
                    terms.classList.remove('is-invalid');
                    if (!terms.checked) {
                        terms.classList.add('is-invalid');
                        return false;
                    }
                    return true;
                }

                // Eventos en tiempo real
                pwd2.addEventListener('input', validarPasswords);
                pwd.addEventListener('input', validarPasswords);
                correo.addEventListener('blur', validarCorreo);
                terms.addEventListener('change', validarTerms);

                form.addEventListener('submit', function(e) {
                    // Quitar invalid previos para campos con HTML5
                    Array.from(form.querySelectorAll('.is-invalid')).forEach(el => {
                        if (el !== pwd && el !== pwd2 && el !== correo && el !== terms) el.classList.remove(
                            'is-invalid');
                    });
                    let ok = true;
                    // Validación nativa primero
                    if (!form.checkValidity()) {
                        ok = false;
                    }
                    if (!validarCorreo()) ok = false;
                    if (!validarPasswords()) ok = false;
                    if (!validarTerms()) ok = false;
                    if (!ok) {
                        e.preventDefault();
                        e.stopPropagation();
                    }
                    form.classList.add('was-validated');
                });
            })();
        </script>
    @endpush
</x-app-layout>
