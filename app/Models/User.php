<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\Role;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'approved_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => Role::class,
            'approved_at' => 'datetime',
        ];
    }

    /**
     * Only approved accounts can log in. See LoginRequest::authenticate().
     */
    public function isApproved(): bool
    {
        return $this->approved_at !== null;
    }

    /**
     * The tickets this user has reported.
     *
     * @return HasMany<Ticket, $this>
     */
    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    /**
     * The replies this user has written.
     *
     * @return HasMany<Reply, $this>
     */
    public function replies(): HasMany
    {
        return $this->hasMany(Reply::class);
    }

    /**
     * The tickets this admin handles (the other side of Ticket::assignees()).
     * Many-to-many: the links are saved in the "ticket_user" pivot table.
     *
     * @return BelongsToMany<Ticket, $this>
     */
    public function assignedTickets(): BelongsToMany
    {
        return $this->belongsToMany(Ticket::class);
    }

    /**
     * Is this user a member of the IT and campus staff?
     */
    public function isAdmin(): bool
    {
        return $this->role === Role::Admin;
    }

    /**
     * Is this user a teacher? Teachers can also choose the priority of their tickets.
     */
    public function isTeacher(): bool
    {
        return $this->role === Role::Teacher;
    }

    /**
     * Is this user a student?
     */
    public function isStudent(): bool
    {
        return $this->role === Role::Student;
    }
}
