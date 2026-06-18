<x-app-layout>
    <x-slot name="header">
        <h1 class="h4 mb-0">Auditoría / Bitácora</h1>
    </x-slot>

    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-6 col-md-2">
                    <label class="form-label small">Desde</label>
                    <input type="date" name="desde" value="{{ $filtros['desde'] ?? '' }}" class="form-control form-control-sm">
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label small">Hasta</label>
                    <input type="date" name="hasta" value="{{ $filtros['hasta'] ?? '' }}" class="form-control form-control-sm">
                </div>
                <div class="col-6 col-md-3">
                    <label class="form-label small">Usuario</label>
                    <select name="user_id" class="form-select form-select-sm">
                        <option value="">Todos</option>
                        @foreach ($usuarios as $u)
                            <option value="{{ $u->id }}" @selected(($filtros['user_id'] ?? null) == $u->id)>{{ $u->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label small">Módulo</label>
                    <select name="modulo" class="form-select form-select-sm">
                        <option value="">Todos</option>
                        @foreach ($modulos as $m)
                            <option value="{{ $m }}" @selected(($filtros['modulo'] ?? null) === $m)>{{ $m }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label small">Acción</label>
                    <input type="text" name="accion" value="{{ $filtros['accion'] ?? '' }}" class="form-control form-control-sm" placeholder="crear, login…">
                </div>
                <div class="col-12 d-flex gap-2">
                    <button class="btn btn-primary btn-sm">Filtrar</button>
                    <a href="{{ route('bitacora.index') }}" class="btn btn-outline-secondary btn-sm">Limpiar</a>
                    <span class="ms-auto"></span>
                    <a href="{{ route('bitacora.exportar', array_merge($filtros, ['formato' => 'pdf'])) }}" class="btn btn-outline-danger btn-sm">PDF</a>
                    <a href="{{ route('bitacora.exportar', array_merge($filtros, ['formato' => 'xlsx'])) }}" class="btn btn-outline-success btn-sm">Excel</a>
                    <a href="{{ route('bitacora.exportar', array_merge($filtros, ['formato' => 'csv'])) }}" class="btn btn-outline-secondary btn-sm">CSV</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-sm table-hover align-middle mb-0">
                    <thead>
                        <tr><th>Fecha</th><th>Usuario</th><th>Acción</th><th>Módulo</th><th>Descripción</th><th>IP</th></tr>
                    </thead>
                    <tbody>
                        @forelse ($registros as $r)
                            <tr>
                                <td class="small">{{ $r->created_at?->format('d/m/Y H:i:s') }}</td>
                                <td>{{ $r->user?->name ?? 'Sistema' }}</td>
                                <td><span class="badge bg-light text-dark">{{ $r->accion }}</span></td>
                                <td class="small">{{ $r->modulo ?? '—' }}</td>
                                <td class="small">{{ $r->descripcion }}</td>
                                <td class="small font-monospace">{{ $r->ip }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center text-muted py-4">Sin movimientos.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">{{ $registros->links() }}</div>
        </div>
    </div>
</x-app-layout>
