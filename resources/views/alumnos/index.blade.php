<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h1 class="h4 mb-0">Alumnos</h1>
            @can('alumnos.crear')
                <a href="{{ route('alumnos.create') }}" class="btn btn-primary btn-sm">Nuevo alumno</a>
            @endcan
        </div>
    </x-slot>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form method="GET" class="mb-3">
                <div class="input-group" style="max-width: 28rem;">
                    <input type="text" name="q" value="{{ $q }}" class="form-control"
                           placeholder="Buscar por nombre, matrícula o CURP">
                    <button class="btn btn-outline-secondary" type="submit">Buscar</button>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Matrícula</th>
                            <th>Nombre</th>
                            <th>Grupo</th>
                            <th>Estatus</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($alumnos as $alumno)
                            <tr>
                                <td class="font-monospace">{{ $alumno->matricula }}</td>
                                <td>{{ $alumno->nombreCompleto() }}</td>
                                <td>{{ $alumno->grupo?->nombreCompleto() ?? '—' }}</td>
                                <td>
                                    <span class="badge bg-{{ $alumno->estatus === 'activo' ? 'success' : 'secondary' }}">
                                        {{ ucfirst($alumno->estatus) }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('alumnos.show', $alumno) }}" class="btn btn-sm btn-outline-secondary">Ver</a>
                                    @can('alumnos.editar')
                                        <a href="{{ route('alumnos.edit', $alumno) }}" class="btn btn-sm btn-outline-primary">Editar</a>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">Sin alumnos.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $alumnos->links() }}
        </div>
    </div>
</x-app-layout>
