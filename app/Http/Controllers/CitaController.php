<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CitaController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $citas = Cita::with('alumno', 'solicitante')
            ->when($user->hasRole('padre'), fn ($q) => $q->where('solicitante_user_id', $user->id))
            ->latest('fecha_solicitada')
            ->paginate(15);

        return view('citas.index', compact('citas'));
    }

    public function create(Request $request): View
    {
        $hijos = $request->user()->hijos()->get();

        return view('citas.create', [
            'hijos' => $hijos,
            'roles' => Cita::ROLES,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'alumno_id' => ['required', Rule::exists('alumnos', 'id')],
            'con_rol' => ['required', Rule::in(array_keys(Cita::ROLES))],
            'motivo' => ['required', 'string'],
            'fecha_solicitada' => ['required', 'date', 'after_or_equal:today'],
            'hora_solicitada' => ['nullable', 'date_format:H:i'],
        ]);

        abort_unless($request->user()->esHijo((int) $validated['alumno_id']), 403);

        Cita::create($validated + [
            'solicitante_user_id' => $request->user()->id,
            'estatus' => 'solicitada',
        ]);

        return redirect()->route('citas.index')->with('status', 'Cita solicitada.');
    }

    public function actualizarEstatus(Request $request, Cita $cita)
    {
        $validated = $request->validate([
            'estatus' => ['required', Rule::in(['solicitada', 'confirmada', 'reprogramada', 'cancelada', 'atendida'])],
        ]);

        $cita->update($validated + ['con_user_id' => $request->user()->id]);

        return back()->with('status', 'Cita actualizada.');
    }
}
