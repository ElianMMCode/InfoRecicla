<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>InfoRecicla</title>
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="{{ asset('imagenes/logo.png') }}">
</head>

<body class="d-flex flex-column min-vh-100">

    {{-- Contenido principal --}}
    <main class="flex-grow-1 d-flex flex-column">
        {{ $slot }}
    </main>

    <!-- FOOTER -->
    <footer class="bg-light border-top py-4 mt-auto">
        <div class="container">
            <ul class="nav justify-content-center gap-3">
                <li class="nav-item"><a class="nav-link text-muted" href="#">Acerca de</a></li>
                <li class="nav-item"><a class="nav-link text-muted" href="#">Soporte</a></li>
                <li class="nav-item"><a class="nav-link text-muted" href="#">Contacto</a></li>
            </ul>
            <p class="text-center text-muted mb-0 mt-2 small">&copy; <span id="year"></span> InfoRecicla</p>
        </div>
    </footer>

    <script>
        // año dinámico en el footer
        document.getElementById('year').textContent = new Date().getFullYear();
    </script>

    <!-- Bootstrap Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    {{-- Pila de scripts específicos de las vistas (@push('scripts')) --}}
    @stack('scripts')
</body>

</html>
