<section>
    <header class="mb-3">
        <h2 class="h5 mb-1 text-danger">{{ __('Eliminar cuenta') }}</h2>
        <p class="text-muted small mb-0">
            {{ __('Al eliminar tu cuenta, todos sus datos se borran de forma permanente.') }}
        </p>
    </header>

    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#confirmUserDeletion">
        {{ __('Eliminar cuenta') }}
    </button>

    <div class="modal fade" id="confirmUserDeletion" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="{{ route('profile.destroy') }}">
                    @csrf
                    @method('delete')

                    <div class="modal-header">
                        <h5 class="modal-title">{{ __('¿Eliminar tu cuenta?') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>

                    <div class="modal-body">
                        <p class="text-muted small">
                            {{ __('Esta acción es permanente. Ingresa tu contraseña para confirmar.') }}
                        </p>
                        <x-input-label for="password" :value="__('Contraseña')" class="visually-hidden" />
                        <x-text-input id="password" name="password" type="password" placeholder="{{ __('Contraseña') }}" />
                        <x-input-error :messages="$errors->userDeletion->get('password')" />
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                            {{ __('Cancelar') }}
                        </button>
                        <x-danger-button type="submit">{{ __('Eliminar cuenta') }}</x-danger-button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @if ($errors->userDeletion->isNotEmpty())
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                new window.bootstrap.Modal(document.getElementById('confirmUserDeletion')).show();
            });
        </script>
    @endif
</section>
