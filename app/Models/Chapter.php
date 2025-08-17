<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Chapter extends Model
{
    protected $fillable = [
        'subject_id',
        'title',
        'order',
        'slug',
        'summary',
        'image'
    ];

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function lessons()
    {
        return $this->hasMany(Lesson::class);
    }

    public function getQuestionsCountAttribute()
    {
        // Đảm bảo đã eager load 'lessons.sections.questions' để tránh N+1
        return $this->lessons->sum(function ($lesson) {
            return $lesson->sections->sum(function ($section) {
                return $section->questions_count ?? $section->questions->count();
            });
        });
    }

}
