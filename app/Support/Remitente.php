<?php

namespace App\Support;

use App\Models\School;

class Remitente
{
    /**
     * Devuelve [direccion, nombre] para el From de los correos.
     * El nombre es "Aulita - {Escuela}" según el school_id del aviso.
     *
     * @return array{0: string, 1: string}
     */
    public static function para(?int $schoolId): array
    {
        $nombre = $schoolId ? School::find($schoolId)?->nombre : null;

        return [
            config('mail.from.address'),
            $nombre ? "Aulita - {$nombre}" : 'Aulita',
        ];
    }
}
