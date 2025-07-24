<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    protected $fillable = ['title', 'description', 'subject_id'];

    public function questions()
    {
        return $this->belongsToMany(Question::class)->withTimestamps();
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

}
