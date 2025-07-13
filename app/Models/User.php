<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

/**
 * @mixin \Spatie\Permission\Traits\HasRoles
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    /**
     * Los atributos que son asignables en masa.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'alcalde_id',
        'concejal_id',
    ];

    /**
     * Los atributos que deben ocultarse para arrays.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Los atributos que deben convertirse a tipos nativos.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // ===============================
    // RELACIONES DE JERARQUÍA
    // ===============================

    /**
     * Alcalde al que pertenece este usuario (si es concejal)
     */
    public function alcalde(): BelongsTo
    {
        return $this->belongsTo(User::class, 'alcalde_id');
    }

    /**
     * Concejales que pertenecen a este alcalde
     */
    public function concejales(): HasMany
    {
        return $this->hasMany(User::class, 'alcalde_id');
    }

    /**
     * Concejal al que pertenece este usuario (si es líder)
     */
    public function concejal(): BelongsTo
    {
        return $this->belongsTo(User::class, 'concejal_id');
    }

    /**
     * Líderes que pertenecen a este concejal
     */
    public function lideres(): HasMany
    {
        return $this->hasMany(User::class, 'concejal_id');
    }

    // ===============================
    // RELACIONES CON VOTANTES
    // ===============================

    /**
     * Votantes registrados por este usuario
     */
    public function votantesRegistrados(): HasMany
    {
        return $this->hasMany(Votante::class, 'registrado_por');
    }

    /**
     * Votantes asociados a este alcalde
     */
    public function votantesAlcalde(): HasMany
    {
        return $this->hasMany(Votante::class, 'alcalde_id');
    }

    /**
     * Votantes asociados a este concejal
     */
    public function votantesConcejal(): HasMany
    {
        return $this->hasMany(Votante::class, 'concejal_id');
    }

    // ===============================
    // MÉTODOS DE UTILIDAD
    // ===============================

    /**
     * Verifica si el usuario es administrador
     */
    public function esAdministrador(): bool
    {
        return $this->hasRole('administrador');
    }

    /**
     * Verifica si el usuario es alcalde
     */
    public function esAlcalde(): bool
    {
        return $this->hasRole('alcalde');
    }

    /**
     * Verifica si el usuario es concejal
     */
    public function esConcejal(): bool
    {
        return $this->hasRole('concejal');
    }

    /**
     * Verifica si el usuario es líder
     */
    public function esLider(): bool
    {
        return $this->hasRole('lider');
    }

    /**
     * Obtiene el alcalde asociado (directo o indirecto)
     */
    public function obtenerAlcalde(): ?User
    {
        if ($this->esAlcalde()) {
            return $this;
        }
        
        if ($this->esConcejal()) {
            return $this->alcalde;
        }
        
        if ($this->esLider()) {
            return $this->concejal?->alcalde;
        }
        
        return null;
    }

    /**
     * Obtiene todos los concejales disponibles para este usuario
     */
    public function concejalesDisponibles()
    {
        if ($this->esAlcalde()) {
            return $this->concejales()->whereHas('roles', function($q) {
                $q->where('name', 'concejal');
            })->get();
        }
        
        if ($this->esLider()) {
            $alcalde = $this->concejal?->alcalde;
            return $alcalde ? $alcalde->concejales()->whereHas('roles', function($q) {
                $q->where('name', 'concejal');
            })->get() : collect();
        }
        
        return collect();
    }

    // ===============================
    // SCOPES
    // ===============================

    /**
     * Scope para obtener usuarios por rol
     */
    public function scopeConRol($query, $rol)
    {
        return $query->whereHas('roles', function($q) use ($rol) {
            $q->where('name', $rol);
        });
    }

    /**
     * Scope para obtener alcaldes
     */
    public function scopeAlcaldes($query)
    {
        return $query->conRol('alcalde');
    }

    /**
     * Scope para obtener concejales
     */
    public function scopeConcejales($query)
    {
        return $query->conRol('concejal');
    }

    /**
     * Scope para obtener líderes
     */
    public function scopeLideres($query)
    {
        return $query->conRol('lider');
    }
}