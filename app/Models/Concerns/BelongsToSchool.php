<?php

namespace App\Models\Concerns;

use App\Models\School;
use App\Scopes\TenantScope;
use App\Tenancy\TenantManager;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Aplica multitenancy a un modelo de dominio:
 *  - Añade el TenantScope (filtra por school_id en toda consulta).
 *  - Asigna automáticamente el school_id activo al crear (RN-T02).
 */
trait BelongsToSchool
{
    protected static function bootBelongsToSchool(): void
    {
        static::addGlobalScope(new TenantScope);

        static::creating(function ($model) {
            if (empty($model->school_id)) {
                $tenant = app(TenantManager::class);

                if ($tenant->hasTenant()) {
                    $model->school_id = $tenant->schoolId();
                }
            }
        });
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }
}
