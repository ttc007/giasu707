<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Exam;
use App\Models\Question;

class ExamQuestionController extends Controller
{
    public function destroy(Exam $exam, Question $question)
    {
        $exam->questions()->detach($question->id);

        return response()->json(['message' => 'Removed successfully']);
    }

}
