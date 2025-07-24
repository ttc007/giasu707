<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lesson;
use App\Models\Chapter;
use App\Models\Subject;

class LessonController extends Controller
{
    public function show($subject_slug, $chapter_slug, $lesson_slug)
    {
        // Lấy subject theo slug
        $subject = Subject::where('slug', $subject_slug)->firstOrFail();

        // Lấy chapter theo slug và subject_id
        $chapter = Chapter::where('slug', $chapter_slug)
                          ->where('subject_id', $subject->id)
                          ->firstOrFail();

        // Lấy lesson theo slug và chapter_id
        $lesson = Lesson::with('sections') // nếu cần hiện các section trong bài học
                        ->where('slug', $lesson_slug)
                        ->where('chapter_id', $chapter->id)
                        ->firstOrFail();

        return view('lessons.show', compact('subject', 'chapter', 'lesson'));
    }
}
