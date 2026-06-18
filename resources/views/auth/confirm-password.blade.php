<x-guest-layout>
    <p class="text-muted small mb-3">
        {{ __('Esta es una zona segura. Confirma tu contraseña para continuar.') }}
    </p>

    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf

        <div class="mb-3">
            <x-input-label for="password" :value="__('Contraseña')" />
            <x-text-input id="password" type="password" name="password" required autocomplete="current-password" autofocus />
            <x-input-error :messages="$errors->get('password')" />
        </div>

        <div class="d-flex justify-content-end">
            <x-primary-button>{{ __('Confirmar') }}</x-primary-button>
        </div>
    </form>
</x-guest-layout>
