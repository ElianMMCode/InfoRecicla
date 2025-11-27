<x-app-layout>
    <x-navbar-layout></x-navbar-layout>

    <div class="bg-success d-flex flex-column justify-content-center flex-grow-1 py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-12 col-sm-10 col-md-8 col-lg-6 col-xl-5">
                    <div class="card shadow border-0">
                        <div class="card-header bg-success text-white text-center">
                            <h5 class="mb-0">Registro exitoso</h5>
                        </div>
                        <div class="card-body text-center vstack gap-3">
                            <div>
                                <h1 class="h4 fw-semibold text-success mb-1">¡Bienvenido a InfoRecicla!</h1>
                                <p class="text-muted small mb-0">Tu cuenta se creó correctamente.</p>
                            </div>
                            <div class="alert alert-success py-2 small mb-0">
                                Ya puedes iniciar sesión y completar tu perfil.
                            </div>
                            <div class="d-grid mt-2">
                                <a href="{{ route('login') }}" class="btn btn-success">Iniciar sesión</a>
                            </div>
                        </div>
                        <div class="card-footer text-center small text-muted">
                            InfoRecicla · Plataforma de reciclaje colaborativo
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
