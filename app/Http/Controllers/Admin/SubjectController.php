<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Subject;
use App\Models\View;
use App\Models\Post;
use App\Models\Collection;
use App\Models\Lesson;
use App\Models\Section;
use App\Models\Chapter;

use Str;

class SubjectController extends Controller
{
    public function admin()
    {
        $views = View::select('model_type', 'model_id')
            ->selectRaw('COUNT(*) as total_views')
            ->whereIn('model_type', ['Post', 'Collection', 'Section', 'Lesson'])
            ->groupBy('model_type', 'model_id')
            ->orderByDesc('total_views')
            ->paginate(20);

        // map để bổ sung title cho từng dòng
        $views->getCollection()->transform(function ($view) {
            $modelClass = $view->model_type;

            if ($modelClass === 'Section') {
                $section = Section::with('lesson.chapter.subject')->find($view->model_id);
                if ($section) {
                    $view->title = $section->title;
                }
            } elseif ($modelClass === 'Lesson') {
                $lesson = Lesson::with('chapter.subject')->find($view->model_id);
                if ($lesson) {
                    $view->title = $lesson->title;
                }
            } elseif ($modelClass === 'Collection') {
                $collection = Collection::find($view->model_id);
                if ($collection) {
                    $view->title = $collection->title;
                }
            } elseif ($modelClass === 'Post') {
                $post = Post::with('collection')->find($view->model_id);
                if ($post) {
                    $view->title = $post->title;
                }
            } 

            return $view;
        });

        return view('admin.index', compact('views'));
    }

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
