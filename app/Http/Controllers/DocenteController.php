<?php

namespace App\Http\Controllers;

use App\Models\Docente;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class DocenteController extends Controller
{
    public function index(): View
    {
        $docentes = Docente::orderBy('apellido_paterno')->paginate(15);

        return view('docentes.index', compact('docentes'));
    }

    public function create(): View
    {
        return view('docentes.create', ['docente' => new Docente]);
    }

    public function store(Request $request)
    {
        Docente::create($this->validated($request));

        return redirect()->route('docentes.index')->with('status', 'Docente creado.');
    }

    public function edit(Docente $docente): View
    {
        return view('docentes.edit', compact('docente'));
    }

    public function update(Request $request, Docente $docente)
    {
        $docente->update($this->validated($request));

        return redirect()->route('docentes.index')->with('status', 'Docente actualizado.');
    }

    public function destroy(Docente $docente)
    {
        $docente->delete();

        return redirect()->route('docentes.index')->with('status', 'Docente eliminado.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'numero_empleado' => ['nullable', 'string', 'max:30'],
            'nombre' => ['required', 'string', 'max:100'],
            'apellido_paterno' => ['required', 'string', 'max:100'],
            'apellido_materno' => ['nullable', 'string', 'max:100'],
            'telefono' => ['nullable', 'string', 'max:20'],
            'estatus' => ['required', Rule::in(['activo', 'inactivo'])],
        ]);
    }
}
