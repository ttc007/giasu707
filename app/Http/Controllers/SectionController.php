<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Section;
use App\Models\Question;
use App\Models\Subject;
use App\Models\Chapter;

class SectionController extends Controller
{
    public function show($subject_slug, $chapter_slug, $section_slug)
    {
        $subject = Subject::where('slug', $subject_slug)->firstOrFail();

        $chapter = Chapter::where('slug', $chapter_slug)
            ->where('subject_id', $subject->id)
            ->firstOrFail();

        $section = Section::where('slug', $section_slug)
            ->whereHas('lesson', function ($query) use ($chapter) {
                $query->where('chapter_id', $chapter->id);
            })
            ->with('lesson')
            ->firstOrFail();

        return view('sections.show', compact('subject', 'chapter', 'section'));
    }
}
