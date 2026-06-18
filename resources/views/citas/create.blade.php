<x-app-layout>
    <x-slot name="header">
        <h1 class="h4 mb-0">Solicitar cita</h1>
    </x-slot>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('citas.store') }}">
                @csrf
                <div class="row g-3">
                    <div class="col-md-6">
                        <x-input-label for="alumno_id" value="Alumno" />
                        <select id="alumno_id" name="alumno_id" class="form-select" required>
                            @foreach ($hijos as $hijo)
                                <option value="{{ $hijo->id }}">{{ $hijo->nombreCompleto() }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('alumno_id')" />
                    </div>
                    <div class="col-md-6">
                        <x-input-label for="con_rol" value="Con" />
                        <select id="con_rol" name="con_rol" class="form-select" required>
                            @foreach ($roles as $val => $label)
                                <option value="{{ $val }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('con_rol')" />
                    </div>
                    <div class="col-md-6">
                        <x-input-label for="fecha_solicitada" value="Fecha" />
                        <x-text-input id="fecha_solicitada" name="fecha_solicitada" type="date" :value="old('fecha_solicitada')" required />
                        <x-input-error :messages="$errors->get('fecha_solicitada')" />
                    </div>
                    <div class="col-md-6">
                        <x-input-label for="hora_solicitada" value="Hora (opcional)" />
                        <x-text-input id="hora_solicitada" name="hora_solicitada" type="time" :value="old('hora_solicitada')" />
                        <x-input-error :messages="$errors->get('hora_solicitada')" />
                    </div>
                    <div class="col-12">
                        <x-input-label for="motivo" value="Motivo" />
                        <textarea id="motivo" name="motivo" rows="3" class="form-control" required>{{ old('motivo') }}</textarea>
                        <x-input-error :messages="$errors->get('motivo')" />
                    </div>
                </div>

                <div class="mt-4 d-flex gap-2">
                    <x-primary-button>Solicitar</x-primary-button>
                    <a href="{{ route('citas.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
