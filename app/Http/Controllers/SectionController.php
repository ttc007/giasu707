<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Section;
use App\Models\Question;
use App\Models\Subject;
use App\Models\Chapter;
use DB;

class SectionController extends Controller
{
    public function show(Request $request, $subject_slug, $chapter_slug, $section_slug)
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
            ->withCount('questions')
            ->firstOrFail();

        $ip = $request->ip();
        $model = 'Section';
        $twentyFourHoursAgo = now()->subHours(24);

        $existingView = DB::table('views')
            ->where('model_type', $model)
            ->where('model_id', $section->id)
            ->where('ip_address', $ip)
            ->first();

        if (!$existingView) {
            DB::table('views')->insert([
                'model_type' => $model,
                'model_id'   => $section->id,
                'ip_address' => $ip,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        } else {
            $lastUpdated = \Carbon\Carbon::parse($existingView->updated_at);
            if ($lastUpdated->lt($twentyFourHoursAgo)) {
                DB::table('views')
                    ->where('model_type', $model)
                    ->where('model_id', $section->id)
                    ->where('ip_address', $ip)
                    ->update(['updated_at' => now()]);
            }
        }

        $ip = $request->ip();
        $modelClass = 'Section';
        $model_id = $section->id;
        
        $liked = DB::table('favorites')
            ->where('ip_address', $ip)
            ->where('model_type', $modelClass)
            ->where('model_id', $model_id)
            ->exists();

        return view('sections.show', compact('subject', 'chapter', 'section', 'liked'));
    }

}
