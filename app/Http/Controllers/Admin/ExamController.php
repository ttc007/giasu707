<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Exam;
use App\Models\Question;
use App\Models\Lesson;
use App\Models\Chapter;
use App\Models\Subject;
use App\Models\Section;

class ExamController extends Controller
{
    public function index()
    {
        $exams = Exam::latest()->paginate(10);
        return view('admin.exams.index', compact('exams'));
    }

    public function create()
    {
        $subjects = Subject::all();
        return view('admin.exams.create', compact('subjects'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'subject_id' => 'nullable'
        ]);

        $exam = Exam::create($data);

        return redirect()->route('exams.index')->with('success', 'Tạo đề thi thành công');
    }

    public function edit(Exam $exam)
    {
        $subjects = Subject::all();
        $subject  = $exam->subject;

        // Lấy tất cả section thuộc subject của đề thi
        $sections = Section::with('lesson.chapter') // load đủ quan hệ
            ->whereHas('lesson.chapter', function ($q) use ($subject) {
                $q->where('subject_id', $subject->id);
            })
            ->get();

        return view('admin.exams.edit', compact('exam', 'subjects', 'sections'));
    }


    public function update(Request $request, Exam $exam)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'subject_id' => 'required|integer|exists:subjects,id',
            'mcq' => 'nullable|array',
            'truefalse' => 'nullable|array',
            'fillblank' => 'nullable|array',
        ]);

        $exam->update([
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'subject_id' => $data['subject_id'],
        ]);

        // Gom tất cả câu hỏi lại
        $allQuestions = collect($data['mcq'] ?? [])->map(function($q) {
            $q['type'] = 'multiple_choice';
            return $q;
        })
        ->merge(
            collect($data['truefalse'] ?? [])->map(function($q) {
                $q['type'] = 'true_false';
                return $q;
            })
        )
        ->merge(
            collect($data['fillblank'] ?? [])->map(function($q) {
                $q['type'] = 'fill_blank';
                return $q;
            })
        )
        ->all();

    $questionIds = [];

    foreach ($allQuestions as $q) {
        if (!empty($q['content'])) {
            if (!empty($q['id'])) {
                // Update nếu có id
                $question = Question::find($q['id']);
                if ($question) {
                    $question->update([
                        'content'   => $q['content'],
                        'answer'    => $q['answer'] ?? null,
                        'solution'  => $q['solution'] ?? null,
                        'section_id'=> $q['section_id'] ?? null,
                        'type'      => $q['type'], // đảm bảo lưu lại type
                    ]);
                    $questionIds[] = $question->id;
                }
            } else {
                // Create mới nếu không có id
                $question = Question::create([
                    'content'   => $q['content'],
                    'answer'    => $q['answer'] ?? null,
                    'solution'  => $q['solution'] ?? null,
                    'section_id'=> $q['section_id'] ?? null,
                    'type'      => $q['type'],
                ]);
                $questionIds[] = $question->id;
            }
        }
    }

    // cuối cùng sync lại
    $exam->questions()->sync($questionIds);


        // Đồng bộ lại quan hệ exam - questions
        $exam->questions()->sync($questionIds);


        return redirect()->route('exams.index')->with('success', 'Cập nhật đề thi thành công');
    }

    public function destroy(Exam $exam)
    {
        //
    }
}
