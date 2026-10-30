<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RendezVous extends Model
{
    use HasFactory;

    protected $table = 'rendez_vous';

    protected $fillable = [
        'client_id',
        'cree_par',
        'chef_id',
        'date_rendez_vous',
        'heure_debut',
        'heure_fin',
        'objet',
        'type_rendez_vous',
        'description',
        'statut',
        'rappel_active',
        'rappel_minutes',
    ];

    protected function casts(): array
    {
        return [
            'date_rendez_vous' => 'date',
            'rappel_active' => 'boolean',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function createur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cree_par');
    }

    public function chef(): BelongsTo
    {
        return $this->belongsTo(User::class, 'chef_id');
    }

    public function rappels(): HasMany
    {
        return $this->hasMany(Rappel::class);
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }
}
