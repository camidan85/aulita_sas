<?php

use App\Http\Controllers\ActivacionController;
use App\Http\Controllers\Admin\EscuelaController;
use App\Http\Controllers\AlertaRiesgoController;
use App\Http\Controllers\AlumnoController;
use App\Http\Controllers\AsistenciaController;
use App\Http\Controllers\AvisoController;
use App\Http\Controllers\BitacoraController;
use App\Http\Controllers\CalificacionController;
use App\Http\Controllers\CitaController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DocenteController;
use App\Http\Controllers\FirmaController;
use App\Http\Controllers\GradoController;
use App\Http\Controllers\GrupoController;
use App\Http\Controllers\MateriaController;
use App\Http\Controllers\PortalController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\TutorController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Activación del portal de padres (público)
Route::get('activar', [ActivacionController::class, 'iniciarForm'])->name('activar.iniciar');
Route::post('activar', [ActivacionController::class, 'iniciar'])
    ->middleware('throttle:activacion')->name('activar.enviar');
Route::get('activar/{token}', [ActivacionController::class, 'crearForm'])->name('activar.crear');
Route::post('activar/{token}', [ActivacionController::class, 'crear'])->name('activar.guardar');

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    // Carga masiva (antes del resource para no chocar con alumnos/{alumno}).
    Route::get('alumnos/plantilla', [AlumnoController::class, 'plantilla'])
        ->name('alumnos.plantilla')->middleware('permission:alumnos.crear');
    Route::post('alumnos/importar', [AlumnoController::class, 'importar'])
        ->name('alumnos.importar')->middleware('permission:alumnos.crear');

    Route::resource('alumnos', AlumnoController::class)
        ->middleware('permission:alumnos.ver');

    Route::get('alumnos/{alumno}/qr', [AsistenciaController::class, 'qr'])
        ->name('alumnos.qr')
        ->middleware(['permission:alumnos.ver', 'modulo:asistencia']);

    // Asistencia por QR (operación diaria desde el celular).
    Route::middleware(['permission:asistencias.registrar', 'modulo:asistencia'])->group(function () {
        Route::get('asistencia/escanear', [AsistenciaController::class, 'escanear'])->name('asistencias.escanear');
        Route::post('asistencia/registrar', [AsistenciaController::class, 'registrar'])
            ->middleware('throttle:escaneo')->name('asistencias.registrar');
    });

    Route::get('asistencia', [AsistenciaController::class, 'index'])
        ->name('asistencias.index')
        ->middleware(['permission:asistencias.ver', 'modulo:asistencia']);

    // Alertas de riesgo
    Route::middleware(['permission:asistencias.ver', 'modulo:alertas'])->group(function () {
        Route::get('alertas', [AlertaRiesgoController::class, 'index'])->name('alertas.index');
        Route::patch('alertas/{alerta}/atender', [AlertaRiesgoController::class, 'atender'])->name('alertas.atender');
    });

    // Académico (calificaciones, boletas, kardex)
    Route::middleware(['permission:calificaciones.ver', 'modulo:calificaciones'])->group(function () {
        Route::get('calificaciones', [CalificacionController::class, 'index'])->name('calificaciones.index');
        Route::get('calificaciones/exportar', [CalificacionController::class, 'exportar'])->name('calificaciones.exportar');
        Route::get('alumnos/{alumno}/boleta', [CalificacionController::class, 'boleta'])->name('alumnos.boleta');
        Route::get('alumnos/{alumno}/kardex', [CalificacionController::class, 'kardex'])->name('alumnos.kardex');
    });

    Route::middleware(['permission:calificaciones.capturar', 'modulo:calificaciones'])->group(function () {
        Route::get('calificaciones/capturar', [CalificacionController::class, 'capturar'])->name('calificaciones.capturar');
        Route::post('calificaciones', [CalificacionController::class, 'guardar'])->name('calificaciones.guardar');
    });

    // Reportes de conducta (con evidencias)
    Route::middleware(['permission:reportes.ver', 'modulo:reportes'])->group(function () {
        Route::get('reportes', [ReporteController::class, 'index'])->name('reportes.index');
        Route::get('reportes/{reporte}', [ReporteController::class, 'show'])->name('reportes.show');
        Route::get('evidencias/{evidencia}/descargar', [ReporteController::class, 'descargarEvidencia'])->name('evidencias.descargar');
    });
    Route::middleware(['permission:reportes.crear', 'modulo:reportes'])->group(function () {
        Route::get('reportes-nuevo', [ReporteController::class, 'create'])->name('reportes.create');
        Route::post('reportes', [ReporteController::class, 'store'])->name('reportes.store');
    });

    // Avisos segmentados (con adjuntos)
    Route::middleware(['permission:avisos.ver', 'modulo:avisos'])->group(function () {
        Route::get('avisos', [AvisoController::class, 'index'])->name('avisos.index');
        Route::get('avisos/{aviso}', [AvisoController::class, 'show'])->name('avisos.show');
        Route::get('adjuntos/{adjunto}/descargar', [AvisoController::class, 'descargarAdjunto'])->name('adjuntos.descargar');
    });
    Route::middleware(['permission:avisos.crear', 'modulo:avisos'])->group(function () {
        Route::get('avisos-nuevo', [AvisoController::class, 'create'])->name('avisos.create');
        Route::post('avisos', [AvisoController::class, 'store'])->name('avisos.store');
    });

    // Firma de enterado (fecha/hora/IP)
    Route::patch('reportes/{reporte}/firmar', [FirmaController::class, 'firmarReporte'])
        ->middleware('modulo:reportes')->name('reportes.firmar');
    Route::patch('avisos/{aviso}/firmar', [FirmaController::class, 'firmarAviso'])
        ->middleware('modulo:avisos')->name('avisos.firmar');

    // Portal de padres
    Route::get('portal', [PortalController::class, 'dashboard'])
        ->middleware(['permission:portal.ver', 'modulo:portal'])->name('portal.dashboard');

    Route::middleware(['permission:portal.ver', 'modulo:citas'])->group(function () {
        Route::get('citas/nueva', [CitaController::class, 'create'])->name('citas.create');
        Route::post('citas', [CitaController::class, 'store'])->name('citas.store');
    });
    Route::get('citas', [CitaController::class, 'index'])->middleware('modulo:citas')->name('citas.index');
    Route::patch('citas/{cita}/estatus', [CitaController::class, 'actualizarEstatus'])
        ->middleware(['permission:citas.gestionar', 'modulo:citas'])->name('citas.estatus');

    // Auditoría / bitácora
    Route::middleware(['permission:bitacora.ver', 'modulo:bitacora'])->group(function () {
        Route::get('auditoria', [BitacoraController::class, 'index'])->name('bitacora.index');
        Route::get('auditoria/exportar', [BitacoraController::class, 'exportar'])->name('bitacora.exportar');
    });

    Route::resource('grados', GradoController::class)->except('show')
        ->middleware('permission:grupos.gestionar');

    Route::resource('grupos', GrupoController::class)->except('show')
        ->middleware('permission:grupos.gestionar');

    Route::resource('materias', MateriaController::class)->except('show')
        ->middleware('permission:materias.gestionar');

    Route::resource('docentes', DocenteController::class)->except('show')
        ->middleware('permission:docentes.gestionar');

    Route::resource('tutores', TutorController::class)->except('show')
        ->middleware('permission:tutores.gestionar');
});

// Panel del Super Admin (alta de escuelas + configuración por escuela)
Route::middleware(['auth', 'verified', 'role:super_admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('escuelas', [EscuelaController::class, 'index'])->name('escuelas.index');
    Route::get('escuelas/crear', [EscuelaController::class, 'create'])->name('escuelas.create');
    Route::post('escuelas', [EscuelaController::class, 'store'])->name('escuelas.store');
    Route::get('escuelas/{escuela}/editar', [EscuelaController::class, 'edit'])->name('escuelas.edit');
    Route::put('escuelas/{escuela}', [EscuelaController::class, 'update'])->name('escuelas.update');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
