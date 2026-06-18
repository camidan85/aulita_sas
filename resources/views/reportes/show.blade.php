<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h1 class="h4 mb-0">{{ $reporte->tipoLabel() }}</h1>
            <a href="{{ route('reportes.index') }}" class="btn btn-sm btn-outline-secondary">Volver</a>
        </div>
    </x-slot>

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-3">Alumno</dt><dd class="col-sm-9">{{ $reporte->alumno->nombreCompleto() }}</dd>
                        <dt class="col-sm-3">Profesor</dt><dd class="col-sm-9">{{ $reporte->profesor->name }}</dd>
                        <dt class="col-sm-3">Fecha</dt><dd class="col-sm-9">{{ $reporte->fecha->format('d/m/Y') }} {{ $reporte->hora }}</dd>
                        <dt class="col-sm-3">Descripción</dt><dd class="col-sm-9">{{ $reporte->descripcion }}</dd>
                    </dl>

                    @if ($reporte->evidencias->isNotEmpty())
                        <hr>
                        <h2 class="h6 text-muted">Evidencias</h2>
                        <ul class="list-unstyled mb-0">
                            @foreach ($reporte->evidencias as $ev)
                                <li class="mb-1">
                                    <a href="{{ route('evidencias.descargar', $ev) }}">
                                        {{ $ev->nombre_original ?? $ev->path }}
                                    </a>
                                    <span class="badge bg-light text-dark">{{ $ev->tipo }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h2 class="h6 text-muted mb-3">Firmas de enterado</h2>

                    @forelse ($reporte->firmas as $firma)
                        <div class="border-bottom py-2 small">
                            <strong>{{ $firma->user->name }}</strong><br>
                            {{ $firma->fecha->format('d/m/Y') }} {{ $firma->hora }} · IP {{ $firma->ip }}
                        </div>
                    @empty
                        <p class="text-muted small">Sin firmas todavía.</p>
                    @endforelse

                    @unless ($reporte->firmadoPor(auth()->id()))
                        <form method="POST" action="{{ route('reportes.firmar', $reporte) }}" class="mt-3">
                            @csrf @method('PATCH')
                            <button class="btn btn-success btn-sm">Firmar de enterado</button>
                        </form>
                    @else
                        <div class="text-success small mt-3">Ya firmaste este reporte.</div>
                    @endunless
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
