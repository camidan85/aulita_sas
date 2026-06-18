<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h1 class="h4 mb-0">Escuelas (Super Admin)</h1>
            <a href="{{ route('admin.escuelas.create') }}" class="btn btn-primary btn-sm">Nueva escuela</a>
        </div>
    </x-slot>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr><th>Escuela</th><th>Usuarios</th><th>Formato QR</th><th>Estatus</th><th class="text-end">Acción</th></tr>
                </thead>
                <tbody>
                    @forelse ($escuelas as $e)
                        <tr>
                            <td>
                                <div class="fw-semibold">{{ $e->nombre }}</div>
                                <div class="small text-muted">{{ $e->slug }}</div>
                            </td>
                            <td>{{ $e->users_count }}</td>
                            <td class="font-monospace small">{{ $e->qr_formato }}</td>
                            <td>
                                <span class="badge bg-{{ $e->estatus === 'activa' ? 'success' : 'secondary' }}">{{ ucfirst($e->estatus) }}</span>
                            </td>
                            <td class="text-end">
                                <a href="{{ route('admin.escuelas.edit', $e) }}" class="btn btn-sm btn-outline-primary">Configurar</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted py-4">Sin escuelas.</td></tr>
                    @endforelse
                </tbody>
            </table>
            <div class="mt-3">{{ $escuelas->links() }}</div>
        </div>
    </div>
</x-app-layout>
