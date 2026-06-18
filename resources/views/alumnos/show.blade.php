<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h1 class="h4 mb-0">{{ $alumno->nombreCompleto() }}</h1>
            <div class="d-flex gap-2">
                @can('calificaciones.ver')
                    <a href="{{ route('alumnos.boleta', $alumno) }}" class="btn btn-sm btn-outline-secondary">Boleta</a>
                    <a href="{{ route('alumnos.kardex', $alumno) }}" class="btn btn-sm btn-outline-secondary">Kardex</a>
                @endcan
                <a href="{{ route('alumnos.qr', $alumno) }}" class="btn btn-sm btn-outline-dark">Ver QR</a>
                @can('alumnos.editar')
                    <a href="{{ route('alumnos.edit', $alumno) }}" class="btn btn-sm btn-primary">Editar</a>
                @endcan
                <a href="{{ route('alumnos.index') }}" class="btn btn-sm btn-outline-secondary">Volver</a>
            </div>
        </div>
    </x-slot>

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h2 class="h6 text-muted mb-3">Datos generales</h2>
                    <dl class="row mb-0">
                        <dt class="col-sm-4">Matrícula</dt><dd class="col-sm-8 font-monospace">{{ $alumno->matricula }}</dd>
                        <dt class="col-sm-4">CURP</dt><dd class="col-sm-8 font-monospace">{{ $alumno->curp }}</dd>
                        <dt class="col-sm-4">Grupo</dt><dd class="col-sm-8">{{ $alumno->grupo?->nombreCompleto() ?? '—' }}</dd>
                        <dt class="col-sm-4">Sexo</dt><dd class="col-sm-8">{{ $alumno->sexo ?? '—' }}</dd>
                        <dt class="col-sm-4">Nacimiento</dt><dd class="col-sm-8">{{ $alumno->fecha_nacimiento?->format('d/m/Y') ?? '—' }}</dd>
                        <dt class="col-sm-4">Correo</dt><dd class="col-sm-8">{{ $alumno->correo ?? '—' }}</dd>
                        <dt class="col-sm-4">Teléfono</dt><dd class="col-sm-8">{{ $alumno->telefono ?? '—' }}</dd>
                        <dt class="col-sm-4">Estatus</dt>
                        <dd class="col-sm-8">
                            <span class="badge bg-{{ $alumno->estatus === 'activo' ? 'success' : 'secondary' }}">
                                {{ ucfirst($alumno->estatus) }}
                            </span>
                        </dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h6 text-muted mb-3">Tutores</h2>
                    @forelse ($alumno->tutores as $tutor)
                        <div class="d-flex justify-content-between border-bottom py-2">
                            <div>
                                <div>{{ $tutor->nombre }}</div>
                                <div class="small text-muted">{{ $tutor->telefono }}</div>
                            </div>
                            <span class="badge bg-light text-dark align-self-center">{{ ucfirst($tutor->pivot->tipo) }}</span>
                        </div>
                    @empty
                        <p class="text-muted small mb-0">Sin tutores asignados.</p>
                    @endforelse
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h2 class="h6 text-muted mb-3">Expediente médico</h2>
                    @if ($alumno->expedienteMedico)
                        <dl class="row mb-0 small">
                            <dt class="col-5">Tipo de sangre</dt><dd class="col-7">{{ $alumno->expedienteMedico->tipo_sangre ?? '—' }}</dd>
                            <dt class="col-5">Alergias</dt><dd class="col-7">{{ $alumno->expedienteMedico->alergias ?? '—' }}</dd>
                            <dt class="col-5">Emergencia</dt><dd class="col-7">{{ $alumno->expedienteMedico->contacto_emergencia_telefono ?? '—' }}</dd>
                        </dl>
                    @else
                        <p class="text-muted small mb-0">Sin expediente médico capturado.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
