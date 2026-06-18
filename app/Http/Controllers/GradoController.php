<?php

namespace App\Http\Controllers;

use App\Models\Grado;
use App\Tenancy\TenantManager;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class GradoController extends Controller
{
    public function index(): View
    {
        $grados = Grado::withCount('grupos')->orderBy('nivel')->get();

        return view('grados.index', compact('grados'));
    }

    public function create(): View
    {
        return view('grados.create', ['grado' => new Grado]);
    }

    public function store(Request $request)
    {
        Grado::create($this->validated($request));

        return redirect()->route('grados.index')->with('status', 'Grado creado.');
    }

    public function edit(Grado $grado): View
    {
        return view('grados.edit', compact('grado'));
    }

    public function update(Request $request, Grado $grado)
    {
        $grado->update($this->validated($request, $grado));

        return redirect()->route('grados.index')->with('status', 'Grado actualizado.');
    }

    public function destroy(Grado $grado)
    {
        $grado->delete();

        return redirect()->route('grados.index')->with('status', 'Grado eliminado.');
    }

    private function validated(Request $request, ?Grado $grado = null): array
    {
        $schoolId = app(TenantManager::class)->schoolId();

        return $request->validate([
            'nombre' => ['required', 'string', 'max:30'],
            'nivel' => [
                'required', 'integer', 'between:1,3',
                Rule::unique('grados')->ignore($grado?->id)->where(fn ($q) => $q->where('school_id', $schoolId)),
            ],
        ]);
    }
}
