<x-guest-layout>
    <p class="text-muted small mb-3">
        {{ __('Gracias por registrarte. Antes de empezar, verifica tu correo dando clic en el enlace que te enviamos. Si no lo recibiste, te enviamos otro con gusto.') }}
    </p>

    @if (session('status') == 'verification-link-sent')
        <div class="alert alert-success">
            {{ __('Se envió un nuevo enlace de verificación al correo de tu registro.') }}
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <x-primary-button>{{ __('Reenviar verificación') }}</x-primary-button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn btn-link text-muted small text-decoration-none">
                {{ __('Cerrar sesión') }}
            </button>
        </form>
    </div>
</x-guest-layout>
