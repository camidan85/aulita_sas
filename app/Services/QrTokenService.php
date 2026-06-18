<?php

namespace App\Services;

use App\Models\Alumno;
use App\Models\School;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

/**
 * Genera el contenido del QR de cada alumno a partir de la PLANTILLA configurada
 * por la escuela (school.qr_formato), p. ej. "{matricula}" o "ESC-{matricula}".
 * El valor se materializa en alumnos.codigo_qr para búsquedas rápidas/seguras.
 */
class QrTokenService extends BaseService
{
    /**
     * Aplica la plantilla de la escuela a los datos del alumno.
     */
    public function generarCodigo(Alumno $alumno): string
    {
        $school = $alumno->relationLoaded('school') ? $alumno->school : School::find($alumno->school_id);
        $formato = $school?->qr_formato ?: '{matricula}';

        $valores = [
            '{id}' => (string) $alumno->id,
            '{matricula}' => (string) $alumno->matricula,
            '{curp}' => (string) $alumno->curp,
            '{nombre}' => (string) $alumno->nombre,
            '{apellido_paterno}' => (string) $alumno->apellido_paterno,
            '{apellido_materno}' => (string) $alumno->apellido_materno,
        ];

        $codigo = strtr($formato, $valores);

        // Si la plantilla no produjo nada útil, usa un token aleatorio como respaldo.
        return $codigo !== '' ? $codigo : 'AUL-'.Str::upper(Str::random(10));
    }

    /**
     * Calcula y guarda el codigo_qr del alumno (si cambió).
     */
    public function asignar(Alumno $alumno): string
    {
        $codigo = $this->generarCodigo($alumno);

        if ($alumno->codigo_qr !== $codigo) {
            $alumno->forceFill(['codigo_qr' => $codigo])->saveQuietly();
        }

        return $codigo;
    }

    /**
     * Regenera el codigo_qr de todos los alumnos de una escuela (al cambiar la plantilla).
     */
    public function regenerarParaEscuela(School $school): int
    {
        $n = 0;
        Alumno::where('school_id', $school->id)->chunkById(200, function ($alumnos) use ($school, &$n) {
            foreach ($alumnos as $alumno) {
                $alumno->setRelation('school', $school);
                $this->asignar($alumno);
                $n++;
            }
        });

        return $n;
    }

    public function contenido(Alumno $alumno): string
    {
        return $alumno->codigo_qr ?: $this->asignar($alumno);
    }

    public function svg(Alumno $alumno, int $size = 240): string
    {
        return QrCode::format('svg')->size($size)->margin(1)->generate($this->contenido($alumno));
    }

    /**
     * Resuelve el alumno a partir del contenido escaneado (dentro del tenant actual).
     */
    public function resolverAlumno(string $contenido): ?Alumno
    {
        $codigo = trim($contenido);

        // Compatibilidad con el prefijo antiguo "AULITA:".
        if (Str::startsWith($codigo, 'AULITA:')) {
            $codigo = Str::after($codigo, 'AULITA:');
        }

        return Alumno::where('codigo_qr', $codigo)->first();
    }
}
