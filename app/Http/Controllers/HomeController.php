<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Subject;
use App\Models\Question;
use App\Models\Exam;
use App\Models\Post;
use DB;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $ip = $request->ip();
        $model = 'Home';
        $twentyFourHoursAgo = now()->subHours(24);
        $id = 0;

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

        $featuredPosts = Post::with('collection')->latest()->take(6)->get();
        return view('index', compact('featuredPosts'));
    }

    public function priceTableWeb(Request $request) {
        $ip = $request->ip();
        $model = 'Banggia';
        $twentyFourHoursAgo = now()->subHours(24);
        $id = 0;

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

        return view('price.web');
    }

    public function thiThu(Request $request)
    {
        $ip = $request->ip();
        $model = 'Thithu';
        $twentyFourHoursAgo = now()->subHours(24);
        $id = 0;

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

        $subjects = Subject::all();
        return view('exams.setup', compact('subjects'));
    }

    public function startThiThu(Request $request)
    {
        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'mode' => 'required|in:random,ordered',
            'exam_id' => 'nullable|exists:exams,id',
        ]);

        $subject_id = $request->subject_id;
        $mode = $request->mode;
        $exam_id = $request->exam_id;

        // Ưu tiên nếu có exam_id
        if ($exam_id) {
            $exam = Exam::with('questions')->find($exam_id);
        }

        // Nếu không có hoặc không tìm được exam
        if (empty($exam)) {
            $query = Exam::where('subject_id', $subject_id);

            if ($mode === 'random') {
                $exam = $query->inRandomOrder()->first();
            } else {
                $exam = $query->orderBy('id')->first();
            }
        }

        // Nếu vẫn không có đề nào
        if (!$exam) {
            return redirect()->back()->withErrors(['exam_id' => 'Không tìm thấy đề thi phù hợp.']);
        }

        $questions = $exam->questions;
        // Phân loại câu hỏi
        $grouped = $questions->groupBy('type');

        return view('exams.start', [
            'multipleChoiceQuestions' => $grouped->get('multiple_choice', collect()),
            'trueFalseQuestions'      => $grouped->get('true_false', collect()),
            'fillBlankQuestions'      => $grouped->get('fill_blank', collect()),
            'exam' => $exam
        ]);
    }

}
