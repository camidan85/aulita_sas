<x-guest-layout>
    <h1 class="h5 mb-1">Activar cuenta de padre</h1>
    <p class="text-muted small mb-3">Verifica los datos de tu hijo(a) para activar tu acceso.</p>

    <form method="POST" action="{{ route('activar.enviar') }}">
        @csrf

        <div class="mb-3">
            <x-input-label for="curp" value="CURP del alumno" />
            <x-text-input id="curp" name="curp" :value="old('curp')" maxlength="18" required autofocus />
            <x-input-error :messages="$errors->get('curp')" />
        </div>

        <div class="mb-3">
            <x-input-label for="apellido_paterno" value="Apellido paterno del alumno" />
            <x-text-input id="apellido_paterno" name="apellido_paterno" :value="old('apellido_paterno')" required />
            <x-input-error :messages="$errors->get('apellido_paterno')" />
        </div>

        <hr>

        <div class="mb-3">
            <x-input-label for="nombre" value="Tu nombre" />
            <x-text-input id="nombre" name="nombre" :value="old('nombre')" required />
            <x-input-error :messages="$errors->get('nombre')" />
        </div>

        <div class="mb-3">
            <x-input-label for="correo" value="Tu correo" />
            <x-text-input id="correo" name="correo" type="email" :value="old('correo')" required />
            <x-input-error :messages="$errors->get('correo')" />
        </div>

        <div class="mb-3">
            <x-input-label for="telefono" value="Tu teléfono (WhatsApp)" />
            <x-text-input id="telefono" name="telefono" :value="old('telefono')" placeholder="521..." />
            <x-input-error :messages="$errors->get('telefono')" />
        </div>

        <div class="d-flex justify-content-between align-items-center">
            <a class="small text-decoration-none" href="{{ route('login') }}">Ya tengo cuenta</a>
            <x-primary-button>Continuar</x-primary-button>
        </div>
    </form>
</x-guest-layout>
