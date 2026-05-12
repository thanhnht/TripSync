<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TripDay extends Model
{
    protected $fillable = ['trip_id', 'date', 'day_number', 'title', 'note'];

    protected $casts = ['date' => 'date'];

    public function trip()
    {
        return $this->belongsTo(Trip::class);
    }

    public function activities()
    {
        return $this->hasMany(TripActivity::class)
            ->orderBy('sort_order')
            ->orderBy('start_time');
    }

    public function getDisplayTitleAttribute(): string
    {
        $dateStr = $this->date->format('d/m/Y');
        return $this->title
            ? "Ngày {$this->day_number} – {$this->title}"
            : "Ngày {$this->day_number} – {$dateStr}";
    }

    public function getTotalCostAttribute(): int
    {
        return (int) $this->activities->sum('estimated_cost');
    }
}
