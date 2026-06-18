<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QrToken extends Model
{
    use BelongsToSchool, HasFactory;

    protected $table = 'qr_tokens';

    protected $fillable = ['school_id', 'alumno_id', 'token', 'activo'];

    protected $casts = ['activo' => 'boolean'];

    public function alumno(): BelongsTo
    {
        return $this->belongsTo(Alumno::class);
    }
}
