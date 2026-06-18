<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h1 class="h4 mb-0">Citas</h1>
            @can('portal.ver')
                <a href="{{ route('citas.create') }}" class="btn btn-primary btn-sm">Solicitar cita</a>
            @endcan
        </div>
    </x-slot>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr><th>Fecha</th><th>Alumno</th><th>Con</th><th>Motivo</th><th>Estatus</th>@can('citas.gestionar')<th></th>@endcan</tr>
                </thead>
                <tbody>
                    @forelse ($citas as $cita)
                        <tr>
                            <td>{{ $cita->fecha_solicitada->format('d/m/Y') }} {{ $cita->hora_solicitada }}</td>
                            <td>{{ $cita->alumno->nombreCompleto() }}</td>
                            <td class="text-capitalize">{{ $cita->con_rol }}</td>
                            <td class="small">{{ \Illuminate\Support\Str::limit($cita->motivo, 50) }}</td>
                            <td>
                                @php($map = ['solicitada' => 'secondary', 'confirmada' => 'success', 'reprogramada' => 'info', 'cancelada' => 'danger', 'atendida' => 'primary'])
                                <span class="badge bg-{{ $map[$cita->estatus] ?? 'secondary' }}">{{ ucfirst($cita->estatus) }}</span>
                            </td>
                            @can('citas.gestionar')
                                <td class="text-end">
                                    <form method="POST" action="{{ route('citas.estatus', $cita) }}" class="d-inline">
                                        @csrf @method('PATCH')
                                        <div class="input-group input-group-sm" style="width: 12rem;">
                                            <select name="estatus" class="form-select form-select-sm">
                                                @foreach (['solicitada', 'confirmada', 'reprogramada', 'cancelada', 'atendida'] as $e)
                                                    <option value="{{ $e }}" @selected($cita->estatus === $e)>{{ ucfirst($e) }}</option>
                                                @endforeach
                                            </select>
                                            <button class="btn btn-outline-secondary">OK</button>
                                        </div>
                                    </form>
                                </td>
                            @endcan
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted py-4">Sin citas.</td></tr>
                    @endforelse
                </tbody>
            </table>
            <div class="mt-3">{{ $citas->links() }}</div>
        </div>
    </div>
</x-app-layout>
