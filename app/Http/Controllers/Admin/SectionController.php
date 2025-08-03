<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Lesson;
use App\Models\Chapter;
use App\Models\Subject;
use App\Models\Section;
use Str;

class SectionController extends Controller
{
    public function index(Request $request)
    {
        $query = Section::query();

        if ($search = $request->input('search')) {
            $query->where('content', 'like', '%' . $search . '%');
        }

        // Lấy danh sách chapter nếu đã chọn subject
        $chapters = collect();
        if ($subject_id = $request->input('subject_id')) {
            $query->whereHas('lesson.chapter.subject', function ($q) use ($subject_id) {
                $q->where('id', $subject_id);
            });
            $chapters = Chapter::where('subject_id', $subject_id)->get();
        }

        // Lấy danh sách lesson nếu đã chọn chapter
        $lessons = collect();
        if ($chapter_id = $request->input('chapter_id')) {
            $query->whereHas('lesson.chapter', function ($q) use ($chapter_id) {
                $q->where('id', $chapter_id);
            });
            $lessons = Lesson::where('chapter_id', $chapter_id)->get();
        }

        if ($lesson_id = $request->input('lesson_id')) {
            $query->whereHas('lesson', function ($q) use ($lesson_id) {
                $q->where('id', $lesson_id);
            });
        }

        $sections = $query->orderBy('id', 'desc')->paginate(10);

        return view('admin.sections.index', [
            'sections' => $sections,
            'subjects' => Subject::all(),
            'chapters' => $chapters,
            'lessons' => $lessons,
        ]);
    }

    public function create()
    {
        $subjects = Subject::all(); 
        return view('admin.sections.create', compact('subjects'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'lesson_id' => 'required|exists:lessons,id',
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
        ]);

        Section::create([
            'lesson_id' => $request->lesson_id,
            'title' => $request->title,
            'slug' => Str::slug($request->title),
            'content' => $request->content,
        ]);

        return redirect()->route('sections.index')->with('success', 'Section created successfully.');
    }

    public function edit(Section $section)
    {
        $subjects = Subject::all();
        $chapters = Chapter::where('subject_id', $section->lesson->chapter->subject_id)->get();
        $lessons = Lesson::where('chapter_id', $section->lesson->chapter_id)->get();

        return view('admin.sections.edit', compact('section', 'subjects', 'chapters', 'lessons'));
    }

    public function update(Request $request, Section $section)
    {
        $request->validate([
            'lesson_id' => 'required',
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
        ]);

        $section->update([
            'lesson_id' => $request->lesson_id,
            'title' => $request->title,
            'slug' => Str::slug($request->title),
            'content' => $request->content,
        ]);

        return redirect()->route('sections.index')->with('success', 'Section updated.');
    }

    // public function destroy(Section $section)
    // {
    //     $section->delete();
    //     return redirect()->route('sections.index')->with('success', 'Section deleted successfully.');
    // }
}
