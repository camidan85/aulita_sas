<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class FirmaEnterado extends Model
{
    use BelongsToSchool, HasFactory;

    protected $table = 'firmas_enterado';

    protected $fillable = [
        'school_id', 'firmable_type', 'firmable_id', 'user_id', 'fecha', 'hora', 'ip',
    ];

    protected $casts = [
        'fecha' => 'date',
    ];

    public function firmable(): MorphTo
    {
        return $this->morphTo();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
