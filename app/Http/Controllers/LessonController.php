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

        $ip             = $request->ip();
        $model          = 'Lesson';
        $id             = $lesson->id;
        $twentyFourHoursAgo = now()->subHours(24);
        $registrationId = session('studentId');

        // Điều kiện chung
        $query = DB::table('views')
            ->where('model_type', $model)
            ->where('model_id', $id);

        if ($registrationId) {
            $query->where('registration_id', $registrationId);
        } else {
            $query->where('ip_address', $ip);
        }

        $existingView = $query->first();

        if (!$existingView) {
            DB::table('views')->insert([
                'model_type'      => $model,
                'model_id'        => $id,
                'ip_address'      => $ip,
                'user_agent'      => $request->userAgent(),
                'registration_id' => $registrationId,
                'created_at'      => now(),
                'updated_at'      => now()
            ]);
        } else {
            $lastUpdated = \Carbon\Carbon::parse($existingView->updated_at);
            if ($lastUpdated->lt($twentyFourHoursAgo)) {
                $query->update(['updated_at' => now()]);
            }
        }

        $liked = DB::table('favorites')
            ->where('registration_id', $registrationId)
            ->where('model_type', $model)
            ->where('model_id', $id)
            ->exists();

        return view('lessons.show', compact('subject', 'chapter', 'lesson', 'liked'));
    }
}
