<?php

namespace App\Providers;

use App\Events\AsistenciaRegistrada;
use App\Listeners\NotificarAsistenciaTutores;
use App\Models\User;
use App\Support\Modulos;
use App\Tenancy\TenantManager;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(TenantManager::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();

        Event::listen(AsistenciaRegistrada::class, NotificarAsistenciaTutores::class);

        // Directiva @modulo('clave') ... @endmodulo para ocultar UI de módulos.
        Blade::if('modulo', fn (string $clave) => Modulos::activo($clave));

        // El Super Admin puede todo (atajo para gates/policies).
        Gate::before(fn (User $user) => $user->isSuperAdmin() ? true : null);

        $this->configurarRateLimiters();
    }

    private function configurarRateLimiters(): void
    {
        // Activación del portal de padres (público): evita fuerza bruta de CURP.
        RateLimiter::for('activacion', fn (Request $request) => Limit::perMinute(6)->by($request->ip()));

        // Escaneo de QR: un prefecto registra muchos alumnos seguidos.
        RateLimiter::for('escaneo', fn (Request $request) => Limit::perMinute(120)->by($request->user()?->id ?: $request->ip()));
    }
}
