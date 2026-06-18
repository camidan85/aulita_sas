<div class="row g-3">
    <div class="col-md-4">
        <x-input-label for="grado_id" value="Grado" />
        <select id="grado_id" name="grado_id" class="form-select" required>
            <option value="">Selecciona…</option>
            @foreach ($grados as $grado)
                <option value="{{ $grado->id }}" @selected(old('grado_id', $grupo->grado_id) == $grado->id)>{{ $grado->nombre }}</option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('grado_id')" />
    </div>
    <div class="col-md-2">
        <x-input-label for="nombre" value="Letra" />
        <x-text-input id="nombre" name="nombre" :value="old('nombre', $grupo->nombre)" placeholder="A" required />
        <x-input-error :messages="$errors->get('nombre')" />
    </div>
    <div class="col-md-6">
        <x-input-label for="ciclo_id" value="Ciclo escolar" />
        <select id="ciclo_id" name="ciclo_id" class="form-select" required>
            <option value="">Selecciona…</option>
            @foreach ($ciclos as $ciclo)
                <option value="{{ $ciclo->id }}" @selected(old('ciclo_id', $grupo->ciclo_id) == $ciclo->id)>
                    {{ $ciclo->nombre }}@if ($ciclo->vigente) (vigente) @endif
                </option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('ciclo_id')" />
    </div>
    <div class="col-md-6">
        <x-input-label for="docente_titular_id" value="Docente titular" />
        <select id="docente_titular_id" name="docente_titular_id" class="form-select">
            <option value="">Sin asignar</option>
            @foreach ($docentes as $docente)
                <option value="{{ $docente->id }}" @selected(old('docente_titular_id', $grupo->docente_titular_id) == $docente->id)>
                    {{ $docente->nombreCompleto() }}
                </option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('docente_titular_id')" />
    </div>
</div>
