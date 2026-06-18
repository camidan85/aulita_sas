<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

/**
 * Plantilla descargable para la carga masiva de alumnos.
 */
class AlumnosPlantillaExport implements FromArray, WithHeadings
{
    public function headings(): array
    {
        return [
            'matricula', 'nombre', 'apellido_paterno', 'apellido_materno', 'curp',
            'fecha_nacimiento', 'sexo', 'correo', 'telefono', 'grupo', 'estatus',
        ];
    }

    public function array(): array
    {
        // Fila de ejemplo (puedes borrarla antes de cargar).
        return [
            [
                'A0001', 'Juan', 'Pérez', 'López', 'PELJ100101HDFXXX01',
                '2010-01-01', 'M', 'juan@correo.com', '5215512345678', '1A', 'activo',
            ],
        ];
    }
}
