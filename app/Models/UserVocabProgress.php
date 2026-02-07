<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserVocabProgress extends Model
{
    protected $table = 'user_vocab_progress';

    protected $fillable = [
        'user_id',
        'vocabulary_id',
        'step',
        'next_review_at',
    ];

    protected $casts = [
        'next_review_at' => 'datetime',
    ];

    public function vocabulary()
    {
        return $this->belongsTo(Vocabulary::class);
    }
}
