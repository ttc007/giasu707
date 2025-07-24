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
        return view('admin.exams.edit', compact('exam', 'subjects'));
    }

    public function update(Request $request, Exam $exam)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'questions' => 'nullable|array'
        ]);

        $exam->update($data);
        $exam->questions()->sync($data['questions'] ?? []);

        return redirect()->route('exams.index')->with('success', 'Cập nhật đề thi thành công');
    }

    public function destroy(Exam $exam)
    {
        //
    }
}
