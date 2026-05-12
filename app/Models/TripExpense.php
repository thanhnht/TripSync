<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TripExpense extends Model
{
    protected $fillable = [
        'trip_id', 'trip_activity_id', 'title', 'amount',
        'paid_by', 'split_method', 'note', 'created_by',
    ];

    protected $casts = [
        'amount' => 'integer',
    ];

    public function trip(): BelongsTo
    {
        return $this->belongsTo(Trip::class);
    }

    public function activity(): BelongsTo
    {
        return $this->belongsTo(TripActivity::class, 'trip_activity_id');
    }

    public function payer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'paid_by');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function splits(): HasMany
    {
        return $this->hasMany(ExpenseSplit::class, 'expense_id');
    }

    public function getFormattedAmountAttribute(): string
    {
        return number_format($this->amount, 0, ',', '.') . ' ₫';
    }

    public function splitMethodLabel(): string
    {
        return match($this->split_method) {
            'equal'  => 'Chia đều',
            'custom' => 'Tuỳ chỉnh',
            default  => $this->split_method,
        };
    }
}
