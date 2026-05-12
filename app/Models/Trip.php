<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Trip extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'destination',
        'cover_image',
        'start_date',
        'end_date',
        'status',
        'invite_code',
        'owner_id',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
    ];

    // ── Relationships ──────────────────────────────────────────

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function members()
    {
        return $this->belongsToMany(User::class, 'trip_members')
            ->withPivot('role', 'joined_at')
            ->withTimestamps();
    }

    public function tripMembers()
    {
        return $this->hasMany(TripMember::class);
    }

    public function days()
    {
        return $this->hasMany(TripDay::class)->orderBy('day_number');
    }

    public function expenses()
    {
        return $this->hasMany(TripExpense::class)->latest();
    }

    public function photos()
    {
        return $this->hasMany(TripPhoto::class)->latest();
    }

    public function checklistItems()
    {
        return $this->hasMany(ChecklistItem::class)->orderBy('sort_order');
    }

    // ── Helpers ────────────────────────────────────────────────

    public function generateInviteCode(): string
    {
        $code = strtoupper(Str::random(8));
        $this->update(['invite_code' => $code]);
        return $code;
    }

    public function isOwner(User $user): bool
    {
        return $this->owner_id === $user->id;
    }

    public function isMember(User $user): bool
    {
        return $this->members()->where('user_id', $user->id)->exists();
    }

    public function getDaysCountAttribute(): int
    {
        return $this->start_date->diffInDays($this->end_date) + 1;
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'planning'  => 'Đang lên kế hoạch',
            'ongoing'   => 'Đang diễn ra',
            'completed' => 'Đã hoàn thành',
            'cancelled' => 'Đã huỷ',
            default     => $this->status,
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'planning'  => 'blue',
            'ongoing'   => 'green',
            'completed' => 'gray',
            'cancelled' => 'red',
            default     => 'blue',
        };
    }

    public function getCoverImageUrlAttribute(): string
    {
        if ($this->cover_image) {
            return asset('storage/' . $this->cover_image);
        }
        return asset('images/default-trip.jpg');
    }
}
