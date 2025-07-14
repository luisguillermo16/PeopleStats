<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Alcalde extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'partido_politico',
        'numero_lista',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function concejales(): HasMany
    {
        return $this->hasMany(Concejal::class, 'alcalde_id', 'user_id');
    }
}

