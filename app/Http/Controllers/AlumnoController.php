<?php

namespace App\Http\Controllers;

use App\Exports\AlumnosPlantillaExport;
use App\Http\Requests\StoreAlumnoRequest;
use App\Http\Requests\UpdateAlumnoRequest;
use App\Imports\AlumnosImport;
use App\Models\Alumno;
use App\Models\Grupo;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;

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

    public function plantilla()
    {
        abort_unless(request()->user()->can('alumnos.crear'), 403);

        return Excel::download(new AlumnosPlantillaExport, 'plantilla-alumnos.xlsx');
    }

    public function importar(Request $request)
    {
        abort_unless($request->user()->can('alumnos.crear'), 403);

        $request->validate([
            'archivo' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:5120'],
        ]);

        $import = new AlumnosImport;
        Excel::import($import, $request->file('archivo'));

        $msg = "Importados: {$import->importados}. Omitidos: {$import->omitidos}.";

        return redirect()->route('alumnos.index')
            ->with('status', $msg)
            ->with('import_errores', array_slice($import->errores, 0, 15));
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
