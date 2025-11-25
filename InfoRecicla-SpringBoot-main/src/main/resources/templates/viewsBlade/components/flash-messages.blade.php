@if (session('ok') || session('error'))
    <div class="mb-3">
        @if (session('ok'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong>{{ session('ok') }}</strong>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>{{ session('error') }}</strong>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
            </div>
        @endif
    </div>
@endif
