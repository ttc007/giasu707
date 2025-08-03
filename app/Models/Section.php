<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    protected $fillable = [
        'lesson_id',
        'title',
        'content',
        'type',
        'order',
        'slug'
    ];

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }

    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    public function nextSection()
    {
        return Section::where('lesson_id', $this->lesson_id)
                     ->where('id', '>', $this->id)
                     ->orderBy('id', 'asc')
                     ->first(); // không dùng findOrFail để tránh lỗi
    }

    public function prevSection()
    {
        return Section::where('lesson_id', $this->lesson_id)
                     ->where('id', '<', $this->id)
                     ->orderBy('id', 'desc')
                     ->first();
    }
}
