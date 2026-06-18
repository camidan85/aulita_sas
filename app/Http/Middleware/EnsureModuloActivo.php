<?php

namespace App\Http\Middleware;

use App\Support\Modulos;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Bloquea el acceso a rutas de un módulo desactivado para la escuela.
 * Uso: ->middleware('modulo:calificaciones')
 */
class EnsureModuloActivo
{
    public function handle(Request $request, Closure $next, string $clave): Response
    {
        abort_unless(Modulos::activo($clave), 404);

        return $next($request);
    }
}
