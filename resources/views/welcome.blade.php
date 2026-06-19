<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'AULITA') }}</title>
    @vite(['resources/scss/app.scss', 'resources/js/app.js'])
</head>
<body>
    <div class="min-vh-100 d-flex flex-column">
        <header class="border-bottom bg-white">
            <div class="container d-flex justify-content-between align-items-center py-3">
                <x-application-logo />
                <nav class="d-flex gap-2">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="btn btn-primary btn-sm">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-outline-primary btn-sm">Entrar</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="btn btn-primary btn-sm">Registrarse</a>
                        @endif
                    @endauth
                </nav>
            </div>
        </header>

        <main class="flex-grow-1 d-flex align-items-center">
            <div class="container text-center py-5">
                <h1 class="display-5 fw-bold mb-3">Gestión escolar para secundarias</h1>
                <p class="lead text-muted mb-4">
                    Control de asistencias por QR, comunicación con padres y dashboards en tiempo real.
                </p>
                <a href="{{ route('login') }}" class="btn btn-primary btn-lg">Comenzar</a>
            </div>
        </main>

        <footer class="border-top py-3 text-center text-muted small">
            &copy; {{ date('Y') }} Aulita by MyB Solutions
        </footer>
    </div>
</body>
</html>
