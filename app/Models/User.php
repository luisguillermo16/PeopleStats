<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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

    // ✅ Relación: Un usuario puede ser un líder
    public function lider()
    {
        return $this->hasOne(Lider::class);
    }

    // ✅ Relación: Un usuario puede ser un concejal
    public function concejal()
    {
        return $this->hasOne(Concejal::class);
    }

    // ✅ Relación opcional: Un usuario puede ser un alcalde (si tienes modelo Alcalde)
    // Si no usas modelo Alcalde, puedes ignorar esta relación
    public function alcalde()
    {
        return $this->hasOne(User::class, 'id', 'alcalde_id');
    }
}
