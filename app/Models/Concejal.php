<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Concejal extends Model
{
    use HasFactory;

    protected $table = 'concejales';

    protected $fillable = [
        'user_id',
        'alcalde_id',
        'partido_politico',
        'numero_lista',
        'activo'
    ];

    protected $casts = [
        'activo' => 'boolean'
    ];

    /**
     * Relación con el modelo User (concejal)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relación con el alcalde (usuario)
     */
    public function alcalde(): BelongsTo
    {
        return $this->belongsTo(User::class, 'alcalde_id');
    }

    /**
     * Relación con el modelo Alcalde
     */
    public function alcaldeInfo(): BelongsTo
    {
        return $this->belongsTo(Alcalde::class, 'alcalde_id', 'user_id');
    }

    /**
     * Votantes asociados a este concejal
     */
    public function votantes(): HasMany
    {
        return $this->hasMany(Votante::class, 'concejal_id', 'user_id');
    }

    /**
     * Líderes asociados a este concejal
     */
    public function lideres(): HasMany
    {
        return $this->hasMany(User::class, 'concejal_id', 'user_id');
    }

    /**
     * Scope para concejales activos
     */
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    /**
     * Scope para concejales de un alcalde específico
     */
    public function scopeDeAlcalde($query, $alcaldeId)
    {
        return $query->where('alcalde_id', $alcaldeId);
    }

    /**
     * Scope para concejales con sus relaciones
     */
    public function scopeConRelaciones($query)
    {
        return $query->with(['user', 'alcalde', 'alcaldeInfo']);
    }
}