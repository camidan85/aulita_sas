<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h1 class="h4 mb-0">Tutores</h1>
            <a href="{{ route('tutores.create') }}" class="btn btn-primary btn-sm">Nuevo tutor</a>
        </div>
    </x-slot>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr><th>Nombre</th><th>Parentesco</th><th>Teléfono</th><th>Correo</th><th class="text-end">Acciones</th></tr>
                </thead>
                <tbody>
                    @forelse ($tutores as $tutor)
                        <tr>
                            <td>{{ $tutor->nombre }}</td>
                            <td>{{ $tutor->parentesco ?? '—' }}</td>
                            <td>{{ $tutor->telefono ?? '—' }}</td>
                            <td>{{ $tutor->correo ?? '—' }}</td>
                            <td class="text-end">
                                <a href="{{ route('tutores.edit', $tutor) }}" class="btn btn-sm btn-outline-primary">Editar</a>
                                <form method="POST" action="{{ route('tutores.destroy', $tutor) }}" class="d-inline"
                                      onsubmit="return confirm('¿Eliminar tutor?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted py-4">Sin tutores.</td></tr>
                    @endforelse
                </tbody>
            </table>
            <div class="mt-3">{{ $tutores->links() }}</div>
        </div>
    </div>
</x-app-layout>
