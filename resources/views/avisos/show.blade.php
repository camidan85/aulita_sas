<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h1 class="h4 mb-0">{{ $aviso->titulo }}</h1>
            <a href="{{ route('avisos.index') }}" class="btn btn-sm btn-outline-secondary">Volver</a>
        </div>
    </x-slot>

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <p class="text-muted small mb-2">
                        {{ $aviso->alcanceLabel() }} · {{ $aviso->fecha_publicacion->format('d/m/Y H:i') }} · {{ $aviso->publicadoPor->name }}
                    </p>
                    <p style="white-space: pre-line;">{{ $aviso->contenido }}</p>

                    @if ($aviso->adjuntos->isNotEmpty())
                        <hr>
                        <h2 class="h6 text-muted">Adjuntos</h2>
                        <ul class="list-unstyled mb-0">
                            @foreach ($aviso->adjuntos as $adj)
                                <li><a href="{{ route('adjuntos.descargar', $adj) }}">{{ $adj->nombre_original ?? $adj->path }}</a></li>
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
                    @forelse ($aviso->firmas as $firma)
                        <div class="border-bottom py-2 small">
                            <strong>{{ $firma->user->name }}</strong><br>
                            {{ $firma->fecha->format('d/m/Y') }} {{ $firma->hora }} · IP {{ $firma->ip }}
                        </div>
                    @empty
                        <p class="text-muted small">Sin firmas todavía.</p>
                    @endforelse

                    @if ($aviso->requiere_firma)
                        @unless ($aviso->firmadoPor(auth()->id()))
                            <form method="POST" action="{{ route('avisos.firmar', $aviso) }}" class="mt-3">
                                @csrf @method('PATCH')
                                <button class="btn btn-success btn-sm">Firmar de enterado</button>
                            </form>
                        @else
                            <div class="text-success small mt-3">Ya firmaste este aviso.</div>
                        @endunless
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
