<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Lesson;
use App\Models\Chapter;
use App\Models\Subject;
use Str;

class LessonController extends Controller
{
    public function index()
    {
        $lessons = Lesson::with('chapter')->latest()->paginate(10);
        return view('admin.lessons.index', compact('lessons'));
    }

    public function create()
    {
        $subjects = Subject::all();
        $chapters = []; // hoặc để rỗng cũng được
        return view('admin.lessons.create', compact('subjects', 'chapters'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'chapter_id' => 'required|exists:chapters,id',
        ]);

        Lesson::create([
            'title' => $request->title,
            'slug' => Str::slug($request->title),
            'chapter_id' => $request->chapter_id,
            'summary' => $request->summary
        ]);

        return redirect()->route('lessons.index')->with('success', 'Lesson tạo thành công.');
    }

    public function edit(Lesson $lesson)
    {
        $subjects = \App\Models\Subject::all();
        $chapters = \App\Models\Chapter::where('subject_id', $lesson->chapter->subject_id)->get();
        return view('admin.lessons.edit', compact('lesson', 'chapters', 'subjects'));
    }

    public function update(Request $request, Lesson $lesson)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'chapter_id' => 'required|exists:chapters,id',
        ]);

        $lesson->update([
            'title' => $request->title,
            'slug' => Str::slug($request->title),
            'chapter_id' => $request->chapter_id,
            'summary' => $request->summary
        ]);

        return redirect()->route('lessons.index')->with('success', 'Lesson cập nhật thành công.');
    }

    // public function destroy(Lesson $lesson)
    // {
    //     $lesson->delete();
    //     return redirect()->route('lessons.index')->with('success', 'Lesson deleted successfully.');
    // }
}
