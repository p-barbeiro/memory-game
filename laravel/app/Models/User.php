<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'type',
        'nickname',
        'blocked',
        'photo_filename',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function transactions() : HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function wins() : HasMany
    {
        return $this->hasMany(Game::class, 'winner_user_id');
    }

    public function created_games() : HasMany
    {
        return $this->hasMany(Game::class, 'created_user_id');
    }

    public function multiplayer_games() : HasMany
    {
        return $this->hasMany(Multiplayer::class);
    }

    public function is_blocked(): bool
    {
        return $this->blocked === 1;
    }
}
