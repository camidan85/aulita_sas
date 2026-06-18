<?php

namespace App\Services;

use App\Models\Alumno;
use App\Models\Asistencia;
use App\Models\School;
use App\Notifications\AusenciaNotification;
use App\Support\DestinatariosEscolares;
use App\Tenancy\TenantManager;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Notification;

/**
 * Detección automática de faltas a la hora de corte (RN-FA01..FA03).
 */
class FaltasService extends BaseService
{
    /** Envíos de WhatsApp por segundo para no exceder los límites de Meta. */
    private const ENVIOS_POR_SEGUNDO = 10;

    public function __construct(
        protected TenantManager $tenant,
        protected RiesgoService $riesgo,
    ) {}

    /**
     * Marca falta_pendiente a los alumnos activos sin registro del día,
     * notifica a tutores/administrativo (con throttling) y evalúa riesgo.
     *
     * @return int Número de faltas pendientes generadas.
     */
    public function detectarDelDia(School $school, ?Carbon $fecha = null): int
    {
        $this->tenant->setSchoolId($school->id);

        $tz = $school->timezone ?: config('app.timezone');
        $fecha = ($fecha ?? Carbon::now($tz))->toDateString();

        $alumnos = Alumno::where('estatus', 'activo')
            ->whereDoesntHave('asistencias', fn ($q) => $q->whereDate('fecha', $fecha))
            ->get();

        foreach ($alumnos as $indice => $alumno) {
            $asistencia = Asistencia::create([
                'alumno_id' => $alumno->id,
                'fecha' => $fecha,
                'hora' => null,
                'estatus' => 'falta_pendiente',
                'origen' => 'automatico',
            ]);

            $this->notificar($alumno, $asistencia, $indice);
            $this->riesgo->evaluar($alumno);
        }

        return $alumnos->count();
    }

    private function notificar(Alumno $alumno, Asistencia $asistencia, int $indice): void
    {
        $destinatarios = DestinatariosEscolares::tutoresYAdministrativos($alumno);

        if (empty($destinatarios)) {
            return;
        }

        // Throttling: espacia los envíos para respetar el rate limit de Meta.
        $delay = now()->addSeconds(intdiv($indice, self::ENVIOS_POR_SEGUNDO));

        Notification::send($destinatarios, (new AusenciaNotification($asistencia))->delay($delay));
    }
}
