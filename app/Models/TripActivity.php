<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class TripActivity extends Model
{
    protected $fillable = [
        'trip_day_id', 'created_by', 'title', 'description',
        'type', 'start_time', 'end_time', 'location',
        'estimated_cost', 'reference_url', 'status', 'sort_order',
    ];

    // ── Relationships ──────────────────────────────────────────

    public function day()
    {
        return $this->belongsTo(TripDay::class, 'trip_day_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function votes()
    {
        return $this->hasMany(ActivityVote::class, 'activity_id');
    }

    public function comments()
    {
        return $this->hasMany(ActivityComment::class, 'activity_id')
            ->with('user')
            ->latest();
    }

    // ── Computed attributes ────────────────────────────────────

    public function getUpVotesCountAttribute(): int
    {
        return $this->votes->where('vote', 'up')->count();
    }

    public function getDownVotesCountAttribute(): int
    {
        return $this->votes->where('vote', 'down')->count();
    }

    public function getUserVoteAttribute(): ?string
    {
        if (!Auth::check()) return null;
        return $this->votes->where('user_id', Auth::id())->first()?->vote;
    }

    public function getTypeIconAttribute(): string
    {
        return match ($this->type) {
            'transport'     => '🚗',
            'accommodation' => '🏨',
            'food'          => '🍜',
            'sightseeing'   => '🏛️',
            'activity'      => '🎯',
            default         => '📌',
        };
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'transport'     => 'Di chuyển',
            'accommodation' => 'Lưu trú',
            'food'          => 'Ăn uống',
            'sightseeing'   => 'Tham quan',
            'activity'      => 'Hoạt động',
            default         => 'Khác',
        };
    }

    public function getStatusBadgeAttribute(): array
    {
        return match ($this->status) {
            'approved' => ['label' => 'Đã duyệt',  'color' => 'green'],
            'rejected' => ['label' => 'Từ chối',   'color' => 'red'],
            default    => ['label' => 'Đề xuất',   'color' => 'yellow'],
        };
    }

    public function getFormattedCostAttribute(): string
    {
        if ($this->estimated_cost <= 0) return 'Miễn phí';
        return number_format($this->estimated_cost, 0, ',', '.') . ' ₫';
    }

    public function getTimeRangeAttribute(): string
    {
        if (!$this->start_time) return '';
        $range = substr($this->start_time, 0, 5);
        if ($this->end_time) $range .= ' – ' . substr($this->end_time, 0, 5);
        return $range;
    }
}
