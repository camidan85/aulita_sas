<?php

namespace App\Exports;

use App\Models\Calificacion;
use App\Models\Grupo;
use App\Models\Periodo;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CalificacionesExport implements FromCollection, WithHeadings
{
    public function __construct(protected Grupo $grupo, protected Periodo $periodo) {}

    public function headings(): array
    {
        return ['Matrícula', 'Alumno', 'Materia', 'Periodo', 'Calificación'];
    }

    public function collection()
    {
        return Calificacion::with('alumno', 'materia', 'periodo')
            ->where('periodo_id', $this->periodo->id)
            ->whereIn('alumno_id', $this->grupo->alumnos()->select('id'))
            ->get()
            ->map(fn (Calificacion $c) => [
                $c->alumno->matricula,
                $c->alumno->nombreCompleto(),
                $c->materia->nombre,
                $c->periodo->nombre,
                $c->calificacion,
            ]);
    }
}
