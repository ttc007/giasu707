<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Subject;
use App\Models\Chapter;
use Str;

class ChapterController extends Controller
{
    public function index()
    {
        $chapters = Chapter::with('subject')->latest()->get();
        return view('admin.chapters.index', compact('chapters'));
    }

    public function create()
    {
        $subjects = Subject::all();
        return view('admin.chapters.create', compact('subjects'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'subject_id' => 'required|exists:subjects,id',
        ]);

        Chapter::create([
            'title' => $request->name,
            'slug' => Str::slug($request->name),
            'subject_id' => $request->subject_id,
            'summary' => $request->summary
        ]);

        return redirect()->route('chapters.index')->with('success', 'Tạo chương thành công');
    }

    public function edit(Chapter $chapter)
    {
        $subjects = Subject::all();
        return view('admin.chapters.edit', compact('chapter', 'subjects'));
    }

    public function update(Request $request, Chapter $chapter)
    {
        $request->validate([
            'name' => 'required|string',
            'subject_id' => 'required|exists:subjects,id',
        ]);

        $chapter->update([
            'title' => $request->name,
            'slug' => Str::slug($request->name),
            'subject_id' => $request->subject_id,
            'summary' => $request->summary
        ]);

        return redirect()->route('chapters.index')->with('success', 'Cập nhật chương thành công');
    }

    // public function destroy(Chapter $chapter)
    // {
    //     $chapter->delete();
    //     return redirect()->route('chapters.index')->with('success', 'Đã xoá chương');
    // }
}
