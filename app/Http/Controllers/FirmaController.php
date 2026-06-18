<?php

namespace App\Http\Controllers;

use App\Models\Aviso;
use App\Models\Reporte;
use App\Services\FirmaService;
use Illuminate\Http\Request;

class FirmaController extends Controller
{
    public function __construct(protected FirmaService $firmas) {}

    public function firmarReporte(Request $request, Reporte $reporte)
    {
        $user = $request->user();

        // Un padre solo puede firmar reportes de sus hijos (RN-F01).
        if ($user->hasRole('padre')) {
            abort_unless($user->esHijo($reporte->alumno_id), 403);
        }

        $this->firmas->firmar($reporte, $user, $request->ip());

        return back()->with('status', 'Firma de enterado registrada.');
    }

    public function firmarAviso(Request $request, Aviso $aviso)
    {
        $this->firmas->firmar($aviso, $request->user(), $request->ip());

        return back()->with('status', 'Firma de enterado registrada.');
    }
}
