<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Game extends Model
{
    public function board() : BelongsTo
    {
        return $this->belongsTo(Board::class);
    }

    public function transactions() : HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function winner() : BelongsTo
    {
        return $this->belongsTo(User::class, 'winner_user_id');
    }

    public function creator() : BelongsTo
    {
        return $this->belongsTo(User::class, 'created_user_id');
    }

    public function multiplayerGamesPlayed() : HasMany
    {
        return $this->hasMany(Multiplayer::class);
    }

    protected $fillable = [
        'type',
        'status',
        'total_time',
        'image',
        'created_user_id',
        'winner_user_id',
        'began_at',
        'ended_at',
        'board_id',
        'total_turns_winner',
    ];
}
