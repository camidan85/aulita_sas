<?php

namespace App\Support;

use App\Tenancy\TenantManager;

/**
 * Catálogo de módulos que el Super Admin puede activar/ocultar por escuela.
 */
class Modulos
{
    /** @var array<string, string> clave => etiqueta */
    public const DISPONIBLES = [
        'asistencia' => 'Asistencia (QR)',
        'alertas' => 'Alertas de riesgo',
        'calificaciones' => 'Calificaciones',
        'reportes' => 'Reportes de conducta',
        'avisos' => 'Avisos',
        'citas' => 'Citas',
        'portal' => 'Portal de padres',
        'bitacora' => 'Auditoría',
    ];

    /**
     * ¿El módulo está activo para la escuela actual?
     * Sin tenant (Super Admin) todo se considera visible.
     */
    public static function activo(string $clave): bool
    {
        $school = app(TenantManager::class)->school();

        return $school ? $school->moduloActivo($clave) : true;
    }
}
