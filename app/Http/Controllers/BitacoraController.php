<?php

namespace App\Http\Controllers;

use App\Exports\BitacoraExport;
use App\Models\Bitacora;
use App\Models\User;
use App\Tenancy\TenantManager;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Excel as ExcelType;
use Maatwebsite\Excel\Facades\Excel;

class BitacoraController extends Controller
{
    public function index(Request $request): View
    {
        $registros = $this->filtrar($request)
            ->with('user')
            ->latest('created_at')
            ->paginate(25)
            ->withQueryString();

        return view('bitacora.index', [
            'registros' => $registros,
            'usuarios' => User::where('school_id', app(TenantManager::class)->schoolId())->orderBy('name')->get(),
            'modulos' => Bitacora::query()->whereNotNull('modulo')->distinct()->orderBy('modulo')->pluck('modulo'),
            'filtros' => $request->only(['desde', 'hasta', 'user_id', 'modulo', 'accion']),
        ]);
    }

    public function exportar(Request $request)
    {
        $formato = $request->get('formato', 'xlsx');
        $query = $this->filtrar($request)->latest('created_at');

        if ($formato === 'pdf') {
            $registros = $query->with('user')->limit(2000)->get();

            return Pdf::loadView('bitacora.pdf', compact('registros'))
                ->download('bitacora.pdf');
        }

        $writer = $formato === 'csv' ? ExcelType::CSV : ExcelType::XLSX;
        $extension = $formato === 'csv' ? 'csv' : 'xlsx';

        return Excel::download(new BitacoraExport($query), "bitacora.{$extension}", $writer);
    }

    private function filtrar(Request $request): Builder
    {
        $tenant = app(TenantManager::class);

        return Bitacora::query()
            ->when($tenant->hasTenant(), fn ($q) => $q->where('school_id', $tenant->schoolId()))
            ->when($request->filled('desde'), fn ($q) => $q->whereDate('created_at', '>=', $request->date('desde')))
            ->when($request->filled('hasta'), fn ($q) => $q->whereDate('created_at', '<=', $request->date('hasta')))
            ->when($request->filled('user_id'), fn ($q) => $q->where('user_id', $request->integer('user_id')))
            ->when($request->filled('modulo'), fn ($q) => $q->where('modulo', $request->get('modulo')))
            ->when($request->filled('accion'), fn ($q) => $q->where('accion', $request->get('accion')));
    }
}
