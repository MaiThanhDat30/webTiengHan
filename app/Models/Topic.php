<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Topic extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'parent_id'];

    // Topic cha
    public function parent()
    {
        return $this->belongsTo(Topic::class, 'parent_id');
    }

    // Topic con
    public function children()
    {
        return $this->hasMany(Topic::class, 'parent_id');
    }

    // Từ vựng
    public function vocabularies()
    {
        return $this->hasMany(Vocabulary::class);
    }
}
