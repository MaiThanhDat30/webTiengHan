<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserStreak extends Model
{
    protected $fillable = [
        'user_id',
        'current_streak',
        'longest_streak',
        'last_study_date',
    ];

    protected $casts = [
        'last_study_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
