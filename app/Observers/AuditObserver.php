<?php

namespace App\Observers;

use App\Models\Bitacora;
use App\Tenancy\TenantManager;
use Illuminate\Database\Eloquent\Model;

/**
 * Observa cambios de modelos auditables y los registra en bitácora (RN-AU01).
 */
class AuditObserver
{
    public function created(Model $model): void
    {
        $this->log('crear', $model);
    }

    public function updated(Model $model): void
    {
        $this->log('actualizar', $model);
    }

    public function deleted(Model $model): void
    {
        $this->log('eliminar', $model);
    }

    protected function log(string $accion, Model $model): void
    {
        $tenant = app(TenantManager::class);
        $request = request();

        Bitacora::create([
            'school_id' => $tenant->schoolId() ?? ($model->school_id ?? null),
            'user_id' => auth()->id(),
            'accion' => $accion,
            'modulo' => $model->getTable(),
            'model_type' => $model::class,
            'model_id' => $model->getKey(),
            'descripcion' => $accion.' '.class_basename($model).' #'.$model->getKey(),
            'ip' => $request?->ip(),
            'user_agent' => substr((string) $request?->userAgent(), 0, 255),
        ]);
    }
}
