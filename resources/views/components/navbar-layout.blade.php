<nav class="navbar navbar-expand-lg navbar-dark bg-success">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2" href="/">
                <img src="/imagenes/logo.png" alt="Logo InfoRecicla" width="90" height="90" class="rounded">
                <span class="fs-1 fw-semibold">InfoRecicla</span>
            </a>

            {{-- Menú de navegación --}}
            {{ $slot }}

        </div>
    </nav>