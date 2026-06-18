<x-app-layout>
    <x-slot name="header">
        <h1 class="h4 mb-0">Editar alumno</h1>
    </x-slot>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('alumnos.update', $alumno) }}">
                @csrf
                @method('PUT')
                @include('alumnos._form')

                <div class="mt-4 d-flex justify-content-between">
                    <div class="d-flex gap-2">
                        <x-primary-button>Actualizar</x-primary-button>
                        <a href="{{ route('alumnos.show', $alumno) }}" class="btn btn-outline-secondary">Cancelar</a>
                    </div>

                    @can('alumnos.eliminar')
                        <form method="POST" action="{{ route('alumnos.destroy', $alumno) }}"
                              onsubmit="return confirm('¿Dar de baja a este alumno?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger">Dar de baja</button>
                        </form>
                    @endcan
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
