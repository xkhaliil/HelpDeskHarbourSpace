<?php

namespace App\Models;

use Database\Factories\CommentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    /** @use HasFactory<CommentFactory> */
    use HasFactory;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'approved_at' => 'datetime',
        ];
    }

    public function article()
    {
        return $this->belongsTo(Article::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function canManage($user): bool
    {
        if ($user->is_admin) {
            return true;
        }

        if ($this->article->author_id == $user->id) {
            return true;
        }

        return false;
    }

    public function canView($user): bool
    {
        if ($this->approved_at !== null) {
            return true;
        }

        if (! $user) {
            return false;
        }

        if ($this->user_id == $user->id) {
            return true;
        }

        return $this->canManage($user);
    }
}
