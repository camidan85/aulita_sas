<?php

namespace App\Support;

use App\Models\School;

class Remitente
{
    /**
     * Nombre visible del remitente/cierre: "Aulita - {Escuela}".
     */
    public static function nombre(?int $schoolId): string
    {
        $escuela = $schoolId ? School::find($schoolId)?->nombre : null;

        return $escuela ? "Aulita - {$escuela}" : 'Aulita';
    }

    /**
     * Devuelve [direccion, nombre] para el From de los correos.
     *
     * @return array{0: string, 1: string}
     */
    public static function para(?int $schoolId): array
    {
        return [config('mail.from.address'), self::nombre($schoolId)];
    }
}
