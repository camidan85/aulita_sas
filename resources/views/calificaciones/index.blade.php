<x-app-layout>
    <x-slot name="header">
        <h1 class="h4 mb-0">Calificaciones</h1>
    </x-slot>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-md-5">
                    <label class="form-label">Grupo</label>
                    <select name="grupo_id" class="form-select">
                        <option value="">Selecciona…</option>
                        @foreach ($grupos as $g)
                            <option value="{{ $g->id }}" @selected($grupo?->id === $g->id)>{{ $g->nombreCompleto() }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-5">
                    <label class="form-label">Periodo</label>
                    <select name="periodo_id" class="form-select">
                        <option value="">Selecciona…</option>
                        @foreach ($periodos as $p)
                            <option value="{{ $p->id }}" @selected($periodo?->id === $p->id)>{{ str_replace('_', ' ', $p->nombre) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-primary w-100">Ver</button>
                </div>
            </form>
        </div>
    </div>

    @if ($grupo && $periodo)
        <div class="row g-4">
            <div class="col-lg-5">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h2 class="h6 text-muted mb-3">Capturar por materia</h2>
                        @can('calificaciones.capturar')
                            <div class="list-group">
                                @foreach ($materias as $m)
                                    <a class="list-group-item list-group-item-action d-flex justify-content-between"
                                       href="{{ route('calificaciones.capturar', ['grupo_id' => $grupo->id, 'materia_id' => $m->id, 'periodo_id' => $periodo->id]) }}">
                                        {{ $m->nombre }}
                                        <span class="text-primary">Capturar →</span>
                                    </a>
                                @endforeach
                            </div>
                        @else
                            <p class="text-muted small mb-0">No tienes permiso para capturar.</p>
                        @endcan

                        <a class="btn btn-outline-success btn-sm mt-3"
                           href="{{ route('calificaciones.exportar', ['grupo_id' => $grupo->id, 'periodo_id' => $periodo->id]) }}">
                            Exportar a Excel
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-lg-7">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h2 class="h6 text-muted mb-0">{{ $grupo->nombreCompleto() }} · {{ str_replace('_', ' ', $periodo->nombre) }}</h2>
                            <span class="badge bg-primary">Prom. grupo: {{ $promedioGrupo ?? '—' }}</span>
                        </div>
                        <table class="table table-sm align-middle mb-0">
                            <thead><tr><th>Alumno</th><th>Prom. periodo</th><th class="text-end">Documentos</th></tr></thead>
                            <tbody>
                                @foreach ($alumnos as $fila)
                                    <tr>
                                        <td>{{ $fila['alumno']->nombreCompleto() }}</td>
                                        <td>
                                            @php($prom = $fila['promedio'])
                                            <span class="badge bg-{{ $prom === null ? 'secondary' : ($prom < 6 ? 'danger' : 'success') }}">{{ $prom ?? '—' }}</span>
                                        </td>
                                        <td class="text-end">
                                            <a class="btn btn-sm btn-outline-secondary" href="{{ route('alumnos.boleta', $fila['alumno']) }}">Boleta</a>
                                            <a class="btn btn-sm btn-outline-secondary" href="{{ route('alumnos.kardex', $fila['alumno']) }}">Kardex</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @else
        <p class="text-muted">Selecciona un grupo y un periodo para ver y capturar calificaciones.</p>
    @endif
</x-app-layout>
