<?php

namespace App\Http\Controllers;

use App\Models\AlertaRiesgo;
use Illuminate\View\View;

class AlertaRiesgoController extends Controller
{
    public function index(): View
    {
        $alertas = AlertaRiesgo::with('alumno.grupo.grado')
            ->orderBy('atendida')
            ->orderByDesc('generada_en')
            ->paginate(20);

        return view('alertas.index', compact('alertas'));
    }

    public function atender(AlertaRiesgo $alerta)
    {
        $alerta->update(['atendida' => true]);

        return back()->with('status', 'Alerta marcada como atendida.');
    }
}
