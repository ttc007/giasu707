<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Question;
use App\Models\Lesson;
use App\Models\Chapter;
use App\Models\Subject;
use App\Models\Section;

class QuestionController extends Controller
{
    public function index(Request $request)
    {
        $query = Question::query();

        if ($search = $request->input('search')) {
            $query->where('content', 'like', '%' . $search . '%');
        }

        // Lấy danh sách chapter nếu đã chọn subject
        $chapters = collect();
        if ($subject_id = $request->input('subject_id')) {
            $query->whereHas('section.lesson.chapter.subject', function ($q) use ($subject_id) {
                $q->where('id', $subject_id);
            });
            $chapters = Chapter::where('subject_id', $subject_id)->get();
        }

        // Lấy danh sách lesson nếu đã chọn chapter
        $lessons = collect();
        if ($chapter_id = $request->input('chapter_id')) {
            $query->whereHas('section.lesson.chapter', function ($q) use ($chapter_id) {
                $q->where('id', $chapter_id);
            });
            $lessons = Lesson::where('chapter_id', $chapter_id)->get();
        }

        // Lấy danh sách section nếu đã chọn lesson
        $sections = collect();
        if ($lesson_id = $request->input('lesson_id')) {
            $query->whereHas('section.lesson', function ($q) use ($lesson_id) {
                $q->where('id', $lesson_id);
            });
            $sections = Section::where('lesson_id', $lesson_id)->get();
        }

        // Lọc theo section_id nếu có
        if ($section_id = $request->input('section_id')) {
            $query->where('section_id', $section_id);
        }

        if ($type = $request->input('type')) {
            $query->where('type', $type);
        }

        $questions = $query->orderBy('id', 'desc')->paginate(20);

        return view('admin.questions.index', [
            'questions' => $questions,
            'subjects' => Subject::all(),
            'chapters' => $chapters,
            'lessons' => $lessons,
            'sections' => $sections,
        ]);
    }

    public function create()
    {
        $subjects = Subject::all(); 
        return view('admin.questions.create', compact('subjects'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'section_id' => 'required|exists:sections,id',
            'type'       => 'required',
            'content'    => 'required',
            'solution'   => 'nullable',
            'answer'     => 'nullable',
        ]);

        $question = Question::create($request->all());
        $question->exams()->sync($request->input('exam_ids', []));

        return redirect()->route('questions.index')->with('success', 'Thêm câu hỏi thành công');
    }

    public function edit(Question $question)
    {
        $subjects = Subject::all();
        $chapters = Chapter::where('subject_id', $question->section->lesson->chapter->subject_id)->get();
        $lessons = Lesson::where('chapter_id', $question->section->lesson->chapter_id)->get();
        $sections = Section::where('lesson_id', $question->section->lesson_id)->get();

        $selectedExamIds = $question->exams()->pluck('exam_id')->toArray();

        return view('admin.questions.edit', compact('question', 'subjects', 'chapters', 'lessons', 'sections', 'selectedExamIds'));
    }

    public function update(Request $request, Question $question)
    {
        $request->validate([
            'section_id' => 'required|exists:sections,id',
            'type'       => 'required',
            'content'    => 'required',
            'solution'   => 'nullable',
            'answer'     => 'nullable',
        ]);

        $question->update($request->all());

        $question->exams()->sync($request->input('exam_ids', [])); // cập nhật đề

        return redirect()->route('questions.index')->with('success', 'Cập nhật câu hỏi thành công');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function updateOrder(Request $request)
    {
        foreach ($request->orders as $id => $order) {
            Question::where('id', $id)->update(['order' => $order]);
        }

        return redirect()->route('questions.index')->with('success', 'Cập nhật thứ tự thành công!');
    }

}
