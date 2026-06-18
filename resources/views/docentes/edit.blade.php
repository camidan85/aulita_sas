<x-app-layout>
    <x-slot name="header"><h1 class="h4 mb-0">Editar docente</h1></x-slot>
    <div class="card border-0 shadow-sm"><div class="card-body">
        <form method="POST" action="{{ route('docentes.update', $docente) }}">
            @csrf @method('PUT')
            @include('docentes._form')
            <div class="mt-4 d-flex gap-2">
                <x-primary-button>Actualizar</x-primary-button>
                <a href="{{ route('docentes.index') }}" class="btn btn-outline-secondary">Cancelar</a>
            </div>
        </form>
    </div></div>
</x-app-layout>
