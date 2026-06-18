<?php

namespace App\Http\Middleware;

use App\Tenancy\TenantManager;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Fija el tenant (school_id) a partir del usuario autenticado.
 * El school_id NUNCA se toma de la petición del cliente (RN-T02).
 */
class ResolveTenant
{
    public function __construct(protected TenantManager $tenant) {}

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->school_id) {
            $this->tenant->setSchoolId((int) $user->school_id);
        }

        return $next($request);
    }
}
