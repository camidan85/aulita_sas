<nav class="navbar navbar-expand-md navbar-light bg-white border-bottom">
    <div class="container">
        <a class="navbar-brand" href="{{ route('dashboard') }}">
            <x-application-logo />
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#mainNav" aria-controls="mainNav"
                aria-expanded="false" aria-label="Menú">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>
                </li>

                @role('super_admin')
                    <li class="nav-item">
                        <x-nav-link :href="route('admin.escuelas.index')" :active="request()->routeIs('admin.*')">
                            {{ __('Escuelas') }}
                        </x-nav-link>
                    </li>
                @endrole

                @can('portal.ver')
                    @modulo('portal')
                        <li class="nav-item">
                            <x-nav-link :href="route('portal.dashboard')" :active="request()->routeIs('portal.*')">
                                {{ __('Mi portal') }}
                            </x-nav-link>
                        </li>
                    @endmodulo
                    @modulo('citas')
                        <li class="nav-item">
                            <x-nav-link :href="route('citas.index')" :active="request()->routeIs('citas.*')">
                                {{ __('Citas') }}
                            </x-nav-link>
                        </li>
                    @endmodulo
                @endcan

                @can('alumnos.ver')
                    <li class="nav-item">
                        <x-nav-link :href="route('alumnos.index')" :active="request()->routeIs('alumnos.*')">
                            {{ __('Alumnos') }}
                        </x-nav-link>
                    </li>
                @endcan

                @can('grupos.gestionar')
                    <li class="nav-item">
                        <x-nav-link :href="route('grupos.index')" :active="request()->routeIs('grupos.*')">
                            {{ __('Grupos') }}
                        </x-nav-link>
                    </li>
                @endcan

                @can('asistencias.registrar')
                    @modulo('asistencia')
                        <li class="nav-item">
                            <x-nav-link :href="route('asistencias.escanear')" :active="request()->routeIs('asistencias.escanear')">
                                {{ __('Escanear QR') }}
                            </x-nav-link>
                        </li>
                    @endmodulo
                @endcan

                @can('asistencias.ver')
                    @modulo('asistencia')
                        <li class="nav-item">
                            <x-nav-link :href="route('asistencias.index')" :active="request()->routeIs('asistencias.index')">
                                {{ __('Asistencias') }}
                            </x-nav-link>
                        </li>
                    @endmodulo
                    @modulo('alertas')
                        <li class="nav-item">
                            <x-nav-link :href="route('alertas.index')" :active="request()->routeIs('alertas.*')">
                                {{ __('Alertas') }}
                            </x-nav-link>
                        </li>
                    @endmodulo
                @endcan

                @can('calificaciones.ver')
                    @modulo('calificaciones')
                        <li class="nav-item">
                            <x-nav-link :href="route('calificaciones.index')" :active="request()->routeIs('calificaciones.*')">
                                {{ __('Calificaciones') }}
                            </x-nav-link>
                        </li>
                    @endmodulo
                @endcan

                @can('reportes.ver')
                    @modulo('reportes')
                        <li class="nav-item">
                            <x-nav-link :href="route('reportes.index')" :active="request()->routeIs('reportes.*')">
                                {{ __('Reportes') }}
                            </x-nav-link>
                        </li>
                    @endmodulo
                @endcan

                @can('avisos.ver')
                    @modulo('avisos')
                        <li class="nav-item">
                            <x-nav-link :href="route('avisos.index')" :active="request()->routeIs('avisos.*')">
                                {{ __('Avisos') }}
                            </x-nav-link>
                        </li>
                    @endmodulo
                @endcan

                @can('bitacora.ver')
                    @modulo('bitacora')
                        <li class="nav-item">
                            <x-nav-link :href="route('bitacora.index')" :active="request()->routeIs('bitacora.*')">
                                {{ __('Auditoría') }}
                            </x-nav-link>
                        </li>
                    @endmodulo
                @endcan

                @canany(['grupos.gestionar', 'materias.gestionar', 'docentes.gestionar', 'tutores.gestionar'])
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            {{ __('Catálogos') }}
                        </a>
                        <ul class="dropdown-menu">
                            @can('grupos.gestionar')
                                <li><a class="dropdown-item" href="{{ route('grados.index') }}">Grados</a></li>
                            @endcan
                            @can('materias.gestionar')
                                <li><a class="dropdown-item" href="{{ route('materias.index') }}">Materias</a></li>
                            @endcan
                            @can('docentes.gestionar')
                                <li><a class="dropdown-item" href="{{ route('docentes.index') }}">Docentes</a></li>
                            @endcan
                            @can('tutores.gestionar')
                                <li><a class="dropdown-item" href="{{ route('tutores.index') }}">Tutores</a></li>
                            @endcan
                        </ul>
                    </li>
                @endcanany
            </ul>

            <ul class="navbar-nav ms-auto align-items-md-center">
                @auth
                    <x-dropdown align="end">
                        <x-slot name="trigger">
                            <button class="btn btn-light border d-flex align-items-center gap-2">
                                <span>{{ Auth::user()->name }}</span>
                                <svg width="12" height="12" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.17l3.71-3.94a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <li><span class="dropdown-item-text small text-muted">{{ Auth::user()->email }}</span></li>
                            <li><hr class="dropdown-divider"></li>
                            <x-dropdown-link :href="route('profile.edit')">
                                {{ __('Perfil') }}
                            </x-dropdown-link>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item">
                                        {{ __('Cerrar sesión') }}
                                    </button>
                                </form>
                            </li>
                        </x-slot>
                    </x-dropdown>
                @endauth
            </ul>
        </div>
    </div>
</nav>
