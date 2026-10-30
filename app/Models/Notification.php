<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'rendez_vous_id',
        'titre',
        'message',
        'type',
        'lue',
        'date_envoi',
        'envoye',
    ];

    protected function casts(): array
    {
        return [
            'lue' => 'boolean',
            'envoye' => 'boolean',
            'date_envoi' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function rendezVous(): BelongsTo
    {
        return $this->belongsTo(RendezVous::class);
    }
}
