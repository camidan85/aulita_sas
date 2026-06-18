<?php

namespace App\Console\Commands;

use App\Models\School;
use App\Services\FaltasService;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class DetectarFaltas extends Command
{
    protected $signature = 'asistencia:detectar-faltas
        {--school= : Procesar solo una escuela (ID)}
        {--force : Ignora la verificación de la hora de corte}';

    protected $description = 'Marca falta_pendiente a los alumnos sin asistencia a la hora de corte y evalúa alertas de riesgo';

    public function handle(FaltasService $faltas): int
    {
        $query = School::where('estatus', 'activa');

        if ($id = $this->option('school')) {
            $query->whereKey($id);
        }

        $total = 0;

        foreach ($query->get() as $escuela) {
            if (! $this->option('force') && ! $this->esHoraDeCorte($escuela)) {
                continue;
            }

            $n = $faltas->detectarDelDia($escuela);
            $total += $n;
            $this->info("[{$escuela->nombre}] {$n} faltas pendientes generadas.");
        }

        $this->info("Total: {$total} faltas pendientes.");

        return self::SUCCESS;
    }

    /**
     * El comando corre cada minuto; procesa la escuela cuando el minuto actual
     * (en su zona horaria) coincide con su hora de corte.
     */
    private function esHoraDeCorte(School $escuela): bool
    {
        $tz = $escuela->timezone ?: config('app.timezone');
        $ahora = Carbon::now($tz);
        $corte = Carbon::parse($ahora->toDateString().' '.$escuela->hora_corte_faltas, $tz);

        return $ahora->format('H:i') === $corte->format('H:i');
    }
}
