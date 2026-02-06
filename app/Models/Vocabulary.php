<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vocabulary extends Model
{
    use HasFactory;
    protected $fillable = [
        'topic_id',
        'word_kr',
        'word_vi',
        'example',
        'korean',
        'meaning',
        'topik_level',
        'difficulty',
        'lesson'
    ];


    public function topic()
    {
        return $this->belongsTo(Topic::class);

    }
    public function learningLogs()
    {
        return $this->hasMany(LearningLog::class);
    }

    public function userProgress()
    {
        return $this->hasMany(UserVocabProgress::class);
    }

}
