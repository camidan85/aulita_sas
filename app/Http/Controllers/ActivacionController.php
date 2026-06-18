<?php

namespace App\Http\Controllers;

use App\Models\AccountActivation;
use App\Services\ActivacionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class ActivacionController extends Controller
{
    public function __construct(protected ActivacionService $activacion) {}

    public function iniciarForm(): View
    {
        return view('activar.iniciar');
    }

    public function iniciar(Request $request)
    {
        $datos = $request->validate([
            'curp' => ['required', 'string', 'size:18'],
            'apellido_paterno' => ['required', 'string', 'max:100'],
            'nombre' => ['required', 'string', 'max:150'],
            'correo' => ['required', 'email', 'max:150', 'unique:users,email'],
            'telefono' => ['nullable', 'string', 'max:20'],
        ]);

        $activation = $this->activacion->iniciar($datos);

        if (! $activation) {
            return back()
                ->withInput()
                ->withErrors(['curp' => 'No encontramos un alumno con esos datos.']);
        }

        return view('activar.enviado', ['correo' => $activation->correo]);
    }

    public function crearForm(string $token): View
    {
        $activation = AccountActivation::where('token', $token)->first();

        abort_if(! $activation || ! $activation->vigente(), 410, 'El enlace expiró o ya fue usado.');

        return view('activar.crear', compact('activation'));
    }

    public function crear(Request $request, string $token)
    {
        $activation = AccountActivation::where('token', $token)->first();

        abort_if(! $activation || ! $activation->vigente(), 410, 'El enlace expiró o ya fue usado.');

        $request->validate([
            'password' => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()->symbols()],
        ]);

        $padre = $this->activacion->completar($activation, $request->string('password'));

        Auth::login($padre);

        return redirect()->route('portal.dashboard')
            ->with('status', '¡Cuenta activada! Bienvenido.');
    }
}
