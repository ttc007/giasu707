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
        $registrationId = session('studentId');
        $id = $section->id;

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

        return view('sections.show', compact('subject', 'chapter', 'section', 'liked'));
    }

}
