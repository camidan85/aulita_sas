@php($e = $escuela ?? null)
@php($ocultos = $e?->modulos_ocultos ?? [])

<div class="row g-3">
    <div class="col-md-8">
        <x-input-label for="nombre" value="Nombre de la escuela" />
        <x-text-input id="nombre" name="nombre" :value="old('nombre', $e?->nombre)" required />
        <x-input-error :messages="$errors->get('nombre')" />
    </div>
    <div class="col-md-4">
        <x-input-label for="cct" value="CCT" />
        <x-text-input id="cct" name="cct" :value="old('cct', $e?->cct)" />
    </div>

    <div class="col-md-4">
        <x-input-label for="telefono" value="Teléfono" />
        <x-text-input id="telefono" name="telefono" :value="old('telefono', $e?->telefono)" />
    </div>
    <div class="col-md-4">
        <x-input-label for="correo" value="Correo" />
        <x-text-input id="correo" name="correo" type="email" :value="old('correo', $e?->correo)" />
    </div>
    <div class="col-md-4">
        <x-input-label for="estatus" value="Estatus" />
        <select id="estatus" name="estatus" class="form-select">
            @foreach (['activa', 'suspendida', 'baja'] as $s)
                <option value="{{ $s }}" @selected(old('estatus', $e?->estatus ?? 'activa') === $s)>{{ ucfirst($s) }}</option>
            @endforeach
        </select>
    </div>

    <div class="col-md-3">
        <x-input-label for="hora_corte_faltas" value="Hora de corte de faltas" />
        <x-text-input id="hora_corte_faltas" name="hora_corte_faltas" type="time"
                      :value="old('hora_corte_faltas', $e ? substr($e->hora_corte_faltas, 0, 5) : '07:15')" required />
        <x-input-error :messages="$errors->get('hora_corte_faltas')" />
    </div>
    <div class="col-md-5">
        <x-input-label for="timezone" value="Zona horaria" />
        <x-text-input id="timezone" name="timezone" :value="old('timezone', $e?->timezone ?? 'America/Mexico_City')" required />
    </div>
    <div class="col-md-4">
        <x-input-label for="umbral_riesgo_calif" value="Umbral aprobatorio" />
        <x-text-input id="umbral_riesgo_calif" name="umbral_riesgo_calif" type="number" step="0.01" min="0" max="10"
                      :value="old('umbral_riesgo_calif', $e?->umbral_riesgo_calif ?? '6.00')" required />
    </div>

    <div class="col-12">
        <x-input-label for="qr_formato" value="Plantilla del contenido del QR" />
        <x-text-input id="qr_formato" name="qr_formato" :value="old('qr_formato', $e?->qr_formato ?? '{matricula}')" required />
        <div class="form-text">
            Variables disponibles: <code>{matricula}</code>, <code>{curp}</code>, <code>{id}</code>,
            <code>{nombre}</code>, <code>{apellido_paterno}</code>. Ej: <code>{matricula}</code> o <code>ESC-{matricula}</code>.
            Al cambiarla se regenera el QR de todos los alumnos.
        </div>
        <x-input-error :messages="$errors->get('qr_formato')" />
    </div>

    <div class="col-12">
        <label class="form-label fw-semibold">Módulos visibles para esta escuela</label>
        <div class="row g-2">
            @foreach ($modulos as $clave => $etiqueta)
                <div class="col-md-3 col-6">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="mod_{{ $clave }}"
                               name="modulos[{{ $clave }}]" value="1"
                               @checked(! in_array($clave, $ocultos, true))>
                        <label class="form-check-label" for="mod_{{ $clave }}">{{ $etiqueta }}</label>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="form-text">Desmarca un módulo para ocultarlo (su menú y rutas dejan de estar disponibles para la escuela).</div>
    </div>
</div>
