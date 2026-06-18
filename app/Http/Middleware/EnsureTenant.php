<?php

namespace App\Http\Middleware;

use App\Tenancy\TenantManager;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Garantiza que hay una escuela activa para las operaciones de aula.
 * Un Super Admin sin escuela seleccionada se redirige a elegir una.
 */
class EnsureTenant
{
    public function __construct(protected TenantManager $tenant) {}

    public function handle(Request $request, Closure $next): Response
    {
        if (! $this->tenant->hasTenant()) {
            if ($request->user()?->hasRole('super_admin')) {
                return redirect()->route('admin.escuelas.index')
                    ->with('status', 'Selecciona una escuela para gestionarla.');
            }

            abort(403, 'Sin escuela asignada.');
        }

        return $next($request);
    }
}
