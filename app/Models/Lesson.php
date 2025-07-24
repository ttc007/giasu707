<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    protected $fillable = [
        'chapter_id',
        'title',
        'slug',
        'summary',
        'order',
    ];

    public function chapter()
    {
        return $this->belongsTo(Chapter::class);
    }

    public function sections()
    {
        return $this->hasMany(Section::class);
    }
}
