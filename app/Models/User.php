<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use App\Models\Votante; // Importa el modelo Votante

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

    // Relaciones existentes
    public function concejal()
    {
        return $this->hasOne(Concejal::class);
    }

    public function alcalde()
    {
        return $this->hasOne(User::class, 'id', 'alcalde_id');
    }

    public function creadoPorConcejal()
    {
        return $this->belongsTo(User::class, 'concejal_id');
    }

    public function creadoPorAlcalde()
    {
        return $this->belongsTo(User::class, 'alcalde_id');
    }

    public function lideresCreados()
    {
        return $this->hasMany(User::class, 'concejal_id');
    }

    public function lideresAlcaldia()
    {
        return $this->hasMany(User::class, 'alcalde_id');
    }

    // RELACIÓN NUEVA: votantes registrados por este líder
    public function votantesRegistrados()
    {
        return $this->hasMany(Votante::class, 'lider_id'); // Asegúrate que 'lider_id' sea la FK correcta en votantes
    }
}
