<?php

namespace App\Models\Concerns;

use App\Observers\AuditObserver;

/**
 * Marca un modelo como auditable: cada created/updated/deleted se registra
 * en bitácora a través del AuditObserver.
 *
 * Uso: añadir `use Auditable;` al modelo de dominio.
 */
trait Auditable
{
    public static function bootAuditable(): void
    {
        static::observe(AuditObserver::class);
    }
}
