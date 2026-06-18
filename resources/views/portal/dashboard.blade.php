<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h1 class="h4 mb-0">Mi portal</h1>
            <a href="{{ route('citas.create') }}" class="btn btn-primary btn-sm">Solicitar cita</a>
        </div>
    </x-slot>

    @forelse ($hijos as $h)
        @php($a = $h['alumno'])
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <h2 class="h5 mb-0">{{ $a->nombreCompleto() }}</h2>
                        <div class="text-muted small">{{ $a->matricula }} · {{ $a->grupo?->nombreCompleto() ?? 'Sin grupo' }}</div>
                    </div>
                    <span class="badge bg-primary fs-6">Promedio: {{ $h['promedio'] ?? '—' }}</span>
                </div>

                <div class="row g-3 mb-2">
                    <div class="col-6 col-md-3">
                        <div class="border rounded p-2 text-center">
                            <div class="text-muted small">Asistencias (mes)</div>
                            <div class="fs-5 fw-bold text-success">{{ $h['asistencia']['presente'] }}</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="border rounded p-2 text-center">
                            <div class="text-muted small">Retardos</div>
                            <div class="fs-5 fw-bold text-warning">{{ $h['asistencia']['retardo'] }}</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="border rounded p-2 text-center">
                            <div class="text-muted small">Faltas</div>
                            <div class="fs-5 fw-bold text-danger">{{ $h['asistencia']['falta'] }}</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="border rounded p-2 text-center">
                            <div class="text-muted small">Materias en riesgo</div>
                            <div class="fs-5 fw-bold {{ $h['materiasEnRiesgo']->isNotEmpty() ? 'text-danger' : '' }}">{{ $h['materiasEnRiesgo']->count() }}</div>
                        </div>
                    </div>
                </div>

                @if ($h['pendientesFirma']->isNotEmpty())
                    <div class="alert alert-warning">
                        <strong>Pendientes de firma:</strong>
                        <ul class="mb-0 mt-1">
                            @foreach ($h['pendientesFirma'] as $r)
                                <li class="d-flex justify-content-between align-items-center">
                                    <span>{{ $r->tipoLabel() }} · {{ $r->fecha->format('d/m/Y') }}</span>
                                    <form method="POST" action="{{ route('reportes.firmar', $r) }}">
                                        @csrf @method('PATCH')
                                        <button class="btn btn-sm btn-success">Firmar de enterado</button>
                                    </form>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="row g-4 mt-1">
                    <div class="col-md-6">
                        <h3 class="h6 text-muted">Conducta reciente</h3>
                        @forelse ($h['reportes'] as $r)
                            <div class="border-bottom py-1 small">{{ $r->fecha->format('d/m/Y') }} · {{ $r->tipoLabel() }}</div>
                        @empty
                            <p class="text-muted small mb-0">Sin reportes.</p>
                        @endforelse
                    </div>
                    <div class="col-md-6">
                        <h3 class="h6 text-muted">Avisos</h3>
                        @forelse ($h['avisos'] as $aviso)
                            <div class="border-bottom py-1 small">
                                <a href="{{ route('avisos.show', $aviso) }}">{{ $aviso->titulo }}</a>
                                @if ($aviso->requiere_firma) <span class="badge bg-warning text-dark">firma</span> @endif
                            </div>
                        @empty
                            <p class="text-muted small mb-0">Sin avisos.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center text-muted py-5">
                Aún no hay alumnos vinculados a tu cuenta.
            </div>
        </div>
    @endforelse
</x-app-layout>
