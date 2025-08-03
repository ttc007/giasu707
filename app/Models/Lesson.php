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

    public function nextLesson()
    {
        return Lesson::where('chapter_id', $this->chapter_id)
                     ->where('id', '>', $this->id)
                     ->orderBy('id', 'asc')
                     ->first(); // không dùng findOrFail để tránh lỗi
    }

    public function prevLesson()
    {
        return Lesson::where('chapter_id', $this->chapter_id)
                     ->where('id', '<', $this->id)
                     ->orderBy('id', 'desc')
                     ->first();
    }
}
