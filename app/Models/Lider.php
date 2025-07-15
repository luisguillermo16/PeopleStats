<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lider extends Model
{
    protected $table = 'lideres';

    protected $fillable = [
        'user_id',
        'concejal_id',
        'alcalde_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function concejal()
    {
        return $this->belongsTo(Concejal::class);
    }

    public function alcalde()
    {
        return $this->belongsTo(User::class, 'alcalde_id');
    }
}
