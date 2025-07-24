<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Subject;
use Str;

class SubjectController extends Controller
{
    public function index()
    {
        $subjects = Subject::all();
        return view('admin.subjects.index', compact('subjects'));
    }

    public function create()
    {
        return view('admin.subjects.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255'
        ]);

        Subject::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name), // Tự tạo slug
        ]);
        return redirect()->route('subjects.index')->with('success', 'Thêm môn học thành công!');
    }


    // Hiển thị form chỉnh sửa
    public function edit($id)
    {
        $subject = Subject::findOrFail($id);
        return view('admin.subjects.edit', compact('subject'));
    }

    // Cập nhật dữ liệu sau khi chỉnh sửa
    public function update(Request $request, $id)
    {
        $subject = Subject::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:subjects,slug,' . $subject->id,
        ]);

        $subject->name = $request->name;
        $subject->slug = $request->slug ? Str::slug($request->slug) : Str::slug($request->name);
        $subject->save();

        return redirect()->route('subjects.index')->with('success', 'Cập nhật thành công!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
