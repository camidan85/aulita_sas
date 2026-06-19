<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'AULITA') }}</title>
    <link rel="manifest" href="/manifest.webmanifest">
    <meta name="theme-color" content="#2563eb">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('img/aulita-icon-32.png') }}">
    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('img/aulita-icon-192.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('img/aulita-icon-192.png') }}">
    @vite(['resources/scss/app.scss', 'resources/js/app.js'])
</head>
<body>
    <div class="min-vh-100 d-flex flex-column justify-content-center align-items-center py-5">
        <a href="/" class="text-decoration-none mb-3">
            <x-application-logo height="96" />
        </a>

        <div class="card border-0 shadow-sm w-100" style="max-width: 26rem;">
            <div class="card-body p-4">
                {{ $slot }}
            </div>
        </div>

        <footer class="text-center text-muted small mt-4">
            &copy; {{ date('Y') }} Aulita <span class="opacity-75">by MyB Solutions</span>
        </footer>
    </div>
</body>
</html>
