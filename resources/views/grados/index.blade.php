<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h1 class="h4 mb-0">Grados</h1>
            <a href="{{ route('grados.create') }}" class="btn btn-primary btn-sm">Nuevo grado</a>
        </div>
    </x-slot>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr><th>Nivel</th><th>Nombre</th><th>Grupos</th><th class="text-end">Acciones</th></tr>
                </thead>
                <tbody>
                    @forelse ($grados as $grado)
                        <tr>
                            <td>{{ $grado->nivel }}</td>
                            <td>{{ $grado->nombre }}</td>
                            <td>{{ $grado->grupos_count }}</td>
                            <td class="text-end">
                                <a href="{{ route('grados.edit', $grado) }}" class="btn btn-sm btn-outline-primary">Editar</a>
                                <form method="POST" action="{{ route('grados.destroy', $grado) }}" class="d-inline"
                                      onsubmit="return confirm('¿Eliminar grado?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center text-muted py-4">Sin grados.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
