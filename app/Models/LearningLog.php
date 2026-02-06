<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LearningLog extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'vocabulary_id',
        'action',
        'result',
        'interval',
        'reviewed_at',
    ];

    protected $dates = ['reviewed_at'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function vocabulary()
    {
        return $this->belongsTo(Vocabulary::class);
    }
    
}
