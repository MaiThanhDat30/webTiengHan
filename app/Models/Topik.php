<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Topik extends Model
{
    protected $fillable = ['name', 'level'];

    public function topics()
    {
        return $this->hasMany(Topic::class);
    }
}
