<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h1 class="h4 mb-0">Avisos</h1>
            @can('avisos.crear')
                <a href="{{ route('avisos.create') }}" class="btn btn-primary btn-sm">Nuevo aviso</a>
            @endcan
        </div>
    </x-slot>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr><th>Fecha</th><th>Título</th><th>Alcance</th><th>Publicó</th><th></th></tr>
                </thead>
                <tbody>
                    @forelse ($avisos as $aviso)
                        <tr>
                            <td>{{ $aviso->fecha_publicacion->format('d/m/Y H:i') }}</td>
                            <td>{{ $aviso->titulo }}</td>
                            <td><span class="badge bg-light text-dark">{{ $aviso->alcanceLabel() }}</span></td>
                            <td>{{ $aviso->publicadoPor->name }}</td>
                            <td class="text-end">
                                <a href="{{ route('avisos.show', $aviso) }}" class="btn btn-sm btn-outline-secondary">Ver</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted py-4">Sin avisos.</td></tr>
                    @endforelse
                </tbody>
            </table>
            <div class="mt-3">{{ $avisos->links() }}</div>
        </div>
    </div>
</x-app-layout>
