<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AvisoAdjunto extends Model
{
    use BelongsToSchool, HasFactory;

    protected $table = 'aviso_adjuntos';

    protected $fillable = [
        'school_id', 'aviso_id', 'path', 'nombre_original', 'mime', 'size',
    ];

    public function aviso(): BelongsTo
    {
        return $this->belongsTo(Aviso::class);
    }
}
