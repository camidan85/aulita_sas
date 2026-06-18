<div class="row g-3">
    <div class="col-md-4">
        <x-input-label for="numero_empleado" value="Número de empleado" />
        <x-text-input id="numero_empleado" name="numero_empleado" :value="old('numero_empleado', $docente->numero_empleado)" />
        <x-input-error :messages="$errors->get('numero_empleado')" />
    </div>
    <div class="col-md-4">
        <x-input-label for="nombre" value="Nombre(s)" />
        <x-text-input id="nombre" name="nombre" :value="old('nombre', $docente->nombre)" required />
        <x-input-error :messages="$errors->get('nombre')" />
    </div>
    <div class="col-md-4">
        <x-input-label for="telefono" value="Teléfono" />
        <x-text-input id="telefono" name="telefono" :value="old('telefono', $docente->telefono)" />
        <x-input-error :messages="$errors->get('telefono')" />
    </div>
    <div class="col-md-4">
        <x-input-label for="apellido_paterno" value="Apellido paterno" />
        <x-text-input id="apellido_paterno" name="apellido_paterno" :value="old('apellido_paterno', $docente->apellido_paterno)" required />
        <x-input-error :messages="$errors->get('apellido_paterno')" />
    </div>
    <div class="col-md-4">
        <x-input-label for="apellido_materno" value="Apellido materno" />
        <x-text-input id="apellido_materno" name="apellido_materno" :value="old('apellido_materno', $docente->apellido_materno)" />
        <x-input-error :messages="$errors->get('apellido_materno')" />
    </div>
    <div class="col-md-4">
        <x-input-label for="estatus" value="Estatus" />
        <select id="estatus" name="estatus" class="form-select">
            @foreach (['activo', 'inactivo'] as $val)
                <option value="{{ $val }}" @selected(old('estatus', $docente->estatus ?? 'activo') === $val)>{{ ucfirst($val) }}</option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('estatus')" />
    </div>
</div>
