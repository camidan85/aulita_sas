<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h1 class="h4 mb-0">Asistencias</h1>
            @can('asistencias.registrar')
                <a href="{{ route('asistencias.escanear') }}" class="btn btn-primary btn-sm">Escanear QR</a>
            @endcan
        </div>
    </x-slot>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form method="GET" class="mb-3">
                <div class="input-group" style="max-width: 18rem;">
                    <input type="date" name="fecha" value="{{ $fecha }}" class="form-control">
                    <button class="btn btn-outline-secondary" type="submit">Ver</button>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr><th>Hora</th><th>Alumno</th><th>Grupo</th><th>Estatus</th><th>Origen</th><th>Registró</th></tr>
                    </thead>
                    <tbody>
                        @forelse ($asistencias as $a)
                            <tr>
                                <td>{{ $a->hora ?? '—' }}</td>
                                <td>{{ $a->alumno->nombreCompleto() }}</td>
                                <td>{{ $a->alumno->grupo?->nombreCompleto() ?? '—' }}</td>
                                <td>
                                    @php($map = ['presente' => 'success', 'retardo' => 'warning', 'falta' => 'danger', 'falta_pendiente' => 'secondary', 'justificada' => 'info'])
                                    <span class="badge bg-{{ $map[$a->estatus] ?? 'secondary' }}">{{ str_replace('_', ' ', ucfirst($a->estatus)) }}</span>
                                </td>
                                <td class="text-capitalize">{{ $a->origen }}</td>
                                <td>{{ $a->registradoPor?->name ?? 'Sistema' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center text-muted py-4">Sin registros para esta fecha.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $asistencias->links() }}
        </div>
    </div>
</x-app-layout>
