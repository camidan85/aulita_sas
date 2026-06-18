<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h1 class="h4 mb-0">QR de asistencia</h1>
            <a href="{{ route('alumnos.show', $alumno) }}" class="btn btn-sm btn-outline-secondary">Volver</a>
        </div>
    </x-slot>

    <div class="row justify-content-center">
        <div class="col-auto">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body">
                    <div class="mb-2">{!! $svg !!}</div>
                    <div class="fw-semibold">{{ $alumno->nombreCompleto() }}</div>
                    <div class="text-muted small font-monospace">{{ $alumno->matricula }}</div>
                    <div class="text-muted small">{{ $alumno->grupo?->nombreCompleto() }}</div>
                    <div class="badge bg-light text-dark border mt-1 font-monospace">{{ $alumno->codigo_qr }}</div>

                    <button onclick="window.print()" class="btn btn-outline-primary btn-sm mt-3">Imprimir</button>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
