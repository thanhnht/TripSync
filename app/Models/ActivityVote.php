<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityVote extends Model
{
    protected $fillable = ['activity_id', 'user_id', 'vote'];

    public function activity()
    {
        return $this->belongsTo(TripActivity::class, 'activity_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
