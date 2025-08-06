<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Question;
use App\Models\Lesson;
use App\Models\Chapter;
use App\Models\Exam;

class QuestionController extends Controller
{
    public function getRandom($sectionId)
    {
        $question = Question::where('section_id', $sectionId)
            ->inRandomOrder()
            ->first();

        if (!$question) {
            return response()->json(['error' => 'Không có câu hỏi nào'], 404);
        }

        return response()->json([
            'id' => $question->id,
            'content' => $question->content,
            'solution' => $question->solution,
            'answer' => $question->answer, // nếu không muốn show sớm thì đừng gửi luôn
        ]);
    }

    public function getOrderedQuestion(Section $section, $number)
    {
        $question = $section->questions()->orderBy('id')->skip($number - 1)->first();

        if (!$question) {
            return response()->json(['error' => 'Không tìm thấy câu hỏi số ' . $number]);
        }

        return response()->json([
            'content' => $question->content,
            'answer' => $question->answer,
            'solution' => $question->solution,
        ]);
    }

    public function getRandomByLesson($lessonId)
    {
        $lesson = Lesson::with('sections.questions')->find($lessonId);

        if (!$lesson) {
            return response()->json(['error' => 'Bài học không tồn tại'], 404);
        }

        // Gom tất cả câu hỏi của các section lại
        $questions = $lesson->sections->flatMap(function ($section) {
            return $section->questions;
        });

        if ($questions->isEmpty()) {
            return response()->json(['error' => 'Không có câu hỏi nào'], 404);
        }

        $question = $questions->random();

        return response()->json([
            'id' => $question->id,
            'content' => $question->content,
            'solution' => $question->solution,
            'answer' => $question->answer,
        ]);
    }

    public function randomFromChapter($chapterId)
    {
        $chapter = Chapter::with('lessons.sections.questions')->findOrFail($chapterId);

        $questions = $chapter->lessons->flatMap(function ($lesson) {
            return $lesson->sections->flatMap->questions;
        });

        if ($questions->isEmpty()) {
            return response()->json(['error' => 'Không có câu hỏi nào'], 404);
        }

        $question = $questions->random();

        return response()->json([
            'id' => $question->id,
            'content' => $question->content,
            'solution' => $question->solution,
            'answer' => $question->answer,
        ]);
    }

    public function getExamsBySubject($id)
    {
        $exams = Exam::where('subject_id', $id)->get(['id', 'title']);
        return response()->json($exams);
    }

}   
