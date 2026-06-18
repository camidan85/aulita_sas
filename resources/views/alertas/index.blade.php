<x-app-layout>
    <x-slot name="header">
        <h1 class="h4 mb-0">Alertas de riesgo</h1>
    </x-slot>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr><th>Alumno</th><th>Grupo</th><th>Motivo</th><th>Generada</th><th>Estado</th><th class="text-end">Acción</th></tr>
                    </thead>
                    <tbody>
                        @forelse ($alertas as $alerta)
                            <tr class="{{ $alerta->atendida ? 'opacity-50' : '' }}">
                                <td>{{ $alerta->alumno->nombreCompleto() }}</td>
                                <td>{{ $alerta->alumno->grupo?->nombreCompleto() ?? '—' }}</td>
                                <td><span class="badge bg-danger-subtle text-danger-emphasis">{{ $alerta->descripcion() }}</span></td>
                                <td class="small text-muted">{{ $alerta->generada_en?->format('d/m/Y H:i') }}</td>
                                <td>
                                    @if ($alerta->atendida)
                                        <span class="badge bg-secondary">Atendida</span>
                                    @else
                                        <span class="badge bg-warning text-dark">Pendiente</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    @unless ($alerta->atendida)
                                        <form method="POST" action="{{ route('alertas.atender', $alerta) }}" class="d-inline">
                                            @csrf @method('PATCH')
                                            <button class="btn btn-sm btn-outline-success">Marcar atendida</button>
                                        </form>
                                    @endunless
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center text-muted py-4">Sin alertas de riesgo.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $alertas->links() }}
        </div>
    </div>
</x-app-layout>
