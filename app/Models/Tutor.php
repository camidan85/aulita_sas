<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use App\Models\Concerns\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Notifications\Notifiable;

class Tutor extends Model
{
    use Auditable, BelongsToSchool, HasFactory, Notifiable;

    protected $table = 'tutores';

    protected $fillable = [
        'school_id', 'user_id', 'nombre', 'correo', 'telefono', 'parentesco',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function alumnos(): BelongsToMany
    {
        return $this->belongsToMany(Alumno::class, 'alumno_tutor')
            ->withPivot('tipo')
            ->withTimestamps();
    }

    /**
     * Correo del tutor para el canal mail.
     */
    public function routeNotificationForMail(): ?string
    {
        return $this->correo;
    }

    /**
     * Número en formato E.164 para WhatsApp (canal de notificaciones).
     */
    public function routeNotificationForWhatsapp(): ?string
    {
        return $this->telefono;
    }
}
