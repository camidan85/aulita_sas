<?php

namespace App\Http\Controllers;

use App\Models\Materia;
use App\Tenancy\TenantManager;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class MateriaController extends Controller
{
    public function index(): View
    {
        $materias = Materia::orderBy('nombre')->get();

        return view('materias.index', compact('materias'));
    }

    public function create(): View
    {
        return view('materias.create', ['materia' => new Materia]);
    }

    public function store(Request $request)
    {
        Materia::create($this->validated($request));

        return redirect()->route('materias.index')->with('status', 'Materia creada.');
    }

    public function edit(Materia $materia): View
    {
        return view('materias.edit', compact('materia'));
    }

    public function update(Request $request, Materia $materia)
    {
        $materia->update($this->validated($request, $materia));

        return redirect()->route('materias.index')->with('status', 'Materia actualizada.');
    }

    public function destroy(Materia $materia)
    {
        $materia->delete();

        return redirect()->route('materias.index')->with('status', 'Materia eliminada.');
    }

    private function validated(Request $request, ?Materia $materia = null): array
    {
        $schoolId = app(TenantManager::class)->schoolId();

        return $request->validate([
            'clave' => ['nullable', 'string', 'max:20'],
            'nombre' => [
                'required', 'string', 'max:100',
                Rule::unique('materias')->ignore($materia?->id)->where(fn ($q) => $q->where('school_id', $schoolId)),
            ],
        ]);
    }
}
