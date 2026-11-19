<?php

namespace App\Models;

use Database\Factories\ReplyFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reply extends Model
{
    /** @use HasFactory<ReplyFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     * The user_id always comes from the logged-in user, never from the form.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'message',
    ];

    /**
     * @return BelongsTo<Ticket, $this>
     */
    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    /**
     * The person who wrote the reply (the ticket owner or an admin).
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
