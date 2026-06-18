<?php

namespace App\Http\Controllers;

use App\Models\Tutor;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TutorController extends Controller
{
    public function index(): View
    {
        $tutores = Tutor::orderBy('nombre')->paginate(15);

        return view('tutores.index', compact('tutores'));
    }

    public function create(): View
    {
        return view('tutores.create', ['tutor' => new Tutor]);
    }

    public function store(Request $request)
    {
        Tutor::create($this->validated($request));

        return redirect()->route('tutores.index')->with('status', 'Tutor creado.');
    }

    public function edit(Tutor $tutor): View
    {
        return view('tutores.edit', compact('tutor'));
    }

    public function update(Request $request, Tutor $tutor)
    {
        $tutor->update($this->validated($request));

        return redirect()->route('tutores.index')->with('status', 'Tutor actualizado.');
    }

    public function destroy(Tutor $tutor)
    {
        $tutor->delete();

        return redirect()->route('tutores.index')->with('status', 'Tutor eliminado.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'nombre' => ['required', 'string', 'max:150'],
            'correo' => ['nullable', 'email', 'max:150'],
            'telefono' => ['nullable', 'string', 'max:20'],
            'parentesco' => ['nullable', 'string', 'max:40'],
        ]);
    }
}
