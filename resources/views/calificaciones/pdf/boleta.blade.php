<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <style>
        * { font-family: DejaVu Sans, sans-serif; }
        body { font-size: 12px; color: #222; }
        h1 { font-size: 18px; margin: 0 0 2px; }
        .muted { color: #666; font-size: 11px; }
        table { width: 100%; border-collapse: collapse; margin-top: 14px; }
        th, td { border: 1px solid #bbb; padding: 6px 8px; text-align: center; }
        th { background: #f0f3f8; }
        td.left, th.left { text-align: left; }
        .resumen { margin-top: 14px; font-size: 12px; }
        .riesgo { color: #b00; }
        .badge-final { font-weight: bold; }
    </style>
</head>
<body>
    <h1>{{ $titulo ?? 'Boleta de calificaciones' }}</h1>
    <div class="muted">
        {{ $alumno->nombreCompleto() }} · Matrícula {{ $alumno->matricula }}
        @if ($alumno->grupo) · Grupo {{ $alumno->grupo->nombreCompleto() }} @endif
    </div>

    <table>
        <thead>
            <tr>
                <th class="left">Materia</th>
                @foreach ($periodos as $p)
                    <th>{{ str_replace('_', ' ', $p->nombre) }}</th>
                @endforeach
                <th>Promedio</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($materias as $m)
                @php
                    $valores = [];
                    foreach ($periodos as $p) { $valores[$p->id] = $matriz[$m->id][$p->id] ?? null; }
                    $nums = array_filter($valores, fn ($v) => $v !== null);
                    $promMateria = count($nums) ? round(array_sum($nums) / count($nums), 2) : null;
                @endphp
                <tr>
                    <td class="left">{{ $m->nombre }}</td>
                    @foreach ($periodos as $p)
                        <td>{{ $valores[$p->id] ?? '—' }}</td>
                    @endforeach
                    <td class="badge-final {{ ($promMateria !== null && $promMateria < 6) ? 'riesgo' : '' }}">{{ $promMateria ?? '—' }}</td>
                </tr>
            @empty
                <tr><td class="left" colspan="{{ count($periodos) + 2 }}">Sin calificaciones capturadas.</td></tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <th class="left">Promedio del periodo</th>
                @foreach ($periodos as $p)
                    <th>{{ $promedioPorPeriodo[$p->id] ?? '—' }}</th>
                @endforeach
                <th>{{ $promedioGeneral ?? '—' }}</th>
            </tr>
        </tfoot>
    </table>

    <div class="resumen">
        <strong>Promedio general:</strong> {{ $promedioGeneral ?? '—' }}
        @if ($materiasEnRiesgo->isNotEmpty())
            <div class="riesgo" style="margin-top:6px;">
                <strong>Materias en riesgo:</strong>
                {{ $materiasEnRiesgo->map(fn ($r) => $r['materia']?->nombre.' ('.$r['promedio'].')')->implode(', ') }}
            </div>
        @endif
    </div>
</body>
</html>
