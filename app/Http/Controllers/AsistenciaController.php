<?php

namespace App\Http\Controllers;

use App\Models\Alumno;
use App\Models\Asistencia;
use App\Services\AsistenciaService;
use App\Services\QrTokenService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AsistenciaController extends Controller
{
    public function __construct(
        protected AsistenciaService $asistencias,
        protected QrTokenService $qr,
    ) {}

    public function index(Request $request): View
    {
        $fecha = $request->date('fecha')?->toDateString() ?? now()->toDateString();

        $asistencias = Asistencia::with('alumno.grupo.grado', 'registradoPor')
            ->whereDate('fecha', $fecha)
            ->orderByDesc('hora')
            ->paginate(20)
            ->withQueryString();

        return view('asistencias.index', compact('asistencias', 'fecha'));
    }

    public function escanear(): View
    {
        return view('asistencias.escanear');
    }

    /**
     * Endpoint AJAX: recibe el contenido del QR y registra la asistencia.
     */
    public function registrar(Request $request): JsonResponse
    {
        $data = $request->validate([
            'contenido' => ['required', 'string', 'max:255'],
        ]);

        $alumno = $this->qr->resolverAlumno($data['contenido']);

        if (! $alumno) {
            return response()->json([
                'ok' => false,
                'mensaje' => 'QR inválido o de otra escuela.',
            ], 422);
        }

        $resultado = $this->asistencias->registrar($alumno, [
            'origen' => 'qr',
            'registrado_por' => $request->user()->id,
            'ip' => $request->ip(),
            'dispositivo' => substr((string) $request->userAgent(), 0, 255),
        ]);

        $asistencia = $resultado['asistencia'];

        return response()->json([
            'ok' => true,
            'estado' => $resultado['estado'],
            'duplicado' => $resultado['estado'] === 'duplicado',
            'alumno' => $alumno->nombreCompleto(),
            'grupo' => $alumno->grupo?->nombreCompleto(),
            'estatus' => $asistencia->estatus,
            'hora' => $asistencia->hora,
            'mensaje' => $this->mensaje($resultado['estado'], $asistencia->estatus),
        ]);
    }

    public function qr(Alumno $alumno): View
    {
        $token = $this->qr->tokenActivo($alumno);
        $svg = $this->qr->svg($token, 240);

        return view('asistencias.qr', compact('alumno', 'svg'));
    }

    private function mensaje(string $estado, string $estatus): string
    {
        if ($estado === 'duplicado') {
            return 'La asistencia ya estaba registrada hoy.';
        }

        return $estatus === 'presente'
            ? 'Asistencia registrada (presente).'
            : 'Registrado con retardo.';
    }
}
