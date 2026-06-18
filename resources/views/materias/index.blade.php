<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h1 class="h4 mb-0">Materias</h1>
            <a href="{{ route('materias.create') }}" class="btn btn-primary btn-sm">Nueva materia</a>
        </div>
    </x-slot>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr><th>Clave</th><th>Nombre</th><th class="text-end">Acciones</th></tr>
                </thead>
                <tbody>
                    @forelse ($materias as $materia)
                        <tr>
                            <td class="font-monospace">{{ $materia->clave ?? '—' }}</td>
                            <td>{{ $materia->nombre }}</td>
                            <td class="text-end">
                                <a href="{{ route('materias.edit', $materia) }}" class="btn btn-sm btn-outline-primary">Editar</a>
                                <form method="POST" action="{{ route('materias.destroy', $materia) }}" class="d-inline"
                                      onsubmit="return confirm('¿Eliminar materia?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="text-center text-muted py-4">Sin materias.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
