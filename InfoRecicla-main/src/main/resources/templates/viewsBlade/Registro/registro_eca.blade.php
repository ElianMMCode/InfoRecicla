<x-app-layout>
    <x-navbar-layout />

    <div class="bg-success d-flex flex-column justify-content-center flex-grow-1 py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-12 col-xl-10">
                    <div class="card shadow">
                        <div class="card-header bg-success text-white text-center">
                            <h5 class="mb-0">Registrar Punto ECA</h5>
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

                            <form method="POST" action="{{ route('registro.eca') }}" id="ecaForm" novalidate
                                enctype="multipart/form-data" class="vstack gap-4">
                                @csrf
                                <input type="hidden" name="tipo" value="GestorECA">
                                <input type="hidden" name="account_type" value="eca">

                                <p class="small text-success fw-bold mb-1">Datos de cuenta</p>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="correo" class="form-label small fw-semibold">Correo electrónico
                                            (acceso)</label>
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
                                </div>

                                <p class="small text-success fw-bold mb-1 mt-2">Datos del gestor (propietario del punto)
                                </p>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="nombre" class="form-label small fw-semibold">Nombres</label>
                                        <input type="text"
                                            class="form-control form-control-sm @error('nombre') is-invalid @enderror"
                                            id="nombre" name="nombre" value="{{ old('nombre') }}" required>
                                        @error('nombre')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="apellido" class="form-label small fw-semibold">Apellidos</label>
                                        <input type="text"
                                            class="form-control form-control-sm @error('apellido') is-invalid @enderror"
                                            id="apellido" name="apellido" value="{{ old('apellido') }}" required>
                                        @error('apellido')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-4">
                                        <label for="tipoDocumento" class="form-label small fw-semibold">Tipo de
                                            documento</label>
                                        <select id="tipoDocumento" name="tipoDocumento"
                                            class="form-select form-select-sm @error('tipoDocumento') is-invalid @enderror"
                                            required>
                                            <option value="" disabled
                                                {{ old('tipoDocumento') ? '' : 'selected' }}>Selecciona...</option>
                                            @php($docs = ['Cédula de Ciudadanía', 'Cédula de Extranjería', 'Tarjeta de Identidad', 'Pasaporte'])
                                            @foreach ($docs as $doc)
                                                <option value="{{ $doc }}"
                                                    {{ old('tipoDocumento') === $doc ? 'selected' : '' }}>
                                                    {{ $doc }}</option>
                                            @endforeach
                                        </select>
                                        @error('tipoDocumento')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-4">
                                        <label for="numeroDocumento" class="form-label small fw-semibold">Número de
                                            documento</label>
                                        <input type="text"
                                            class="form-control form-control-sm @error('numeroDocumento') is-invalid @enderror"
                                            id="numeroDocumento" name="numeroDocumento"
                                            value="{{ old('numeroDocumento') }}" minlength="5" maxlength="20"
                                            required>
                                        @error('numeroDocumento')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <p class="small text-success fw-bold mb-1 mt-2">Información de la estación</p>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="nombrePunto" class="form-label small fw-semibold">Nombre del Punto
                                            ECA</label>
                                        <input type="text"
                                            class="form-control form-control-sm @error('nombrePunto') is-invalid @enderror"
                                            id="nombrePunto" name="nombrePunto" value="{{ old('nombrePunto') }}"
                                            required>
                                        @error('nombrePunto')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-3">
                                        <label for="nit" class="form-label small fw-semibold">NIT
                                            (opcional)</label>
                                        <input type="text"
                                            class="form-control form-control-sm @error('nit') is-invalid @enderror"
                                            id="nit" name="nit" value="{{ old('nit') }}"
                                            maxlength="20" placeholder="Si aplica">
                                        @error('nit')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="row g-3 mt-1">
                                    <div class="col-md-6">
                                        <label for="horarioAtencion" class="form-label small fw-semibold">Horario de
                                            atención (opcional)</label>
                                        <input type="text"
                                            class="form-control form-control-sm @error('horarioAtencion') is-invalid @enderror"
                                            id="horarioAtencion" name="horarioAtencion"
                                            value="{{ old('horarioAtencion') }}"
                                            placeholder="Lun-Vie 8:00–17:00, Sáb 9:00–13:00">
                                        @error('horarioAtencion')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="row g-3 mt-1">
                                    <div class="col-md-6">
                                        <label for="correoPunto" class="form-label small fw-semibold">Correo de
                                            contacto del punto</label>
                                        <input type="email"
                                            class="form-control form-control-sm @error('correoPunto') is-invalid @enderror"
                                            id="correoPunto" name="correoPunto" value="{{ old('correoPunto') }}"
                                            placeholder="Puede ser distinto al de acceso" required>
                                        @error('correoPunto')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="telefonoPunto" class="form-label small fw-semibold">Teléfono de
                                            contacto del punto</label>
                                        <input type="tel"
                                            class="form-control form-control-sm @error('telefonoPunto') is-invalid @enderror"
                                            id="telefonoPunto" name="telefonoPunto"
                                            value="{{ old('telefonoPunto') }}" inputmode="tel" pattern="^\d{10}$"
                                            maxlength="10" placeholder="Ej: 3XXXXXXXXX" required>
                                        @error('telefonoPunto')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="row g-3 mt-1">
                                    <div class="col-md-6">
                                        <label for="direccionPunto" class="form-label small fw-semibold">Dirección del
                                            punto</label>
                                        <input type="text"
                                            class="form-control form-control-sm @error('direccionPunto') is-invalid @enderror"
                                            id="direccionPunto" name="direccionPunto"
                                            value="{{ old('direccionPunto') }}" required>
                                        @error('direccionPunto')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-3">
                                        <label for="ciudad" class="form-label small fw-semibold">Ciudad</label>
                                        <select id="ciudad" name="ciudad"
                                            class="form-select form-select-sm @error('ciudad') is-invalid @enderror"
                                            required>
                                            <option value="" disabled {{ old('ciudad') ? '' : 'selected' }}>
                                                Selecciona...</option>
                                            <option value="Bogotá" {{ old('ciudad') === 'Bogotá' ? 'selected' : '' }}>
                                                Bogotá</option>
                                        </select>
                                        @error('ciudad')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-3">
                                        <label for="localidadPunto"
                                            class="form-label small fw-semibold">Localidad</label>
                                        <select id="localidadPunto" name="localidadPunto"
                                            class="form-select form-select-sm @error('localidadPunto') is-invalid @enderror"
                                            required>
                                            <option value="" disabled
                                                {{ old('localidadPunto') ? '' : 'selected' }}>Selecciona localidad...
                                            </option>
                                            @php($locs = ['Usaquén', 'Chapinero', 'Santa Fe', 'San Cristóbal', 'Usme', 'Tunjuelito', 'Bosa', 'Kennedy', 'Fontibón', 'Engativá', 'Suba', 'Barrios Unidos', 'Teusaquillo', 'Los Mártires', 'Antonio Nariño', 'Puente Aranda', 'La Candelaria', 'Rafael Uribe', 'Ciudad Bolívar', 'Sumapaz'])
                                            @foreach ($locs as $l)
                                                <option value="{{ $l }}"
                                                    {{ old('localidadPunto') === $l ? 'selected' : '' }}>
                                                    {{ $l }}</option>
                                            @endforeach
                                        </select>
                                        @error('localidadPunto')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <label class="form-label small fw-semibold mb-1">Ubicación (opcional)</label>
                                    <div class="row g-2 align-items-start">
                                        <div class="col-12 col-md-4">
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-text">Lat</span>
                                                <input type="text" class="form-control" id="latitudVisible"
                                                    placeholder="Ej: 4.609710" value="{{ old('latitud') }}">
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-4">
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-text">Lng</span>
                                                <input type="text" class="form-control" id="longitudVisible"
                                                    placeholder="Ej: -74.081749" value="{{ old('longitud') }}">
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-4 d-grid d-md-block">
                                            <button type="button" id="geoBtn"
                                                class="btn btn-outline-success btn-sm w-100">GPS</button>
                                        </div>
                                    </div>
                                    <div id="locationDisplay" class="form-text mt-1">Pulsa GPS para solicitar permiso
                                        al navegador y capturar tu ubicación precisa.</div>
                                    <input type="hidden" id="latitud" name="latitud"
                                        value="{{ old('latitud') }}">
                                    <input type="hidden" id="longitud" name="longitud"
                                        value="{{ old('longitud') }}">
                                </div>
                                <div class="row g-3 mt-1">
                                    <div class="col-md-6">
                                        <label for="logo" class="form-label small fw-semibold">Logo del punto
                                            (opcional)</label>
                                        <input type="file"
                                            class="form-control form-control-sm @error('logo') is-invalid @enderror"
                                            id="logo" name="logo" accept="image/*">
                                        @error('logo')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="foto" class="form-label small fw-semibold">Foto del punto
                                            (opcional)</label>
                                        <input type="file"
                                            class="form-control form-control-sm @error('foto') is-invalid @enderror"
                                            id="foto" name="foto" accept="image/*">
                                        @error('foto')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="sitioWeb" class="form-label small fw-semibold">Sitio web
                                            (opcional)</label>
                                        <input type="url"
                                            class="form-control form-control-sm @error('sitioWeb') is-invalid @enderror"
                                            id="sitioWeb" name="sitioWeb" value="{{ old('sitioWeb') }}"
                                            placeholder="https://">
                                        @error('sitioWeb')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-check mt-3">
                                    <input class="form-check-input" type="checkbox" value="1" id="mostrarMapa"
                                        name="mostrarMapa" {{ old('mostrarMapa') ? 'checked' : '' }} required>
                                    <label class="form-check-label small" for="mostrarMapa">Mostrar el punto en el
                                        mapa público cuando sea aprobado.</label>
                                </div>
                                <div class="form-check mt-2">
                                    <input class="form-check-input" type="checkbox" value="1"
                                        id="recibeNotificaciones" name="recibeNotificaciones"
                                        {{ old('recibeNotificaciones') ? 'checked' : '' }} required>
                                    <label class="form-check-label small" for="recibeNotificaciones">Deseo recibir
                                        notificaciones (aprobaciones, comentarios, etc.).</label>
                                </div>
                                <div class="form-check mt-2">
                                    <input class="form-check-input" type="checkbox" value="1" id="terms"
                                        required>
                                    <label class="form-check-label small" for="terms">Acepto los términos y
                                        condiciones y el tratamiento de datos.</label>
                                    <div class="invalid-feedback">Debes aceptar los términos para continuar.</div>
                                </div>
                                <div class="d-grid mt-3">
                                    <button type="submit" class="btn btn-success">Registrar Punto ECA</button>
                                </div>
                                <p class="text-center mt-3 mb-0 small">¿Ya tienes cuenta? <a
                                        href="{{ route('login') }}">Inicia sesión</a></p>
                            </form>
                        </div>
                        <div class="card-footer text-center small text-muted">InfoRecicla · Registro Punto ECA</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // Geolocalización precisa (sin aproximaciones): getCurrentPosition + watchPosition fallback
            (function() {
                const btn = document.getElementById('geoBtn');
                if (!btn) return;
                const latH = document.getElementById('latitud');
                const lngH = document.getElementById('longitud');
                const latV = document.getElementById('latitudVisible');
                const lngV = document.getElementById('longitudVisible');
                const display = document.getElementById('locationDisplay');
                let busy = false;
                let watchId = null;
                let attempt = 0;

                function feedback(msg, type = 'muted') {
                    display.className = `form-text mt-1 text-${type}`;
                    display.textContent = msg;
                }

                function setBusy(s) {
                    busy = s;
                    btn.disabled = s;
                    btn.textContent = s ? 'Obteniendo…' : 'GPS';
                }

                function write(lat, lng) {
                    if (lat == null || lng == null) return;
                    lat = Number(lat);
                    lng = Number(lng);
                    if (isNaN(lat) || isNaN(lng)) return;
                    latH.value = lat.toFixed(6);
                    lngH.value = lng.toFixed(6);
                    latV.value = latH.value;
                    lngV.value = lngH.value;
                }
                async function permissionState() {
                    if (!('permissions' in navigator)) return null;
                    try {
                        const s = await navigator.permissions.query({
                            name: 'geolocation'
                        });
                        return s.state;
                    } catch {
                        return null;
                    }
                }

                function secureCheck() {
                    if (isSecureContext) return true;
                    const host = location.hostname;
                    if (host === 'localhost' || host === '127.0.0.1') return true;
                    feedback('El navegador exige HTTPS para geolocalización. Usa https:// o localhost.', 'danger');
                    return false;
                }

                function clearWatch() {
                    if (watchId != null) {
                        navigator.geolocation.clearWatch(watchId);
                        watchId = null;
                    }
                }

                function startWatchFallback() {
                    if (!('geolocation' in navigator)) return;
                    feedback('Intentando actualización continua (watchPosition)…', 'warning');
                    let gotFirst = false;
                    watchId = navigator.geolocation.watchPosition(
                        pos => {
                            const {
                                latitude,
                                longitude,
                                accuracy
                            } = pos.coords;
                            if (!gotFirst) {
                                gotFirst = true;
                                write(latitude, longitude);
                                feedback('Ubicación obtenida (watch). Precisión ~' + Math.round(accuracy) + 'm.',
                                    'success');
                                setBusy(false);
                                // Dejamos unos segundos más por si mejora la precisión
                                setTimeout(() => clearWatch(), 5000);
                            } else if (accuracy < 25) { // mejora significativa
                                write(latitude, longitude);
                                feedback('Precisión refinada (' + Math.round(accuracy) + 'm).', 'success');
                            }
                        },
                        err => {
                            feedback('No fue posible obtener ubicación con watch (' + err.code + ').', 'danger');
                            setBusy(false);
                            clearWatch();
                        }, {
                            enableHighAccuracy: true,
                            maximumAge: 0,
                            timeout: 15000
                        }
                    );
                }

                function attemptPrecise() {
                    attempt++;
                    if (!('geolocation' in navigator)) {
                        feedback('Este navegador no soporta geolocalización.', 'danger');
                        return;
                    }
                    if (!secureCheck()) return;
                    setBusy(true);
                    feedback('Solicitando permiso al navegador…');
                    let finished = false;
                    const timeoutMs = 10000;
                    const timer = setTimeout(() => {
                        if (finished) return;
                        feedback('Sin respuesta rápida, escalando a modo continuo…', 'warning');
                        startWatchFallback();
                    }, 4000);
                    navigator.geolocation.getCurrentPosition(
                        pos => {
                            if (finished) return;
                            finished = true;
                            clearTimeout(timer);
                            const {
                                latitude,
                                longitude,
                                accuracy
                            } = pos.coords;
                            write(latitude, longitude);
                            feedback('Ubicación capturada (±' + Math.round(accuracy) + 'm).', 'success');
                            setBusy(false);
                        },
                        err => {
                            if (finished) return;
                            finished = true;
                            clearTimeout(timer);
                            const map = {
                                1: 'Permiso denegado por el usuario',
                                2: 'Posición no disponible',
                                3: 'Tiempo de espera agotado'
                            };
                            feedback((map[err.code] || 'Error desconocido') + (err.code === 1 ?
                                ' (ajusta permisos del sitio y reintenta)' : ''), 'danger');
                            if (err.code !== 1) { // si no es permiso denegado probamos watch
                                startWatchFallback();
                            } else {
                                setBusy(false);
                            }
                        }, {
                            enableHighAccuracy: true,
                            timeout: timeoutMs,
                            maximumAge: 0
                        }
                    );
                }

                btn.addEventListener('click', async () => {
                    if (busy) return;
                    const state = await permissionState();
                    if (state === 'denied') {
                        feedback(
                            'Permiso previamente denegado. Ve al candado del navegador y permite "Ubicación" para este sitio, luego reintenta.',
                            'danger');
                        return;
                    }
                    attemptPrecise();
                });

                // Permitir edición manual conservando precisión formateada
                [latV, lngV].forEach(inp => inp.addEventListener('blur', () => {
                    const v = inp.value.trim();
                    if (/^[-]?\d{1,3}\.\d+$/.test(v)) {
                        if (inp === latV) latH.value = v;
                        else lngH.value = v;
                        feedback('Coordenadas manuales establecidas.', 'info');
                    }
                }));
            })();

            // Validación ligera similar a otras vistas
            (function() {
                const form = document.getElementById('ecaForm');
                if (!form) return;
                const correo = document.getElementById('correo');
                const pwd = document.getElementById('password');
                const pwd2 = document.getElementById('password_confirmation');
                const terms = document.getElementById('terms');
                const tel = document.getElementById('telefonoPunto');

                function invalidate(field, msg) {
                    field.classList.add('is-invalid');
                    let fb = field.parentElement.querySelector('.invalid-feedback');
                    if (!fb) {
                        fb = document.createElement('div');
                        fb.className = 'invalid-feedback';
                        field.parentElement.appendChild(fb);
                    }
                    if (msg) fb.textContent = msg;
                }

                function clear(field) {
                    field.classList.remove('is-invalid');
                }

                function validEmail() {
                    clear(correo);
                    if (!/^[^@\s]+@[^@\s]+\.[^@\s]+$/.test(correo.value)) {
                        invalidate(correo, 'Correo no válido.');
                        return false;
                    }
                    return true;
                }

                function validPwd() {
                    clear(pwd);
                    clear(pwd2);
                    if (pwd.value.length < 8) {
                        invalidate(pwd, 'Mínimo 8 caracteres.');
                        return false;
                    }
                    if (pwd.value !== pwd2.value) {
                        invalidate(pwd2, 'No coincide.');
                        return false;
                    }
                    return true;
                }

                function validTel() {
                    if (!tel.value) return true;
                    clear(tel);
                    if (!/^[0-9\s()+-]{7,20}$/.test(tel.value)) {
                        invalidate(tel, 'Teléfono inválido.');
                        return false;
                    }
                    return true;
                }

                function validTerms() {
                    terms.classList.remove('is-invalid');
                    if (!terms.checked) {
                        terms.classList.add('is-invalid');
                        return false;
                    }
                    return true;
                }

                correo.addEventListener('blur', validEmail);
                pwd.addEventListener('input', validPwd);
                pwd2.addEventListener('input', validPwd);
                tel.addEventListener('blur', validTel);
                terms.addEventListener('change', validTerms);

                form.addEventListener('submit', e => {
                    let ok = form.checkValidity();
                    if (!validEmail()) ok = false;
                    if (!validPwd()) ok = false;
                    if (!validTel()) ok = false;
                    if (!validTerms()) ok = false;
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
