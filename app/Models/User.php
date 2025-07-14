<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
        'alcalde_id',
        'concejal_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // ===============================
    // RELACIÓN CON ALCALDE
    // ===============================

    public function alcaldeInfo(): HasOne
    {
        return $this->hasOne(Alcalde::class);
    }

    // ===============================
    // RELACIÓN CON CONCEJAL
    // ===============================

    public function concejal(): HasOne
    {
        return $this->hasOne(Concejal::class);
    }

    // ===============================
    // RELACIONES DE JERARQUÍA
    // ===============================

    public function alcalde(): BelongsTo
    {
        return $this->belongsTo(User::class, 'alcalde_id');
    }

    public function concejales(): HasMany
    {
        return $this->hasMany(User::class, 'alcalde_id');
    }

    public function concejalesInfo(): HasMany
    {
        return $this->hasMany(Concejal::class, 'alcalde_id');
    }

    public function concejalPadre(): BelongsTo
    {
        return $this->belongsTo(User::class, 'concejal_id');
    }

    public function lideres(): HasMany
    {
        return $this->hasMany(User::class, 'concejal_id');
    }

    // ===============================
    // RELACIONES CON VOTANTES
    // ===============================

    public function votantesRegistrados(): HasMany
    {
        return $this->hasMany(Votante::class, 'registrado_por');
    }

    public function votantesAlcalde(): HasMany
    {
        return $this->hasMany(Votante::class, 'alcalde_id');
    }

    public function votantesConcejal(): HasMany
    {
        return $this->hasMany(Votante::class, 'concejal_id');
    }

    // ===============================
    // MÉTODOS DE UTILIDAD
    // ===============================

    public function esAdministrador(): bool
    {
        return $this->hasRole('administrador');
    }

    public function esAlcalde(): bool
    {
        return $this->hasRole('alcalde');
    }

    public function esConcejal(): bool
    {
        return $this->hasRole('aspirante-concejo') || $this->hasRole('concejal');
    }

    public function esLider(): bool
    {
        return $this->hasRole('lider');
    }

    public function obtenerAlcalde(): ?User
    {
        if ($this->esAlcalde()) {
            return $this;
        }

        if ($this->esConcejal()) {
            return $this->alcalde;
        }

        if ($this->esLider()) {
            return $this->concejalPadre?->alcalde;
        }

        return null;
    }

    public function concejalesDisponibles()
    {
        if ($this->esAlcalde()) {
            return $this->concejales()->whereHas('roles', function ($q) {
                $q->whereIn('name', ['aspirante-concejo', 'concejal']);
            })->get();
        }

        if ($this->esLider()) {
            $alcalde = $this->concejalPadre?->alcalde;
            return $alcalde
                ? $alcalde->concejales()->whereHas('roles', function ($q) {
                    $q->whereIn('name', ['aspirante-concejo', 'concejal']);
                })->get()
                : collect();
        }

        return collect();
    }

    // ===============================
    // SCOPES
    // ===============================

    public function scopeConRol($query, $rol)
    {
        return $query->whereHas('roles', function ($q) use ($rol) {
            $q->where('name', $rol);
        });
    }

    public function scopeAlcaldes($query)
    {
        return $query->conRol('alcalde');
    }

    public function scopeConcejales($query)
    {
        return $query->conRol('aspirante-concejo');
    }

    public function scopeLideres($query)
    {
        return $query->conRol('lider');
    }

    // ===============================
    // EVENTOS DEL MODELO
    // ===============================

    protected static function boot()
    {
        parent::boot();

        // Al eliminar un usuario, borrar datos relacionados
        static::deleting(function ($user) {
            if ($user->concejal) {
                $user->concejal->delete();
            }
            if ($user->alcaldeInfo) {
                $user->alcaldeInfo->delete();
            }
        });

        // Al crear un usuario, si es alcalde, crear registro en alcaldes
        static::created(function ($user) {
            if ($user->hasRole('alcalde') && !$user->alcaldeInfo) {
                $user->alcaldeInfo()->create([
                    'activo' => true,
                ]);
            }
        });
    }
}
