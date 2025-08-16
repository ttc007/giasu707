<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lesson;
use App\Models\Chapter;
use App\Models\Subject;
use DB;

class LessonController extends Controller
{
    public function show($subject_slug, $chapter_slug, $lesson_slug, Request $request)
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

        $ip = $request->ip();
        $model = 'Lesson';
        $twentyFourHoursAgo = now()->subHours(24);
        $id = $lesson->id;

        $existingView = DB::table('views')
            ->where('model_type', $model)
            ->where('model_id', $id)
            ->where('ip_address', $ip)
            ->first();

        if (!$existingView) {
            DB::table('views')->insert([
                'model_type' => $model,
                'model_id'   => $id,
                'ip_address' => $ip,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        } else {
            $lastUpdated = \Carbon\Carbon::parse($existingView->updated_at);
            if ($lastUpdated->lt($twentyFourHoursAgo)) {
                DB::table('views')
                    ->where('model_type', $model)
                    ->where('model_id', $id)
                    ->where('ip_address', $ip)
                    ->update(['updated_at' => now()]);
            }
        }

        $ip = $request->ip();
        $modelClass = 'Lesson';
        $model_id = $lesson->id;
        
        $liked = DB::table('favorites')
            ->where('ip_address', $ip)
            ->where('model_type', $modelClass)
            ->where('model_id', $model_id)
            ->exists();

        return view('lessons.show', compact('subject', 'chapter', 'lesson', 'liked'));
    }
}
