<?php

namespace App\Imports;

use App\Models\Alumno;
use App\Models\Grupo;
use App\Tenancy\TenantManager;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date;

/**
 * Carga masiva de alumnos desde Excel. Cabeceras esperadas (fila 1):
 * matricula, nombre, apellido_paterno, apellido_materno, curp,
 * fecha_nacimiento, sexo, correo, telefono, grupo, estatus
 *
 * El codigo_qr se genera solo (observer) según la plantilla de la escuela.
 */
class AlumnosImport implements ToCollection, WithHeadingRow
{
    public int $importados = 0;

    public int $omitidos = 0;

    /** @var array<int, string> */
    public array $errores = [];

    /** @var Collection<int, Grupo>|null */
    private ?Collection $grupos = null;

    public function collection(Collection $rows): void
    {
        $schoolId = app(TenantManager::class)->schoolId();
        $this->grupos = Grupo::with('grado')->get();

        foreach ($rows as $i => $row) {
            $fila = $i + 2; // +1 cabecera, +1 base 1
            $matricula = trim((string) ($row['matricula'] ?? ''));
            $curp = strtoupper(trim((string) ($row['curp'] ?? '')));
            $nombre = trim((string) ($row['nombre'] ?? ''));
            $apaterno = trim((string) ($row['apellido_paterno'] ?? ''));

            if ($matricula === '' || $nombre === '' || $apaterno === '' || $curp === '') {
                $this->omitidos++;
                $this->errores[] = "Fila {$fila}: faltan datos obligatorios (matrícula, nombre, apellido paterno o CURP).";

                continue;
            }

            Alumno::updateOrCreate(
                ['school_id' => $schoolId, 'matricula' => $matricula],
                [
                    'curp' => $curp,
                    'nombre' => $nombre,
                    'apellido_paterno' => $apaterno,
                    'apellido_materno' => trim((string) ($row['apellido_materno'] ?? '')) ?: null,
                    'fecha_nacimiento' => $this->fecha($row['fecha_nacimiento'] ?? null),
                    'sexo' => $this->sexo($row['sexo'] ?? null),
                    'correo' => trim((string) ($row['correo'] ?? '')) ?: null,
                    'telefono' => trim((string) ($row['telefono'] ?? '')) ?: null,
                    'grupo_id' => $this->grupoId($row['grupo'] ?? null),
                    'estatus' => in_array($row['estatus'] ?? null, ['activo', 'baja', 'egresado', 'suspendido'], true)
                        ? $row['estatus'] : 'activo',
                ],
            );

            $this->importados++;
        }
    }

    private function fecha($valor): ?string
    {
        if (empty($valor)) {
            return null;
        }

        try {
            if (is_numeric($valor)) {
                return Date::excelToDateTimeObject((float) $valor)->format('Y-m-d');
            }

            return Carbon::parse((string) $valor)->format('Y-m-d');
        } catch (\Throwable) {
            return null;
        }
    }

    private function sexo($valor): ?string
    {
        $v = strtoupper(trim((string) $valor));

        return in_array($v, ['M', 'F', 'X'], true) ? $v : null;
    }

    private function grupoId($nombre): ?int
    {
        $nombre = trim((string) $nombre);
        if ($nombre === '' || ! $this->grupos) {
            return null;
        }

        return $this->grupos->first(fn (Grupo $g) => strcasecmp($g->nombreCompleto(), $nombre) === 0)?->id;
    }
}
