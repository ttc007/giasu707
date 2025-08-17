<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Subject;
use App\Models\Chapter;
use DB;

class SubjectController extends Controller
{
    public function showSubject($subject_slug, Request $request)
    {
        $subject = Subject::where('slug', $subject_slug)->firstOrFail();
        $chapters = $subject->chapters()->withCount('lessons')->get();

        $ip = $request->ip();
        $model = 'Subject';
        $twentyFourHoursAgo = now()->subHours(24);
        $id = $subject->id;

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

        return view('subjects.show', compact('subject', 'chapters'));
    }
}
