<?php

namespace App\Http\Controllers;

use App\Models\Alumno;
use App\Models\Evidencia;
use App\Models\Reporte;
use App\Services\ReporteService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReporteController extends Controller
{
    public function __construct(protected ReporteService $reportes) {}

    public function index(Request $request): View
    {
        $reportes = Reporte::with('alumno', 'profesor')
            ->when($request->filled('alumno_id'), fn ($q) => $q->where('alumno_id', $request->integer('alumno_id')))
            ->latest('fecha')
            ->paginate(15)
            ->withQueryString();

        return view('reportes.index', compact('reportes'));
    }

    public function create(Request $request): View
    {
        $alumnos = Alumno::orderBy('apellido_paterno')->get();

        return view('reportes.create', [
            'alumnos' => $alumnos,
            'tipos' => Reporte::TIPOS,
            'alumnoSeleccionado' => $request->integer('alumno_id'),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'alumno_id' => ['required', Rule::exists('alumnos', 'id')],
            'tipo' => ['required', Rule::in(array_keys(Reporte::TIPOS))],
            'descripcion' => ['required', 'string'],
            'requiere_firma' => ['nullable', 'boolean'],
            'evidencias' => ['nullable', 'array', 'max:6'],
            'evidencias.*' => ['file', 'max:20480', 'mimes:jpg,jpeg,png,webp,pdf,doc,docx,mp4,mov'],
        ]);

        $reporte = $this->reportes->crear([
            'alumno_id' => $validated['alumno_id'],
            'profesor_id' => $request->user()->id,
            'tipo' => $validated['tipo'],
            'descripcion' => $validated['descripcion'],
            'fecha' => now()->toDateString(),
            'hora' => now()->toTimeString(),
            'requiere_firma' => $request->boolean('requiere_firma'),
        ], $request->file('evidencias', []));

        return redirect()->route('reportes.show', $reporte)
            ->with('status', 'Reporte registrado.');
    }

    public function show(Reporte $reporte): View
    {
        $reporte->load('alumno', 'profesor', 'evidencias', 'firmas.user');

        return view('reportes.show', compact('reporte'));
    }

    public function descargarEvidencia(Evidencia $evidencia): StreamedResponse
    {
        abort_unless(Storage::disk('local')->exists($evidencia->path), 404);

        return Storage::disk('local')->download($evidencia->path, $evidencia->nombre_original);
    }
}
