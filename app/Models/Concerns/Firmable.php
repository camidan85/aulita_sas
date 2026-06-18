<?php

namespace App\Models\Concerns;

use App\Models\FirmaEnterado;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * Permite que un modelo (Reporte, Aviso, Citatorio) reciba firmas de enterado.
 */
trait Firmable
{
    public function firmas(): MorphMany
    {
        return $this->morphMany(FirmaEnterado::class, 'firmable');
    }

    public function firmadoPor(int $userId): bool
    {
        return $this->firmas()->where('user_id', $userId)->exists();
    }
}
