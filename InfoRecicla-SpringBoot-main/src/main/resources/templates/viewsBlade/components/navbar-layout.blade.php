<nav class="navbar navbar-expand-lg navbar-dark bg-success">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center gap-2" href="/">
            <img src="/imagenes/logo.png" alt="Logo InfoRecicla" width="90" height="90" class="rounded">
            <span class="fs-1 fw-semibold">InfoRecicla</span>
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav"
            aria-controls="mainNav" aria-expanded="false" aria-label="Alternar navegación">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div id="mainNav" class="collapse navbar-collapse">
            {{-- Slot opcional para inyectar items adicionales desde la vista --}}
            {{ $slot }}

            <ul class="navbar-nav ms-auto align-items-lg-center gap-2">
                {{-- <li class="nav-item"><a class="nav-link" href="{{ route('publicaciones') }}">Publicaciones</a></li> --}}
                <li class="nav-item"><a class="nav-link" href="{{ route('mapa') }}">Mapa ECA</a></li>

                @guest
                    <li class="nav-item dropdown">
                        <a class="btn btn-light text-success fw-semibold px-3 dropdown-toggle" href="#"
                            data-bs-toggle="dropdown" aria-expanded="false">Acceder</a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="{{ route('login') }}">Iniciar sesión</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item" href="{{ route('registro', 'ciudadano') }}">Registrarse
                                    (Ciudadano)</a></li>
                            <li><a class="dropdown-item" href="{{ route('registro', 'eca') }}">Registrar Punto ECA</a></li>
                        </ul>
                    </li>
                @else
                    @php($user = auth()->user())
                    {{-- Determinar destino según rol --}}
                    @php($isCiudadano = $user && method_exists($user, 'hasRole') ? $user->hasRole('Ciudadano') : ($user->rol ?? '') === 'Ciudadano')
                    @php($isGestor = $user && method_exists($user, 'hasRole') ? $user->hasRole('GestorECA') : ($user->rol ?? '') === 'GestorECA')
                    @php($isAdmin = $user && method_exists($user, 'hasRole') ? $user->hasRole('Administrador') : ($user->rol ?? '') === 'Administrador')

                    <li class="nav-item dropdown" id="miPanelDropdownWrapper">
                        <a id="miPanelToggle" class="btn btn-light text-success fw-semibold px-3 dropdown-toggle"
                            href="#" data-bs-toggle="dropdown" aria-expanded="false" role="button"
                            autocomplete="off">Mi Panel</a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            @if ($isCiudadano)
                                <li><a class="dropdown-item" href="{{ route('ciudadano') }}">Panel Ciudadano</a></li>
                            @endif
                            @if ($isGestor || $isAdmin)
                                <li><a class="dropdown-item" href="{{ route('eca.index') }}">Panel Punto ECA</a></li>
                            @endif
                            @if ($isAdmin)
                                <li><a class="dropdown-item" href="{{ route('admin') }}">Administración</a></li>
                            @endif
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}" class="px-3 py-1">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-danger w-100">Cerrar
                                        sesión</button>
                                </form>
                            </li>
                        </ul>
                    </li>
                @endguest
            </ul>
        </div>
    </div>
</nav>
<script>
    // Fallback específico: si al hacer click no se abre, instanciamos y togglamos manualmente.
    document.addEventListener('DOMContentLoaded', () => {
        const toggle = document.getElementById('miPanelToggle');
        if (!toggle) return;
        toggle.addEventListener('click', (e) => {
            // Si Bootstrap ya lo maneja, no interferimos; detectamos si el hermano no tiene clase show tras un tick.
            setTimeout(() => {
                const menu = toggle.parentElement.querySelector('.dropdown-menu');
                if (!menu.classList.contains('show')) {
                    if (window.bootstrap && bootstrap.Dropdown) {
                        let inst = bootstrap.Dropdown.getInstance(toggle);
                        if (!inst) inst = new bootstrap.Dropdown(toggle);
                        inst.toggle();
                    } else {
                        // Degradado: alternar manualmente clases básicas
                        menu.classList.toggle('show');
                        toggle.setAttribute('aria-expanded', menu.classList.contains('show') ?
                            'true' : 'false');
                    }
                }
            }, 40); // pequeño delay para dejar actuar a Bootstrap si está operativo
        });
    });
</script>
