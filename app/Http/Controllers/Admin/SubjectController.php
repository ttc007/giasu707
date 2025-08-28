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
                    $view->url = route('show.section', [
                        'subject_slug' => $section->lesson->chapter->subject->slug,
                        'chapter_slug' => $section->lesson->chapter->slug,
                        'section_slug' => $section->slug,
                    ]);
                }
            } elseif ($modelClass === 'Lesson') {
                $lesson = Lesson::with('chapter.subject')->find($view->model_id);
                if ($lesson) {
                    $view->title = $lesson->title;
                    $view->url = route('show.lesson', [
                        'subject_slug' => $lesson->chapter->subject->slug,
                        'chapter_slug' => $lesson->chapter->slug,
                        'lesson_slug' => $lesson->slug,
                    ]);
                }
            } elseif ($modelClass === 'Collection') {
                $collection = Collection::find($view->model_id);
                if ($collection) {
                    $view->title = $collection->title;
                    $view->url = route('home.collection', [
                        'slug' => $collection->slug,
                    ]);
                }
            } elseif ($modelClass === 'Post') {
                $post = Post::with('collection')->find($view->model_id);
                if ($post) {
                    $view->title = $post->title;
                    $view->url = route('home.post.show', [
                        'slug' => $post->collection->slug,
                        'post_slug' => $post->slug,
                    ]);
                }
            } 
            // elseif ($modelClass === 'Chapter') {
            //     $chapter = Chapter::with('subject')->find($view->model_id);
            //     if ($chapter) {
            //         $view->title = $chapter->title;
            //         $view->url = route('show.chapter', [
            //             'subject_slug' => $chapter->subject->slug,
            //             'chapter_slug' => $chapter->slug,
            //         ]);
            //     }
            // } elseif ($modelClass === 'Subject') {
            //     $subject = Subject::find($view->model_id);
            //     if ($subject) {
            //         $view->title = $subject->name;
            //         $view->url = route('show.subject', [
            //             'subject_slug' => $subject->slug,
            //         ]);
            //     }
            // }

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
