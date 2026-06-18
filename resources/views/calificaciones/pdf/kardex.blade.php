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
        .final { font-weight: bold; font-size: 14px; margin-top: 14px; }
    </style>
</head>
<body>
    <h1>{{ $titulo ?? 'Kardex académico' }}</h1>
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
                <th>Final</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($materias as $m)
                @php
                    $valores = [];
                    foreach ($periodos as $p) { $valores[$p->id] = $matriz[$m->id][$p->id] ?? null; }
                    $nums = array_filter($valores, fn ($v) => $v !== null);
                    $final = count($nums) ? round(array_sum($nums) / count($nums), 2) : null;
                @endphp
                <tr>
                    <td class="left">{{ $m->nombre }}</td>
                    @foreach ($periodos as $p)
                        <td>{{ $valores[$p->id] ?? '—' }}</td>
                    @endforeach
                    <td>{{ $final ?? '—' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="final">Promedio general acumulado: {{ $promedioGeneral ?? '—' }}</div>
</body>
</html>
