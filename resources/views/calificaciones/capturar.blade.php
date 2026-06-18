<x-app-layout>
    <x-slot name="header">
        <h1 class="h4 mb-0">Capturar · {{ $materia->nombre }}</h1>
    </x-slot>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <p class="text-muted small">
                {{ $grupo->nombreCompleto() }} · {{ str_replace('_', ' ', $periodo->nombre) }} · {{ $materia->nombre }}
            </p>

            <form method="POST" action="{{ route('calificaciones.guardar') }}">
                @csrf
                <input type="hidden" name="grupo_id" value="{{ $grupo->id }}">
                <input type="hidden" name="materia_id" value="{{ $materia->id }}">
                <input type="hidden" name="periodo_id" value="{{ $periodo->id }}">

                <table class="table align-middle">
                    <thead><tr><th>Matrícula</th><th>Alumno</th><th style="width: 8rem;">Calificación</th></tr></thead>
                    <tbody>
                        @forelse ($alumnos as $alumno)
                            <tr>
                                <td class="font-monospace">{{ $alumno->matricula }}</td>
                                <td>{{ $alumno->nombreCompleto() }}</td>
                                <td>
                                    <input type="number" step="0.01" min="0" max="10"
                                           name="calificaciones[{{ $alumno->id }}]"
                                           value="{{ old('calificaciones.'.$alumno->id, $existentes[$alumno->id] ?? '') }}"
                                           class="form-control form-control-sm">
                                    <x-input-error :messages="$errors->get('calificaciones.'.$alumno->id)" />
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="text-center text-muted py-4">El grupo no tiene alumnos.</td></tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="d-flex gap-2">
                    <x-primary-button>Guardar</x-primary-button>
                    <a href="{{ route('calificaciones.index', ['grupo_id' => $grupo->id, 'periodo_id' => $periodo->id]) }}" class="btn btn-outline-secondary">Volver</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
