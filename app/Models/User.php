<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Role;
use App\Models\Transaction;

class User extends Authenticatable implements FilamentUser
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'users';

    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'fcm_token', // ← tambah ini
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'password' => 'hashed',
    ];

    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }

    // Relasi ke Role
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    protected static function booted()
    {
        static::creating(function ($user) {
            if (!$user->role_id) {
                $user->role_id = 1;
            }
        });
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'cashier_id');
    }
}