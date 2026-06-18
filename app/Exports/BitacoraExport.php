<?php

namespace App\Exports;

use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class BitacoraExport implements FromQuery, WithHeadings, WithMapping
{
    public function __construct(protected Builder $query) {}

    public function query()
    {
        return $this->query->with('user');
    }

    public function headings(): array
    {
        return ['Fecha', 'Usuario', 'Acción', 'Módulo', 'Descripción', 'IP', 'Navegador'];
    }

    public function map($bitacora): array
    {
        return [
            $bitacora->created_at?->format('Y-m-d H:i:s'),
            $bitacora->user?->name ?? 'Sistema',
            $bitacora->accion,
            $bitacora->modulo,
            $bitacora->descripcion,
            $bitacora->ip,
            $bitacora->user_agent,
        ];
    }
}
