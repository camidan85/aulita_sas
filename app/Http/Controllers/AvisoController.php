<?php

namespace App\Http\Controllers;

use App\Models\Aviso;
use App\Models\AvisoAdjunto;
use App\Models\Grado;
use App\Models\Grupo;
use App\Services\AvisoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AvisoController extends Controller
{
    public function __construct(protected AvisoService $avisos) {}

    public function index(): View
    {
        $avisos = Aviso::with('publicadoPor')
            ->latest('fecha_publicacion')
            ->paginate(15);

        return view('avisos.index', compact('avisos'));
    }

    public function create(): View
    {
        return view('avisos.create', [
            'alcances' => Aviso::ALCANCES,
            'grados' => Grado::orderBy('nivel')->get(),
            'grupos' => Grupo::with('grado')->get()->sortBy(fn ($g) => $g->nombreCompleto())->values(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'titulo' => ['required', 'string', 'max:150'],
            'contenido' => ['required', 'string'],
            'alcance' => ['required', Rule::in(array_keys(Aviso::ALCANCES))],
            'target_id' => ['nullable', 'integer'],
            'requiere_firma' => ['nullable', 'boolean'],
            'adjuntos' => ['nullable', 'array', 'max:6'],
            'adjuntos.*' => ['file', 'max:20480', 'mimes:jpg,jpeg,png,webp,pdf,doc,docx,mp4,mov'],
        ]);

        $aviso = $this->avisos->publicar([
            'titulo' => $validated['titulo'],
            'contenido' => $validated['contenido'],
            'alcance' => $validated['alcance'],
            'target_id' => $validated['alcance'] === 'escuela' ? null : ($validated['target_id'] ?? null),
            'requiere_firma' => $request->boolean('requiere_firma'),
            'publicado_por' => $request->user()->id,
            'fecha_publicacion' => now(),
        ], $request->file('adjuntos', []));

        return redirect()->route('avisos.show', $aviso)
            ->with('status', 'Aviso publicado.');
    }

    public function show(Aviso $aviso): View
    {
        $aviso->load('publicadoPor', 'adjuntos', 'firmas.user');

        return view('avisos.show', compact('aviso'));
    }

    public function descargarAdjunto(AvisoAdjunto $adjunto): StreamedResponse
    {
        abort_unless(Storage::disk('local')->exists($adjunto->path), 404);

        return Storage::disk('local')->download($adjunto->path, $adjunto->nombre_original);
    }
}
