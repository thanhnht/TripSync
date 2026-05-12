<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExpenseSplit extends Model
{
    protected $fillable = ['expense_id', 'user_id', 'amount'];

    protected $casts = [
        'amount' => 'integer',
    ];

    public function expense(): BelongsTo
    {
        return $this->belongsTo(TripExpense::class, 'expense_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
