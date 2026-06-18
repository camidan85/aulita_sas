<?php

namespace App\Scopes;

use App\Tenancy\TenantManager;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

/**
 * Global Scope que filtra toda consulta por el school_id activo (RN-T01).
 * Si no hay tenant resuelto (consola sin contexto) o está en bypass (Super Admin),
 * no aplica el filtro.
 */
class TenantScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        $tenant = app(TenantManager::class);

        if ($tenant->isBypassed() || ! $tenant->hasTenant()) {
            return;
        }

        $builder->where($model->getTable().'.school_id', $tenant->schoolId());
    }
}
