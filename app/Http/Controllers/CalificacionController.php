<?php

namespace App\Http\Controllers;

use App\Exports\CalificacionesExport;
use App\Models\Alumno;
use App\Models\Calificacion;
use App\Models\Grupo;
use App\Models\Materia;
use App\Models\Periodo;
use App\Services\CalificacionService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;

class CalificacionController extends Controller
{
    public function __construct(protected CalificacionService $calificaciones) {}

    public function index(Request $request)
    {
        $grupos = Grupo::with('grado')->get()->sortBy(fn ($g) => $g->nombreCompleto())->values();
        $periodos = Periodo::orderBy('fecha_inicio')->get();
        $materias = Materia::orderBy('nombre')->get();

        $grupo = $request->filled('grupo_id') ? Grupo::with('grado')->find($request->integer('grupo_id')) : null;
        $periodo = $request->filled('periodo_id') ? Periodo::find($request->integer('periodo_id')) : null;

        $alumnos = collect();
        if ($grupo && $periodo) {
            $alumnos = $grupo->alumnos()->orderBy('apellido_paterno')->get()
                ->map(fn (Alumno $a) => [
                    'alumno' => $a,
                    'promedio' => $this->calificaciones->promedioAlumnoPeriodo($a, $periodo),
                ]);
        }

        $promedioGrupo = ($grupo && $periodo) ? $this->calificaciones->promedioGrupo($grupo, $periodo) : null;

        return view('calificaciones.index', compact(
            'grupos', 'periodos', 'materias', 'grupo', 'periodo', 'alumnos', 'promedioGrupo'
        ));
    }

    public function capturar(Request $request)
    {
        $data = $this->resolverSeleccion($request);

        $alumnos = $data['grupo']->alumnos()->orderBy('apellido_paterno')->get();

        $existentes = $data['materia']->id
            ? Calificacion::where('materia_id', $data['materia']->id)
                ->where('periodo_id', $data['periodo']->id)
                ->pluck('calificacion', 'alumno_id')
            : collect();

        return view('calificaciones.capturar', $data + compact('alumnos', 'existentes'));
    }

    public function guardar(Request $request)
    {
        $validated = $request->validate([
            'grupo_id' => ['required', Rule::exists('grupos', 'id')],
            'materia_id' => ['required', Rule::exists('materias', 'id')],
            'periodo_id' => ['required', Rule::exists('periodos', 'id')],
            'calificaciones' => ['array'],
            'calificaciones.*' => ['nullable', 'numeric', 'between:0,10'],
        ]);

        $grupo = Grupo::findOrFail($validated['grupo_id']);
        $materia = Materia::findOrFail($validated['materia_id']);
        $periodo = Periodo::findOrFail($validated['periodo_id']);

        $n = $this->calificaciones->capturar(
            $grupo, $materia, $periodo,
            $validated['calificaciones'] ?? [],
            $request->user()->id,
        );

        return redirect()
            ->route('calificaciones.index', ['grupo_id' => $grupo->id, 'periodo_id' => $periodo->id])
            ->with('status', "{$n} calificaciones guardadas.");
    }

    public function boleta(Alumno $alumno)
    {
        $data = $this->calificaciones->boletaData($alumno);
        $data['titulo'] = 'Boleta de calificaciones';

        $pdf = Pdf::loadView('calificaciones.pdf.boleta', $data);

        return $pdf->download('boleta-'.$alumno->matricula.'.pdf');
    }

    public function kardex(Alumno $alumno)
    {
        $data = $this->calificaciones->boletaData($alumno);
        $data['titulo'] = 'Kardex académico';

        $pdf = Pdf::loadView('calificaciones.pdf.kardex', $data);

        return $pdf->download('kardex-'.$alumno->matricula.'.pdf');
    }

    public function exportar(Request $request)
    {
        $request->validate([
            'grupo_id' => ['required', Rule::exists('grupos', 'id')],
            'periodo_id' => ['required', Rule::exists('periodos', 'id')],
        ]);

        $grupo = Grupo::with('grado')->findOrFail($request->integer('grupo_id'));
        $periodo = Periodo::findOrFail($request->integer('periodo_id'));

        $nombre = 'calificaciones-'.$grupo->nombreCompleto().'-'.$periodo->nombre.'.xlsx';

        return Excel::download(new CalificacionesExport($grupo, $periodo), $nombre);
    }

    /**
     * @return array{grupo: Grupo, materia: Materia, periodo: Periodo}
     */
    private function resolverSeleccion(Request $request): array
    {
        $request->validate([
            'grupo_id' => ['required', Rule::exists('grupos', 'id')],
            'materia_id' => ['required', Rule::exists('materias', 'id')],
            'periodo_id' => ['required', Rule::exists('periodos', 'id')],
        ]);

        return [
            'grupo' => Grupo::with('grado')->findOrFail($request->integer('grupo_id')),
            'materia' => Materia::findOrFail($request->integer('materia_id')),
            'periodo' => Periodo::findOrFail($request->integer('periodo_id')),
        ];
    }
}
