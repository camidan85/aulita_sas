<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'AULITA') }}</title>
    <link rel="manifest" href="/manifest.webmanifest">
    <meta name="theme-color" content="#2563eb">
    <link rel="apple-touch-icon" href="/icons/aulita.svg">
    @vite(['resources/scss/app.scss', 'resources/js/app.js'])
</head>
<body>
    <div class="min-vh-100 d-flex flex-column justify-content-center align-items-center py-5">
        <a href="/" class="text-decoration-none mb-3">
            <x-application-logo />
        </a>

        <div class="card border-0 shadow-sm w-100" style="max-width: 26rem;">
            <div class="card-body p-4">
                {{ $slot }}
            </div>
        </div>
    </div>
</body>
</html>
