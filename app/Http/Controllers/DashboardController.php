<?php

namespace App\Http\Controllers;

use App\Models\Alumno;
use App\Models\Asistencia;
use App\Models\Aviso;
use App\Models\Calificacion;
use App\Models\Docente;
use App\Models\Grupo;
use App\Models\Reporte;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Los padres tienen su propio portal; el Super Admin su panel de escuelas.
        if (auth()->user()?->hasRole('padre')) {
            return redirect()->route('portal.dashboard');
        }

        if (auth()->user()?->hasRole('super_admin')) {
            return redirect()->route('admin.escuelas.index');
        }

        $hoy = now()->toDateString();
        $inicioMes = now()->startOfMonth();

        $asistenciasHoy = Asistencia::whereDate('fecha', $hoy)
            ->selectRaw('estatus, count(*) as c')
            ->groupBy('estatus')
            ->pluck('c', 'estatus');

        $kpis = [
            'alumnos' => Alumno::count(),
            'docentes' => Docente::count(),
            'grupos' => Grupo::count(),
            'asistencias_hoy' => (int) ($asistenciasHoy['presente'] ?? 0),
            'retardos_hoy' => (int) ($asistenciasHoy['retardo'] ?? 0),
            'faltas_hoy' => (int) (($asistenciasHoy['falta'] ?? 0) + ($asistenciasHoy['falta_pendiente'] ?? 0)),
            'reportes_mes' => Reporte::where('fecha', '>=', $inicioMes->toDateString())->count(),
            'felicitaciones_mes' => Reporte::where('tipo', 'felicitacion')->where('fecha', '>=', $inicioMes->toDateString())->count(),
            'avisos_mes' => Aviso::where('fecha_publicacion', '>=', $inicioMes)->count(),
        ];

        return view('dashboard', [
            'kpis' => $kpis,
            'asistenciaSemanal' => $this->asistenciaSemanal(),
            'rendimientoPorGrupo' => $this->rendimientoPorGrupo(),
            'conductaPorTipo' => $this->conductaPorTipo($inicioMes),
        ]);
    }

    /**
     * Conteo de presente/retardo/falta por día en los últimos 7 días.
     */
    private function asistenciaSemanal(): array
    {
        $desde = now()->subDays(6)->startOfDay();

        $registros = Asistencia::whereDate('fecha', '>=', $desde->toDateString())
            ->selectRaw('fecha, estatus, count(*) as c')
            ->groupBy('fecha', 'estatus')
            ->get();

        $labels = [];
        $presente = $retardo = $falta = [];

        for ($i = 6; $i >= 0; $i--) {
            $dia = now()->subDays($i);
            $fecha = $dia->toDateString();
            $labels[] = $dia->translatedFormat('D d');

            $delDia = $registros->filter(fn ($r) => Carbon::parse($r->fecha)->toDateString() === $fecha);
            $presente[] = (int) ($delDia->firstWhere('estatus', 'presente')->c ?? 0);
            $retardo[] = (int) ($delDia->firstWhere('estatus', 'retardo')->c ?? 0);
            $falta[] = (int) ($delDia->whereIn('estatus', ['falta', 'falta_pendiente'])->sum('c'));
        }

        return compact('labels', 'presente', 'retardo', 'falta');
    }

    /**
     * Promedio de calificaciones por grupo (rendimiento académico).
     */
    private function rendimientoPorGrupo(): array
    {
        $labels = [];
        $promedios = [];

        Grupo::with('grado')->get()->each(function (Grupo $grupo) use (&$labels, &$promedios) {
            $avg = Calificacion::whereIn('alumno_id', $grupo->alumnos()->select('id'))->avg('calificacion');

            if ($avg !== null) {
                $labels[] = $grupo->nombreCompleto();
                $promedios[] = round((float) $avg, 2);
            }
        });

        return compact('labels', 'promedios');
    }

    /**
     * Reportes de conducta por tipo en el mes en curso.
     */
    private function conductaPorTipo(Carbon $desde): array
    {
        $datos = Reporte::where('fecha', '>=', $desde->toDateString())
            ->selectRaw('tipo, count(*) as c')
            ->groupBy('tipo')
            ->pluck('c', 'tipo');

        $labels = [];
        $valores = [];
        foreach (Reporte::TIPOS as $tipo => $label) {
            if (($datos[$tipo] ?? 0) > 0) {
                $labels[] = $label;
                $valores[] = (int) $datos[$tipo];
            }
        }

        return compact('labels', 'valores');
    }
}
