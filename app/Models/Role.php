<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $table = 'roles';

    protected $fillable = [
        'roles_name',
    ];

    // Relasi ke User
    public function users()
    {
        return $this->hasMany(User::class);
    }
}
