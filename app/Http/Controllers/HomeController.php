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
    public function index()
    {
        // 3 bài viết mới
        $latestPosts = Post::with('collection')
            ->latest()
            ->take(3)
            ->get();

        // 3 bài viết xem nhiều nhất
        $popularPosts = Post::with('collection')
            ->select('posts.*', DB::raw('COUNT(views.id) as views_count'))
            ->leftJoin('views', function ($join) {
                $join->on('views.model_id', '=', 'posts.id')
                     ->where('views.model_type', '=', 'Post');
            })
            ->groupBy('posts.id')
            ->orderByDesc('views_count')
            ->take(3)
            ->get();

        return view('index', compact('latestPosts', 'popularPosts'));
    }

    public function priceTableWeb(Request $request) {
        return view('price.web');
    }

    public function thiThu(Request $request)
    {
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
