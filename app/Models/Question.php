<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $fillable = [
        'section_id',
        'title',
        'content',
        'solution',
        'answer',
        'type',
    ];

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function exams()
    {
        return $this->belongsToMany(Exam::class);
    }

}
