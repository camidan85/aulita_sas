<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Evidencia extends Model
{
    use BelongsToSchool, HasFactory;

    protected $table = 'evidencias';

    protected $fillable = [
        'school_id', 'reporte_id', 'tipo', 'path', 'nombre_original', 'mime', 'size',
    ];

    public function reporte(): BelongsTo
    {
        return $this->belongsTo(Reporte::class);
    }
}
