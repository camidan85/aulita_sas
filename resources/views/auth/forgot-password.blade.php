<x-guest-layout>
    <p class="text-muted small mb-3">
        {{ __('¿Olvidaste tu contraseña? Indícanos tu correo y te enviaremos un enlace para restablecerla.') }}
    </p>

    <x-auth-session-status class="mb-3" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <div class="mb-3">
            <x-input-label for="email" :value="__('Correo')" />
            <x-text-input id="email" type="email" name="email" :value="old('email')" required autofocus />
            <x-input-error :messages="$errors->get('email')" />
        </div>

        <div class="d-flex justify-content-end">
            <x-primary-button>{{ __('Enviar enlace') }}</x-primary-button>
        </div>
    </form>
</x-guest-layout>
