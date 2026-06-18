<x-app-layout>
    <x-slot name="header">
        <h1 class="h4 mb-0">Nuevo reporte</h1>
    </x-slot>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('reportes.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="row g-3">
                    <div class="col-md-6">
                        <x-input-label for="alumno_id" value="Alumno" />
                        <select id="alumno_id" name="alumno_id" class="form-select" required>
                            <option value="">Selecciona…</option>
                            @foreach ($alumnos as $a)
                                <option value="{{ $a->id }}" @selected(old('alumno_id', $alumnoSeleccionado) == $a->id)>{{ $a->nombreCompleto() }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('alumno_id')" />
                    </div>
                    <div class="col-md-6">
                        <x-input-label for="tipo" value="Tipo" />
                        <select id="tipo" name="tipo" class="form-select" required>
                            @foreach ($tipos as $val => $label)
                                <option value="{{ $val }}" @selected(old('tipo') === $val)>{{ $label }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('tipo')" />
                    </div>
                    <div class="col-12">
                        <x-input-label for="descripcion" value="Descripción" />
                        <textarea id="descripcion" name="descripcion" rows="4" class="form-control" required>{{ old('descripcion') }}</textarea>
                        <x-input-error :messages="$errors->get('descripcion')" />
                    </div>
                    <div class="col-12">
                        <x-input-label for="evidencias" value="Evidencias (imagen, PDF, documento, video — máx. 6)" />
                        <input id="evidencias" name="evidencias[]" type="file" class="form-control" multiple
                               accept=".jpg,.jpeg,.png,.webp,.pdf,.doc,.docx,.mp4,.mov">
                        <x-input-error :messages="$errors->get('evidencias')" />
                        <x-input-error :messages="$errors->get('evidencias.0')" />
                    </div>
                    <div class="col-12">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="requiere_firma" name="requiere_firma" value="1" @checked(old('requiere_firma'))>
                            <label class="form-check-label" for="requiere_firma">Requiere firma de enterado del padre</label>
                        </div>
                    </div>
                </div>

                <div class="mt-4 d-flex gap-2">
                    <x-primary-button>Guardar</x-primary-button>
                    <a href="{{ route('reportes.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
