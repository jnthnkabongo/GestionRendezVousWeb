<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserDevice extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'fcm_token',
        'plateforme',
        'actif',
        'derniere_connexion',
    ];

    protected function casts(): array
    {
        return [
            'actif' => 'boolean',
            'derniere_connexion' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
