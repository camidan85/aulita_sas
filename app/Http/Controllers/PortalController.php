<?php

namespace App\Http\Controllers;

use App\Models\Alumno;
use App\Models\Aviso;
use App\Models\Reporte;
use App\Services\CalificacionService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PortalController extends Controller
{
    public function dashboard(Request $request, CalificacionService $calificaciones): View
    {
        $padre = $request->user();

        $hijos = $padre->hijos()->with('grupo.grado')->get()->map(function (Alumno $hijo) use ($calificaciones, $padre) {
            $asis = $hijo->asistencias()
                ->whereYear('fecha', now()->year)
                ->whereMonth('fecha', now()->month)
                ->selectRaw('estatus, count(*) as c')
                ->groupBy('estatus')
                ->pluck('c', 'estatus');

            return [
                'alumno' => $hijo,
                'asistencia' => [
                    'presente' => (int) ($asis['presente'] ?? 0),
                    'retardo' => (int) ($asis['retardo'] ?? 0),
                    'falta' => (int) (($asis['falta'] ?? 0) + ($asis['falta_pendiente'] ?? 0)),
                ],
                'promedio' => $calificaciones->promedioAlumnoGeneral($hijo),
                'materiasEnRiesgo' => $calificaciones->materiasEnRiesgo($hijo),
                'pendientesFirma' => Reporte::where('alumno_id', $hijo->id)
                    ->where('requiere_firma', true)
                    ->whereDoesntHave('firmas', fn ($q) => $q->where('user_id', $padre->id))
                    ->get(),
                'reportes' => Reporte::where('alumno_id', $hijo->id)->latest('fecha')->take(5)->get(),
                'avisos' => $this->avisosPara($hijo),
            ];
        });

        return view('portal.dashboard', compact('hijos'));
    }

    private function avisosPara(Alumno $hijo)
    {
        return Aviso::where(function ($q) use ($hijo) {
            $q->where('alcance', 'escuela')
                ->orWhere(fn ($s) => $s->where('alcance', 'grupo')->where('target_id', $hijo->grupo_id))
                ->orWhere(fn ($s) => $s->where('alcance', 'grado')->where('target_id', $hijo->grupo?->grado_id))
                ->orWhere(fn ($s) => $s->where('alcance', 'alumno')->where('target_id', $hijo->id));
        })
            ->latest('fecha_publicacion')
            ->take(5)
            ->get();
    }
}
