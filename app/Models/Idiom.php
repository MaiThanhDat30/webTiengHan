<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Idiom extends Model
{
    protected $fillable = [
        'sentence_kr',
        'sentence_vi',
        'level',
        'tag',
    ];
}

