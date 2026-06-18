<div class="row g-3">
    <div class="col-md-4">
        <x-input-label for="clave" value="Clave" />
        <x-text-input id="clave" name="clave" :value="old('clave', $materia->clave)" />
        <x-input-error :messages="$errors->get('clave')" />
    </div>
    <div class="col-md-8">
        <x-input-label for="nombre" value="Nombre" />
        <x-text-input id="nombre" name="nombre" :value="old('nombre', $materia->nombre)" required />
        <x-input-error :messages="$errors->get('nombre')" />
    </div>
</div>
