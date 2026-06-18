<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <style>
        * { font-family: DejaVu Sans, sans-serif; }
        body { font-size: 10px; color: #222; }
        h1 { font-size: 16px; margin: 0 0 8px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #bbb; padding: 4px 6px; text-align: left; }
        th { background: #f0f3f8; }
    </style>
</head>
<body>
    <h1>Bitácora de auditoría</h1>
    <table>
        <thead>
            <tr><th>Fecha</th><th>Usuario</th><th>Acción</th><th>Módulo</th><th>Descripción</th><th>IP</th></tr>
        </thead>
        <tbody>
            @foreach ($registros as $r)
                <tr>
                    <td>{{ $r->created_at?->format('Y-m-d H:i:s') }}</td>
                    <td>{{ $r->user?->name ?? 'Sistema' }}</td>
                    <td>{{ $r->accion }}</td>
                    <td>{{ $r->modulo }}</td>
                    <td>{{ $r->descripcion }}</td>
                    <td>{{ $r->ip }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
