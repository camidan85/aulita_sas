<?php

namespace App\Services;

use App\Models\Alumno;
use App\Models\Calificacion;
use App\Models\Grupo;
use App\Models\Materia;
use App\Models\Periodo;
use App\Models\School;
use App\Tenancy\TenantManager;
use Illuminate\Support\Collection;

class CalificacionService extends BaseService
{
    public function __construct(protected TenantManager $tenant) {}

    /**
     * Captura/actualiza calificaciones de un grupo en una materia y periodo.
     *
     * @param  array<int|string, mixed>  $valores  [alumno_id => calificacion]
     * @return int Calificaciones guardadas.
     */
    public function capturar(Grupo $grupo, Materia $materia, Periodo $periodo, array $valores, ?int $capturadoPor = null): int
    {
        $guardadas = 0;

        foreach ($valores as $alumnoId => $calificacion) {
            if ($calificacion === null || $calificacion === '') {
                continue;
            }

            Calificacion::updateOrCreate(
                ['alumno_id' => (int) $alumnoId, 'materia_id' => $materia->id, 'periodo_id' => $periodo->id],
                [
                    'school_id' => $this->tenant->schoolId(),
                    'calificacion' => $calificacion,
                    'capturado_por' => $capturadoPor,
                ],
            );

            $guardadas++;
        }

        return $guardadas;
    }

    public function promedioAlumnoPeriodo(Alumno $alumno, Periodo $periodo): ?float
    {
        return $this->redondear(
            Calificacion::where('alumno_id', $alumno->id)->where('periodo_id', $periodo->id)->avg('calificacion')
        );
    }

    public function promedioAlumnoGeneral(Alumno $alumno): ?float
    {
        return $this->redondear(
            Calificacion::where('alumno_id', $alumno->id)->avg('calificacion')
        );
    }

    public function promedioMateriaGrupo(Grupo $grupo, Materia $materia, Periodo $periodo): ?float
    {
        return $this->redondear(
            Calificacion::where('materia_id', $materia->id)
                ->where('periodo_id', $periodo->id)
                ->whereIn('alumno_id', $grupo->alumnos()->select('id'))
                ->avg('calificacion')
        );
    }

    public function promedioGrupo(Grupo $grupo, Periodo $periodo): ?float
    {
        return $this->redondear(
            Calificacion::where('periodo_id', $periodo->id)
                ->whereIn('alumno_id', $grupo->alumnos()->select('id'))
                ->avg('calificacion')
        );
    }

    /**
     * Materias cuyo promedio del alumno está por debajo del umbral de la escuela (RN-C03).
     *
     * @return Collection<int, array{materia: Materia, promedio: float}>
     */
    public function materiasEnRiesgo(Alumno $alumno): Collection
    {
        $umbral = (float) (School::find($alumno->school_id)?->umbral_riesgo_calif ?? 6.0);

        return Calificacion::where('alumno_id', $alumno->id)
            ->selectRaw('materia_id, AVG(calificacion) as promedio')
            ->groupBy('materia_id')
            ->get()
            ->filter(fn ($fila) => (float) $fila->promedio < $umbral)
            ->map(fn ($fila) => [
                'materia' => Materia::find($fila->materia_id),
                'promedio' => round((float) $fila->promedio, 2),
            ])
            ->values();
    }

    /**
     * Datos para boleta/kardex: matriz materia × periodo + promedios.
     */
    public function boletaData(Alumno $alumno): array
    {
        $calificaciones = Calificacion::where('alumno_id', $alumno->id)
            ->with('materia', 'periodo')
            ->get();

        $periodos = Periodo::orderBy('fecha_inicio')->get();
        $materias = $calificaciones->pluck('materia')->unique('id')->sortBy('nombre')->values();

        $matriz = [];
        foreach ($calificaciones as $c) {
            $matriz[$c->materia_id][$c->periodo_id] = $c->calificacion;
        }

        $promedioPorPeriodo = [];
        foreach ($periodos as $periodo) {
            $promedioPorPeriodo[$periodo->id] = $this->promedioAlumnoPeriodo($alumno, $periodo);
        }

        return [
            'alumno' => $alumno,
            'periodos' => $periodos,
            'materias' => $materias,
            'matriz' => $matriz,
            'promedioPorPeriodo' => $promedioPorPeriodo,
            'promedioGeneral' => $this->promedioAlumnoGeneral($alumno),
            'materiasEnRiesgo' => $this->materiasEnRiesgo($alumno),
        ];
    }

    private function redondear($valor): ?float
    {
        return $valor !== null ? round((float) $valor, 2) : null;
    }
}
