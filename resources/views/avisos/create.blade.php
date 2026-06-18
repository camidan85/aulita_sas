<x-app-layout>
    <x-slot name="header">
        <h1 class="h4 mb-0">Nuevo aviso</h1>
    </x-slot>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('avisos.store') }}" enctype="multipart/form-data"
                  x-data="{ alcance: '{{ old('alcance', 'escuela') }}' }">
                @csrf
                <div class="row g-3">
                    <div class="col-md-8">
                        <x-input-label for="titulo" value="Título" />
                        <x-text-input id="titulo" name="titulo" :value="old('titulo')" required />
                        <x-input-error :messages="$errors->get('titulo')" />
                    </div>
                    <div class="col-md-4">
                        <x-input-label for="alcance" value="Dirigido a" />
                        <select id="alcance" name="alcance" class="form-select" x-model="alcance">
                            @foreach ($alcances as $val => $label)
                                <option value="{{ $val }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6" x-show="alcance === 'grado'" x-cloak>
                        <x-input-label for="target_grado" value="Grado" />
                        <select id="target_grado" class="form-select" x-bind:name="alcance === 'grado' ? 'target_id' : ''">
                            @foreach ($grados as $g)
                                <option value="{{ $g->id }}">{{ $g->nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6" x-show="alcance === 'grupo'" x-cloak>
                        <x-input-label for="target_grupo" value="Grupo" />
                        <select id="target_grupo" class="form-select" x-bind:name="alcance === 'grupo' ? 'target_id' : ''">
                            @foreach ($grupos as $g)
                                <option value="{{ $g->id }}">{{ $g->nombreCompleto() }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6" x-show="alcance === 'alumno'" x-cloak>
                        <x-input-label for="target_alumno" value="ID de alumno" />
                        <input id="target_alumno" type="number" class="form-control" x-bind:name="alcance === 'alumno' ? 'target_id' : ''">
                    </div>

                    <div class="col-12">
                        <x-input-label for="contenido" value="Contenido" />
                        <textarea id="contenido" name="contenido" rows="5" class="form-control" required>{{ old('contenido') }}</textarea>
                        <x-input-error :messages="$errors->get('contenido')" />
                    </div>

                    <div class="col-12">
                        <x-input-label for="adjuntos" value="Adjuntos (máx. 6)" />
                        <input id="adjuntos" name="adjuntos[]" type="file" class="form-control" multiple
                               accept=".jpg,.jpeg,.png,.webp,.pdf,.doc,.docx,.mp4,.mov">
                        <x-input-error :messages="$errors->get('adjuntos.0')" />
                    </div>

                    <div class="col-12">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="requiere_firma" name="requiere_firma" value="1" @checked(old('requiere_firma'))>
                            <label class="form-check-label" for="requiere_firma">Requiere firma de enterado</label>
                        </div>
                    </div>
                </div>

                <div class="mt-4 d-flex gap-2">
                    <x-primary-button>Publicar</x-primary-button>
                    <a href="{{ route('avisos.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
