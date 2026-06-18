<?php

namespace App\Services;

use App\Models\FirmaEnterado;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class FirmaService extends BaseService
{
    /**
     * Registra la firma de enterado (idempotente por firmable + usuario).
     * Guarda fecha, hora e IP (RN-F02).
     */
    public function firmar(Model $firmable, User $user, string $ip): FirmaEnterado
    {
        return FirmaEnterado::firstOrCreate(
            [
                'firmable_type' => $firmable->getMorphClass(),
                'firmable_id' => $firmable->getKey(),
                'user_id' => $user->id,
            ],
            [
                'school_id' => $firmable->school_id,
                'fecha' => now()->toDateString(),
                'hora' => now()->toTimeString(),
                'ip' => $ip,
            ],
        );
    }
}
