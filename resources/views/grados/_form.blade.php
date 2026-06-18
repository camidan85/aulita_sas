<div class="row g-3">
    <div class="col-md-6">
        <x-input-label for="nivel" value="Nivel (1-3)" />
        <x-text-input id="nivel" name="nivel" type="number" min="1" max="3" :value="old('nivel', $grado->nivel)" required />
        <x-input-error :messages="$errors->get('nivel')" />
    </div>
    <div class="col-md-6">
        <x-input-label for="nombre" value="Nombre" />
        <x-text-input id="nombre" name="nombre" :value="old('nombre', $grado->nombre)" placeholder="1°" required />
        <x-input-error :messages="$errors->get('nombre')" />
    </div>
</div>
