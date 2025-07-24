<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Chapter;
use App\Models\Question;
use App\Models\Subject;

class ChapterController extends Controller
{
    public function show($subject_slug, $chapter_slug)
    {
        $subject = Subject::where('slug', $subject_slug)->firstOrFail();

        $chapter = Chapter::where('slug', $chapter_slug)
            ->where('subject_id', $subject->id)
            ->with(['lessons.sections']) // nếu bạn muốn load nội dung
            ->firstOrFail();

        return view('chapters.show', compact('subject', 'chapter'));
    }

    public function review($subject_slug, $chapter_slug)
    {
        $subject = Subject::where('slug', $subject_slug)->firstOrFail();

        $chapter = Chapter::where('slug', $chapter_slug)
            ->where('subject_id', $subject->id)
            ->with('lessons.sections')
            ->firstOrFail();

        // Logic ôn tập
        return view('chapters.review', compact('subject', 'chapter'));
    }

}
