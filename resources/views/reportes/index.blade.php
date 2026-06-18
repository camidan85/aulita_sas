<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h1 class="h4 mb-0">Reportes de conducta</h1>
            @can('reportes.crear')
                <a href="{{ route('reportes.create') }}" class="btn btn-primary btn-sm">Nuevo reporte</a>
            @endcan
        </div>
    </x-slot>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr><th>Fecha</th><th>Alumno</th><th>Tipo</th><th>Profesor</th><th>Firma</th><th></th></tr>
                </thead>
                <tbody>
                    @forelse ($reportes as $reporte)
                        <tr>
                            <td>{{ $reporte->fecha->format('d/m/Y') }}</td>
                            <td>{{ $reporte->alumno->nombreCompleto() }}</td>
                            <td>{{ $reporte->tipoLabel() }}</td>
                            <td>{{ $reporte->profesor->name }}</td>
                            <td>
                                @if ($reporte->requiere_firma)
                                    <span class="badge bg-warning text-dark">Requiere firma</span>
                                @else
                                    <span class="text-muted small">—</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <a href="{{ route('reportes.show', $reporte) }}" class="btn btn-sm btn-outline-secondary">Ver</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted py-4">Sin reportes.</td></tr>
                    @endforelse
                </tbody>
            </table>
            <div class="mt-3">{{ $reportes->links() }}</div>
        </div>
    </div>
</x-app-layout>
