<div class="row g-3">
    <div class="col-md-8">
        <x-input-label for="nombre" value="Nombre completo" />
        <x-text-input id="nombre" name="nombre" :value="old('nombre', $tutor->nombre)" required />
        <x-input-error :messages="$errors->get('nombre')" />
    </div>
    <div class="col-md-4">
        <x-input-label for="parentesco" value="Parentesco" />
        <x-text-input id="parentesco" name="parentesco" :value="old('parentesco', $tutor->parentesco)" placeholder="Padre / Madre / Tutor" />
        <x-input-error :messages="$errors->get('parentesco')" />
    </div>
    <div class="col-md-6">
        <x-input-label for="telefono" value="Teléfono (WhatsApp, E.164)" />
        <x-text-input id="telefono" name="telefono" :value="old('telefono', $tutor->telefono)" placeholder="521..." />
        <x-input-error :messages="$errors->get('telefono')" />
    </div>
    <div class="col-md-6">
        <x-input-label for="correo" value="Correo" />
        <x-text-input id="correo" name="correo" type="email" :value="old('correo', $tutor->correo)" />
        <x-input-error :messages="$errors->get('correo')" />
    </div>
</div>
