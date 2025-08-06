<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Question;
use App\Models\Lesson;
use App\Models\Chapter;
use App\Models\Exam;
use App\Models\Section;

class QuestionController extends Controller
{
    public function getRandom($type, $id, Request $request)
    {
        $excludeId = $request->input('exclude_id');
        $query = Question::query();

        // Trừ câu hỏi hiện tại nếu có
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        if ($type === 'section') {
            $query->where('section_id', $id);
        } elseif ($type === 'lesson') {
            $sectionIds = Section::where('lesson_id', $id)->pluck('id');
            $query->whereIn('section_id', $sectionIds);
        } elseif ($type === 'chapter') {
            $lessonIds = Lesson::where('chapter_id', $id)->pluck('id');
            $sectionIds = Section::whereIn('lesson_id', $lessonIds)->pluck('id');
            $query->whereIn('section_id', $sectionIds);
        } else {
            return response()->json(['error' => 'Loại không hợp lệ'], 400);
        }

        $question = $query->inRandomOrder()->first();

        if (!$question) {
            return response()->json(['error' => 'Không có câu hỏi nào'], 404);
        }

        return response()->json([
            'id' => $question->id,
            'content' => $question->content,
            'solution' => $question->solution,
            'answer' => $question->answer,
        ]);
    }

    public function getOrderedQuestion($type, $id, $number)
    {
        $query = Question::query();

        if ($type === 'section') {
            $query->where('section_id', $id);
        } elseif ($type === 'lesson') {
            $sectionIds = Section::where('lesson_id', $id)->pluck('id');
            $query->whereIn('section_id', $sectionIds);
        } elseif ($type === 'chapter') {
            $lessonIds = Lesson::where('chapter_id', $id)->pluck('id');
            $sectionIds = Section::whereIn('lesson_id', $lessonIds)->pluck('id');
            $query->whereIn('section_id', $sectionIds);
        } else {
            return response()->json(['error' => 'Loại không hợp lệ'], 400);
        }

        $question = $query->orderBy('id')->skip($number - 1)->first();

        if (!$question) {
            return response()->json(['error' => 'Không tìm thấy câu hỏi số ' . $number]);
        }

        return response()->json([
            'id' => $question->id,
            'content' => $question->content,
            'answer' => $question->answer,
            'solution' => $question->solution,
        ]);
    }


    public function getExamsBySubject($id)
    {
        $exams = Exam::where('subject_id', $id)->get(['id', 'title']);
        return response()->json($exams);
    }

}   
