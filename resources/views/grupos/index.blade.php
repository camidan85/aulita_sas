<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h1 class="h4 mb-0">Grupos</h1>
            <a href="{{ route('grupos.create') }}" class="btn btn-primary btn-sm">Nuevo grupo</a>
        </div>
    </x-slot>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr><th>Grupo</th><th>Ciclo</th><th>Titular</th><th>Alumnos</th><th class="text-end">Acciones</th></tr>
                </thead>
                <tbody>
                    @forelse ($grupos as $grupo)
                        <tr>
                            <td class="fw-semibold">{{ $grupo->nombreCompleto() }}</td>
                            <td>{{ $grupo->ciclo?->nombre ?? '—' }}</td>
                            <td>{{ $grupo->docenteTitular?->nombreCompleto() ?? '—' }}</td>
                            <td>{{ $grupo->alumnos_count }}</td>
                            <td class="text-end">
                                <a href="{{ route('grupos.edit', $grupo) }}" class="btn btn-sm btn-outline-primary">Editar</a>
                                <form method="POST" action="{{ route('grupos.destroy', $grupo) }}" class="d-inline"
                                      onsubmit="return confirm('¿Eliminar grupo?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted py-4">Sin grupos.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
