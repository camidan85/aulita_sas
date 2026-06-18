@php($a = $alumno ?? null)

<div class="row g-3">
    <div class="col-md-4">
        <x-input-label for="matricula" value="Matrícula" />
        <x-text-input id="matricula" name="matricula" :value="old('matricula', $a?->matricula)" required />
        <x-input-error :messages="$errors->get('matricula')" />
    </div>
    <div class="col-md-8">
        <x-input-label for="curp" value="CURP" />
        <x-text-input id="curp" name="curp" :value="old('curp', $a?->curp)" maxlength="18" required />
        <x-input-error :messages="$errors->get('curp')" />
    </div>

    <div class="col-md-4">
        <x-input-label for="nombre" value="Nombre(s)" />
        <x-text-input id="nombre" name="nombre" :value="old('nombre', $a?->nombre)" required />
        <x-input-error :messages="$errors->get('nombre')" />
    </div>
    <div class="col-md-4">
        <x-input-label for="apellido_paterno" value="Apellido paterno" />
        <x-text-input id="apellido_paterno" name="apellido_paterno" :value="old('apellido_paterno', $a?->apellido_paterno)" required />
        <x-input-error :messages="$errors->get('apellido_paterno')" />
    </div>
    <div class="col-md-4">
        <x-input-label for="apellido_materno" value="Apellido materno" />
        <x-text-input id="apellido_materno" name="apellido_materno" :value="old('apellido_materno', $a?->apellido_materno)" />
        <x-input-error :messages="$errors->get('apellido_materno')" />
    </div>

    <div class="col-md-3">
        <x-input-label for="fecha_nacimiento" value="Fecha de nacimiento" />
        <x-text-input id="fecha_nacimiento" name="fecha_nacimiento" type="date"
                      :value="old('fecha_nacimiento', $a?->fecha_nacimiento?->format('Y-m-d'))" />
        <x-input-error :messages="$errors->get('fecha_nacimiento')" />
    </div>
    <div class="col-md-3">
        <x-input-label for="sexo" value="Sexo" />
        <select id="sexo" name="sexo" class="form-select">
            <option value="">—</option>
            @foreach (['M' => 'Masculino', 'F' => 'Femenino', 'X' => 'Otro'] as $val => $label)
                <option value="{{ $val }}" @selected(old('sexo', $a?->sexo) === $val)>{{ $label }}</option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('sexo')" />
    </div>
    <div class="col-md-6">
        <x-input-label for="grupo_id" value="Grupo" />
        <select id="grupo_id" name="grupo_id" class="form-select">
            <option value="">Sin asignar</option>
            @foreach ($grupos as $grupo)
                <option value="{{ $grupo->id }}" @selected(old('grupo_id', $a?->grupo_id) == $grupo->id)>
                    {{ $grupo->nombreCompleto() }}
                </option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('grupo_id')" />
    </div>

    <div class="col-md-4">
        <x-input-label for="correo" value="Correo" />
        <x-text-input id="correo" name="correo" type="email" :value="old('correo', $a?->correo)" />
        <x-input-error :messages="$errors->get('correo')" />
    </div>
    <div class="col-md-4">
        <x-input-label for="telefono" value="Teléfono" />
        <x-text-input id="telefono" name="telefono" :value="old('telefono', $a?->telefono)" />
        <x-input-error :messages="$errors->get('telefono')" />
    </div>
    <div class="col-md-4">
        <x-input-label for="estatus" value="Estatus" />
        <select id="estatus" name="estatus" class="form-select">
            @foreach (['activo', 'baja', 'egresado', 'suspendido'] as $val)
                <option value="{{ $val }}" @selected(old('estatus', $a?->estatus ?? 'activo') === $val)>{{ ucfirst($val) }}</option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('estatus')" />
    </div>
</div>
