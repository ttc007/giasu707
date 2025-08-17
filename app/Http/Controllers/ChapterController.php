<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Chapter;
use App\Models\Question;
use App\Models\Subject;
use DB;

class ChapterController extends Controller
{
    public function show($subject_slug, $chapter_slug, Request $request)
    {
        $subject = Subject::where('slug', $subject_slug)->firstOrFail();

        $chapter = Chapter::where('slug', $chapter_slug)
        ->where('subject_id', $subject->id)
        ->with([
            'lessons.sections' => function ($query) {
                $query->withCount('questions');
            }
        ])
        ->firstOrFail();

        $ip = $request->ip();
        $model = 'Chapter';
        $twentyFourHoursAgo = now()->subHours(24);
        $id = $chapter->id;

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
                'user_agent' => $request->userAgent(),
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

        return view('chapters.show', compact('subject', 'chapter'));
    }

    public function review($subject_slug, $chapter_slug, Request $request)
    {
        $subject = Subject::where('slug', $subject_slug)->firstOrFail();

        $chapter = Chapter::where('slug', $chapter_slug)
            ->where('subject_id', $subject->id)
            ->with('lessons.sections')
            ->firstOrFail();

        $ip = $request->ip();
        $model = 'ChapterReview';
        $twentyFourHoursAgo = now()->subHours(24);
        $id = $chapter->id;

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
                'user_agent' => $request->userAgent(),
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

        // Logic ôn tập
        return view('chapters.review', compact('subject', 'chapter'));
    }

}
