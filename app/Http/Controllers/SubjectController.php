<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Subject;
use App\Models\Chapter;

class SubjectController extends Controller
{
    public function showSubject($subject_slug)
    {
        $subject = Subject::where('slug', $subject_slug)->firstOrFail();
        $chapters = $subject->chapters()->withCount('lessons')->get();

        return view('subjects.show', compact('subject', 'chapters'));
    }
}
