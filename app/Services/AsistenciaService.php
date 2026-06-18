<?php

namespace App\Services;

use App\Events\AsistenciaRegistrada;
use App\Models\Alumno;
use App\Models\Asistencia;
use App\Models\School;
use App\Tenancy\TenantManager;
use Carbon\Carbon;

class AsistenciaService extends BaseService
{
    /** Estatus que ya cierran el registro del día (no se sobreescriben). */
    private const ESTATUS_FINALES = ['presente', 'retardo', 'justificada'];

    public function __construct(protected TenantManager $tenant) {}

    /**
     * Registra (o actualiza) la asistencia del día de un alumno.
     *
     * @return array{asistencia: Asistencia, estado: 'creado'|'actualizado'|'duplicado'}
     */
    public function registrar(Alumno $alumno, array $meta = []): array
    {
        $school = School::findOrFail($alumno->school_id);
        $tz = $school->timezone ?: config('app.timezone');

        $ahora = Carbon::now($tz);
        $fecha = $ahora->toDateString();
        $corte = Carbon::parse($fecha.' '.$school->hora_corte_faltas, $tz);

        // RN-AS03/AS04: antes del corte = presente; después = retardo.
        $estatus = $ahora->lessThanOrEqualTo($corte) ? 'presente' : 'retardo';

        $asistencia = Asistencia::where('alumno_id', $alumno->id)
            ->whereDate('fecha', $fecha)
            ->first();

        // Ya tiene un registro final hoy: idempotente.
        if ($asistencia && in_array($asistencia->estatus, self::ESTATUS_FINALES, true)) {
            return ['asistencia' => $asistencia, 'estado' => 'duplicado'];
        }

        $datos = [
            'alumno_id' => $alumno->id,
            'fecha' => $fecha,
            'hora' => $ahora->toTimeString(),
            'estatus' => $estatus,
            'origen' => $meta['origen'] ?? 'qr',
            'registrado_por' => $meta['registrado_por'] ?? auth()->id(),
            'ip' => $meta['ip'] ?? null,
            'dispositivo' => $meta['dispositivo'] ?? null,
        ];

        if ($asistencia) {
            // Transición falta_pendiente/falta -> presente|retardo (RN-AS04).
            $asistencia->update($datos);
            $estado = 'actualizado';
        } else {
            $asistencia = Asistencia::create($datos);
            $estado = 'creado';
        }

        AsistenciaRegistrada::dispatch($asistencia->fresh('alumno'));

        return ['asistencia' => $asistencia, 'estado' => $estado];
    }
}
