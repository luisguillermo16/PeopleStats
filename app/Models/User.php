<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
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
        'concejal_id',
        'alcalde_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // Relación con la tabla líderes
    public function lider()
    {
        return $this->hasOne(Lider::class);
    }

    // ✅ Relación: Un usuario puede ser un concejal
    public function concejal()
    {
        return $this->hasOne(Concejal::class);
    }

    // ✅ Relación: Un usuario puede ser un alcalde (si tienes modelo Alcalde)
    public function alcalde()
    {
        return $this->hasOne(User::class, 'id', 'alcalde_id');
    }

    // ✅ Relación: Obtener el concejal que creó este líder
    public function creadoPorConcejal()
    {
        return $this->belongsTo(User::class, 'concejal_id');
    }

    // ✅ Relación: Obtener el alcalde que creó este líder
    public function creadoPorAlcalde()
    {
        return $this->belongsTo(User::class, 'alcalde_id');
    }

    // ✅ Relación inversa: líderes creados por este concejal
    public function lideresCreados()
    {
        return $this->hasMany(User::class, 'concejal_id');
    }

    // ✅ Relación inversa: líderes bajo este alcalde
    public function lideresAlcaldia()
    {
        return $this->hasMany(User::class, 'alcalde_id');
    }
}