<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SrsReview extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'vocabulary_id',
        'topic_id',
        'step',
        'wrong_count',
        'next_review_at',
    ];
    public function vocabulary()
    {
        return $this->belongsTo(Vocabulary::class);
    }
}
