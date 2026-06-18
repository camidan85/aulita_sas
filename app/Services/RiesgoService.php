<?php

namespace App\Services;

use App\Models\AlertaRiesgo;
use App\Models\Alumno;
use App\Notifications\AlertaRiesgoNotification;
use App\Support\DestinatariosEscolares;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Notification;

/**
 * Evalúa las reglas de riesgo de un alumno y genera alertas (RN-R01..R04).
 */
class RiesgoService extends BaseService
{
    private const FALTAS = ['falta', 'falta_pendiente'];

    /**
     * @return array<int, AlertaRiesgo>
     */
    public function evaluar(Alumno $alumno): array
    {
        $generadas = [];

        if ($this->faltasConsecutivas($alumno) >= 3) {
            $generadas[] = $this->generar($alumno, AlertaRiesgo::TIPO_3_FALTAS, '3 faltas consecutivas');
        }

        if ($this->faltasEnMes($alumno) >= 5) {
            $generadas[] = $this->generar($alumno, AlertaRiesgo::TIPO_5_FALTAS_MES, '5 faltas en el mes en curso');
        }

        if ($this->retardos($alumno) >= 10) {
            $generadas[] = $this->generar($alumno, AlertaRiesgo::TIPO_10_RETARDOS, '10 retardos acumulados');
        }

        return array_values(array_filter($generadas));
    }

    private function faltasConsecutivas(Alumno $alumno): int
    {
        $recientes = $alumno->asistencias()
            ->orderByDesc('fecha')
            ->limit(60)
            ->get();

        $streak = 0;
        foreach ($recientes as $a) {
            if (in_array($a->estatus, self::FALTAS, true)) {
                $streak++;
            } else {
                break;
            }
        }

        return $streak;
    }

    private function faltasEnMes(Alumno $alumno): int
    {
        $ahora = Carbon::now();

        return $alumno->asistencias()
            ->whereIn('estatus', self::FALTAS)
            ->whereYear('fecha', $ahora->year)
            ->whereMonth('fecha', $ahora->month)
            ->count();
    }

    private function retardos(Alumno $alumno): int
    {
        // Acumulado (RN-R03). Puede acotarse al ciclo vigente cuando se requiera.
        return $alumno->asistencias()->where('estatus', 'retardo')->count();
    }

    private function generar(Alumno $alumno, string $tipo, string $detalle): ?AlertaRiesgo
    {
        // No duplicar: si ya hay una alerta de ese tipo sin atender, no se recrea (RN-R04).
        $yaExiste = AlertaRiesgo::where('alumno_id', $alumno->id)
            ->where('tipo', $tipo)
            ->where('atendida', false)
            ->exists();

        if ($yaExiste) {
            return null;
        }

        $alerta = AlertaRiesgo::create([
            'alumno_id' => $alumno->id,
            'tipo' => $tipo,
            'detalle' => $detalle,
            'atendida' => false,
            'generada_en' => now(),
        ]);

        $destinatarios = DestinatariosEscolares::tutoresYAdministrativos($alumno);

        if (! empty($destinatarios)) {
            Notification::send($destinatarios, new AlertaRiesgoNotification($alerta));
        }

        return $alerta;
    }
}
