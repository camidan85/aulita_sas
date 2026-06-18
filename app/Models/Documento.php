<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use App\Models\Concerns\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Documento extends Model
{
    use Auditable, BelongsToSchool, HasFactory;

    protected $table = 'documentos';

    protected $fillable = [
        'school_id', 'alumno_id', 'tipo', 'path',
        'nombre_original', 'mime', 'size', 'subido_por',
    ];

    public function alumno(): BelongsTo
    {
        return $this->belongsTo(Alumno::class);
    }
}
