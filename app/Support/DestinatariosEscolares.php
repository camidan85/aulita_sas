<?php

namespace App\Support;

use App\Models\Alumno;
use App\Models\User;

class DestinatariosEscolares
{
    /**
     * Tutores del alumno (principal y secundario) + personal administrativo
     * de la escuela. Se usa array_merge para no deduplicar por id entre
     * modelos distintos (Tutor y User).
     *
     * @return array<int, object>
     */
    public static function tutoresYAdministrativos(Alumno $alumno): array
    {
        $tutores = $alumno->loadMissing('tutores')->tutores->all();

        $administrativos = User::where('school_id', $alumno->school_id)
            ->role('administrativo')
            ->get()
            ->all();

        return array_merge($tutores, $administrativos);
    }
}
