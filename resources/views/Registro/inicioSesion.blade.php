<x-app-layout>
    <x-navbar-layout />

    <div class="bg-success d-flex flex-column justify-content-center flex-grow-1 py-5">
        <div class="row justify-content-center">
            <div class="col-12 col-sm-10 col-md-6 col-lg-4">
                <div class="card shadow">
                    <div class="card-header bg-success text-white text-center">
                        <h5 class="mb-0">Iniciar sesión</h5>
                    </div>
                    <div class="card-body">
                        {{-- Mensajes flash globales --}}
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

                        <form method="POST" action="{{ route('login.post') }}" novalidate class="vstack gap-3">
                            @csrf
                            <div>
                                <label class="form-label small fw-semibold">Correo</label>
                                <input type="email" name="correo"
                                    class="form-control form-control-sm @error('correo') is-invalid @enderror"
                                    value="{{ old('correo') }}" required autofocus autocomplete="email">
                                @error('correo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div>
                                <label class="form-label small fw-semibold">Contraseña</label>
                                <input type="password" name="password"
                                    class="form-control form-control-sm @error('password') is-invalid @enderror"
                                    required autocomplete="current-password">
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                                <div class="form-check m-0">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember"
                                        {{ old('remember') ? 'checked' : '' }}>
                                    <label class="form-check-label small" for="remember">Mantener sesión</label>
                                </div>
                                <a href="{{ route('registro') }}" class="small text-decoration-none">Crear cuenta</a>
                            </div>
                            <button type="submit" class="btn btn-success w-100">Entrar</button>
                        </form>
                    </div>
                    <div class="card-footer text-center small text-muted">
                        InfoRecicla · Acceso seguro
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            if (document.querySelector('.is-invalid')) {
                document.querySelector('.is-invalid').focus();
            }
        </script>
    @endpush
</x-app-layout>
