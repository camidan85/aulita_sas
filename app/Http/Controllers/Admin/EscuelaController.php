<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Services\QrTokenService;
use App\Support\Modulos;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Panel del Super Admin: alta de escuelas y configuración por escuela
 * (plantilla de QR + módulos visibles).
 */
class EscuelaController extends Controller
{
    public function __construct(protected QrTokenService $qr) {}

    public function index(): View
    {
        $escuelas = School::withCount('users')->orderBy('nombre')->paginate(20);

        return view('admin.escuelas.index', compact('escuelas'));
    }

    /**
     * El Super Admin entra al contexto de una escuela para gestionarla.
     */
    public function seleccionar(Request $request, School $escuela)
    {
        $request->session()->put('admin_school_id', $escuela->id);

        return redirect()->route('dashboard')->with('status', "Gestionando: {$escuela->nombre}");
    }

    /**
     * Sale del contexto de escuela y vuelve al panel.
     */
    public function salir(Request $request)
    {
        $request->session()->forget('admin_school_id');

        return redirect()->route('admin.escuelas.index')->with('status', 'Saliste del contexto de escuela.');
    }

    public function create(): View
    {
        return view('admin.escuelas.create', ['modulos' => Modulos::DISPONIBLES]);
    }

    public function store(Request $request)
    {
        $data = $this->validar($request);
        $data['slug'] = ($data['slug'] ?? null) ?: Str::slug($data['nombre']);
        $data['modulos_ocultos'] = $this->modulosOcultos($request);

        School::create($data);

        return redirect()->route('admin.escuelas.index')->with('status', 'Escuela creada.');
    }

    public function edit(School $escuela): View
    {
        return view('admin.escuelas.edit', [
            'escuela' => $escuela,
            'modulos' => Modulos::DISPONIBLES,
        ]);
    }

    public function update(Request $request, School $escuela)
    {
        $data = $this->validar($request, $escuela);
        $data['slug'] = ($data['slug'] ?? null) ?: Str::slug($data['nombre']);
        $data['modulos_ocultos'] = $this->modulosOcultos($request);

        $formatoCambio = $escuela->qr_formato !== $data['qr_formato'];
        $escuela->update($data);

        // Si cambió la plantilla del QR, regenera el código de todos sus alumnos.
        if ($formatoCambio) {
            $this->qr->regenerarParaEscuela($escuela->fresh());
        }

        return redirect()->route('admin.escuelas.index')->with('status', 'Escuela actualizada.');
    }

    private function validar(Request $request, ?School $escuela = null): array
    {
        return $request->validate([
            'nombre' => ['required', 'string', 'max:150'],
            'slug' => ['nullable', 'string', 'max:150', Rule::unique('schools', 'slug')->ignore($escuela?->id)],
            'cct' => ['nullable', 'string', 'max:20'],
            'telefono' => ['nullable', 'string', 'max:20'],
            'correo' => ['nullable', 'email', 'max:150'],
            'hora_corte_faltas' => ['required', 'date_format:H:i'],
            'timezone' => ['required', 'string', 'max:40'],
            'umbral_riesgo_calif' => ['required', 'numeric', 'between:0,10'],
            'qr_formato' => ['required', 'string', 'max:100'],
            'estatus' => ['required', Rule::in(['activa', 'suspendida', 'baja'])],
        ]);
    }

    /**
     * @return array<int, string> claves de módulos desactivados (no marcados)
     */
    private function modulosOcultos(Request $request): array
    {
        $activos = array_keys($request->input('modulos', []));

        return array_values(array_diff(array_keys(Modulos::DISPONIBLES), $activos));
    }
}
