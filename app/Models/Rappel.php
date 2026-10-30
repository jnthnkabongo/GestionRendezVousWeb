<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Rappel extends Model
{
    use HasFactory;

    protected $fillable = [
        'rendez_vous_id',
        'user_id',
        'date_prevue',
        'statut',
        'date_envoi',
    ];

    protected function casts(): array
    {
        return [
            'date_prevue' => 'datetime',
            'date_envoi' => 'datetime',
        ];
    }

    public function rendezVous(): BelongsTo
    {
        return $this->belongsTo(RendezVous::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
