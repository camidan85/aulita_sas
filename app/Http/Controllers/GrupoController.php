<?php

namespace App\Http\Controllers;

use App\Models\CicloEscolar;
use App\Models\Docente;
use App\Models\Grado;
use App\Models\Grupo;
use App\Tenancy\TenantManager;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class GrupoController extends Controller
{
    public function index(): View
    {
        $grupos = Grupo::with(['grado', 'ciclo', 'docenteTitular'])
            ->withCount('alumnos')
            ->get()
            ->sortBy(fn (Grupo $g) => $g->nombreCompleto())
            ->values();

        return view('grupos.index', compact('grupos'));
    }

    public function create(): View
    {
        return view('grupos.create', $this->formData(new Grupo));
    }

    public function store(Request $request)
    {
        Grupo::create($this->validated($request));

        return redirect()->route('grupos.index')->with('status', 'Grupo creado.');
    }

    public function edit(Grupo $grupo): View
    {
        return view('grupos.edit', $this->formData($grupo));
    }

    public function update(Request $request, Grupo $grupo)
    {
        $grupo->update($this->validated($request, $grupo));

        return redirect()->route('grupos.index')->with('status', 'Grupo actualizado.');
    }

    public function destroy(Grupo $grupo)
    {
        $grupo->delete();

        return redirect()->route('grupos.index')->with('status', 'Grupo eliminado.');
    }

    private function formData(Grupo $grupo): array
    {
        return [
            'grupo' => $grupo,
            'grados' => Grado::orderBy('nivel')->get(),
            'ciclos' => CicloEscolar::orderByDesc('vigente')->orderBy('nombre')->get(),
            'docentes' => Docente::orderBy('apellido_paterno')->get(),
        ];
    }

    private function validated(Request $request, ?Grupo $grupo = null): array
    {
        $schoolId = app(TenantManager::class)->schoolId();

        $data = $request->validate([
            'grado_id' => ['required', Rule::exists('grados', 'id')->where(fn ($q) => $q->where('school_id', $schoolId))],
            'nombre' => ['required', 'string', 'max:10'],
            'ciclo_id' => ['required', Rule::exists('ciclos_escolares', 'id')->where(fn ($q) => $q->where('school_id', $schoolId))],
            'docente_titular_id' => ['nullable', Rule::exists('docentes', 'id')->where(fn ($q) => $q->where('school_id', $schoolId))],
        ]);

        return $data;
    }
}
