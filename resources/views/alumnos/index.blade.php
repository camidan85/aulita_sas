<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h1 class="h4 mb-0">Alumnos</h1>
            @can('alumnos.crear')
                <div class="d-flex gap-2">
                    <a href="{{ route('alumnos.plantilla') }}" class="btn btn-outline-success btn-sm">Plantilla Excel</a>
                    <button class="btn btn-outline-primary btn-sm" type="button" data-bs-toggle="collapse" data-bs-target="#importBox">Importar Excel</button>
                    <a href="{{ route('alumnos.create') }}" class="btn btn-primary btn-sm">Nuevo alumno</a>
                </div>
            @endcan
        </div>
    </x-slot>

    @can('alumnos.crear')
        <div class="collapse mb-3" id="importBox">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <form method="POST" action="{{ route('alumnos.importar') }}" enctype="multipart/form-data" class="row g-2 align-items-end">
                        @csrf
                        <div class="col-md-8">
                            <label class="form-label small mb-1">Archivo Excel (.xlsx/.csv) — usa la plantilla. El código QR se genera según el formato de la escuela.</label>
                            <input type="file" name="archivo" class="form-control form-control-sm" accept=".xlsx,.xls,.csv" required>
                            <x-input-error :messages="$errors->get('archivo')" />
                        </div>
                        <div class="col-md-4">
                            <button class="btn btn-primary btn-sm">Cargar alumnos</button>
                        </div>
                    </form>
                    @if (session('import_errores') && count(session('import_errores')))
                        <div class="alert alert-warning mt-3 mb-0 small">
                            <strong>Filas omitidas:</strong>
                            <ul class="mb-0">@foreach (session('import_errores') as $e)<li>{{ $e }}</li>@endforeach</ul>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endcan

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form method="GET" class="mb-3">
                <div class="input-group" style="max-width: 28rem;">
                    <input type="text" name="q" value="{{ $q }}" class="form-control"
                           placeholder="Buscar por nombre, matrícula o CURP">
                    <button class="btn btn-outline-secondary" type="submit">Buscar</button>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Matrícula</th>
                            <th>Nombre</th>
                            <th>Grupo</th>
                            <th>Estatus</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($alumnos as $alumno)
                            <tr>
                                <td class="font-monospace">{{ $alumno->matricula }}</td>
                                <td>{{ $alumno->nombreCompleto() }}</td>
                                <td>{{ $alumno->grupo?->nombreCompleto() ?? '—' }}</td>
                                <td>
                                    <span class="badge bg-{{ $alumno->estatus === 'activo' ? 'success' : 'secondary' }}">
                                        {{ ucfirst($alumno->estatus) }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('alumnos.show', $alumno) }}" class="btn btn-sm btn-outline-secondary">Ver</a>
                                    @can('alumnos.editar')
                                        <a href="{{ route('alumnos.edit', $alumno) }}" class="btn btn-sm btn-outline-primary">Editar</a>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">Sin alumnos.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $alumnos->links() }}
        </div>
    </div>
</x-app-layout>
