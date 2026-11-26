<?php

namespace App\Models;

use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use Database\Factories\TicketFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Ticket extends Model
{
    /** @use HasFactory<TicketFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     * The user_id is not in this list: it is always set through $user->tickets()->create().
     *
     * @var list<string>
     */
    protected $fillable = [
        'category_id',
        'title',
        'description',
        'location',
        'priority',
        'status',
        'screenshot',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'priority' => TicketPriority::class,
            'status' => TicketStatus::class,
        ];
    }

    /**
     * The student or teacher who reported the problem.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo<Category, $this>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * The conversation about this ticket, in the order it was written.
     *
     * @return HasMany<Reply, $this>
     */
    public function replies(): HasMany
    {
        return $this->hasMany(Reply::class)->oldest();
    }

    /**
     * The URL of the uploaded screenshot, or null when there is no (existing) image.
     */
    public function screenshotUrl(): ?string
    {
        if ($this->screenshot === null || ! Storage::disk('public')->exists($this->screenshot)) {
            return null;
        }

        return asset('storage/'.$this->screenshot);
    }

    /**
     * Remove the uploaded screenshot file from the public disk.
     */
    public function deleteScreenshot(): void
    {
        if ($this->screenshot !== null) {
            Storage::disk('public')->delete($this->screenshot);
        }
    }
}
