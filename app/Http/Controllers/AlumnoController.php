<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAlumnoRequest;
use App\Http\Requests\UpdateAlumnoRequest;
use App\Models\Alumno;
use App\Models\Grupo;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class AlumnoController extends Controller
{
    public function index(Request $request): View
    {
        $q = trim((string) $request->get('q'));

        $alumnos = Alumno::query()
            ->with('grupo.grado')
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('nombre', 'like', "%{$q}%")
                        ->orWhere('apellido_paterno', 'like', "%{$q}%")
                        ->orWhere('apellido_materno', 'like', "%{$q}%")
                        ->orWhere('matricula', 'like', "%{$q}%")
                        ->orWhere('curp', 'like', "%{$q}%");
                });
            })
            ->orderBy('apellido_paterno')
            ->paginate(15)
            ->withQueryString();

        return view('alumnos.index', compact('alumnos', 'q'));
    }

    public function create(): View
    {
        return view('alumnos.create', ['grupos' => $this->grupos()]);
    }

    public function store(StoreAlumnoRequest $request)
    {
        $alumno = Alumno::create($request->validated());

        return redirect()->route('alumnos.show', $alumno)
            ->with('status', 'Alumno registrado correctamente.');
    }

    public function show(Alumno $alumno): View
    {
        $alumno->load('grupo.grado', 'tutores', 'expedienteMedico', 'documentos');

        return view('alumnos.show', compact('alumno'));
    }

    public function edit(Alumno $alumno): View
    {
        return view('alumnos.edit', ['alumno' => $alumno, 'grupos' => $this->grupos()]);
    }

    public function update(UpdateAlumnoRequest $request, Alumno $alumno)
    {
        $alumno->update($request->validated());

        return redirect()->route('alumnos.show', $alumno)
            ->with('status', 'Alumno actualizado correctamente.');
    }

    public function destroy(Alumno $alumno)
    {
        abort_unless(request()->user()->can('alumnos.eliminar'), 403);

        $alumno->delete();

        return redirect()->route('alumnos.index')
            ->with('status', 'Alumno dado de baja.');
    }

    /**
     * @return Collection<int, Grupo>
     */
    private function grupos()
    {
        return Grupo::with('grado')->get()
            ->sortBy(fn (Grupo $g) => $g->nombreCompleto())
            ->values();
    }
}
