<x-guest-layout>
    <h1 class="h5 mb-1">Crea tu contraseña</h1>
    <p class="text-muted small mb-3">Hola {{ $activation->nombre }}, define tu contraseña de acceso.</p>

    <form method="POST" action="{{ route('activar.guardar', $activation->token) }}">
        @csrf

        <div class="mb-3">
            <x-input-label for="password" value="Contraseña" />
            <x-text-input id="password" name="password" type="password" required autofocus autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" />
            <div class="form-text">Mínimo 8 caracteres, con mayúscula, minúscula, número y carácter especial.</div>
        </div>

        <div class="mb-3">
            <x-input-label for="password_confirmation" value="Confirmar contraseña" />
            <x-text-input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password" />
        </div>

        <div class="d-flex justify-content-end">
            <x-primary-button>Activar cuenta</x-primary-button>
        </div>
    </form>
</x-guest-layout>
