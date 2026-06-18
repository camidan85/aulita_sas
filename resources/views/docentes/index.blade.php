<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h1 class="h4 mb-0">Docentes</h1>
            <a href="{{ route('docentes.create') }}" class="btn btn-primary btn-sm">Nuevo docente</a>
        </div>
    </x-slot>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr><th># Empleado</th><th>Nombre</th><th>Teléfono</th><th>Estatus</th><th class="text-end">Acciones</th></tr>
                </thead>
                <tbody>
                    @forelse ($docentes as $docente)
                        <tr>
                            <td class="font-monospace">{{ $docente->numero_empleado ?? '—' }}</td>
                            <td>{{ $docente->nombreCompleto() }}</td>
                            <td>{{ $docente->telefono ?? '—' }}</td>
                            <td>
                                <span class="badge bg-{{ $docente->estatus === 'activo' ? 'success' : 'secondary' }}">
                                    {{ ucfirst($docente->estatus) }}
                                </span>
                            </td>
                            <td class="text-end">
                                <a href="{{ route('docentes.edit', $docente) }}" class="btn btn-sm btn-outline-primary">Editar</a>
                                <form method="POST" action="{{ route('docentes.destroy', $docente) }}" class="d-inline"
                                      onsubmit="return confirm('¿Eliminar docente?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted py-4">Sin docentes.</td></tr>
                    @endforelse
                </tbody>
            </table>
            <div class="mt-3">{{ $docentes->links() }}</div>
        </div>
    </div>
</x-app-layout>
