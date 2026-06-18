<x-guest-layout>
    <div class="text-center">
        <h1 class="h5 mb-2">Revisa tu correo</h1>
        <p class="text-muted">
            Enviamos un enlace de activación a <strong>{{ $correo }}</strong>.
            Expira en 24 horas.
        </p>
        <a href="{{ route('login') }}" class="btn btn-outline-secondary btn-sm mt-2">Volver al inicio</a>
    </div>
</x-guest-layout>
