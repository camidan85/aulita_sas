<x-app-layout>
    <x-slot name="header"><h1 class="h4 mb-0">Nuevo grado</h1></x-slot>
    <div class="card border-0 shadow-sm"><div class="card-body">
        <form method="POST" action="{{ route('grados.store') }}">
            @csrf
            @include('grados._form')
            <div class="mt-4 d-flex gap-2">
                <x-primary-button>Guardar</x-primary-button>
                <a href="{{ route('grados.index') }}" class="btn btn-outline-secondary">Cancelar</a>
            </div>
        </form>
    </div></div>
</x-app-layout>
