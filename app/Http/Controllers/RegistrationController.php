<?php

namespace App\Http\Controllers;

use App\Models\Registration;
use Illuminate\Http\Request;
use App\Models\Collection;
use DB;
use App\Models\Section;
use App\Models\Lesson;
use App\Models\Post;

class RegistrationController extends Controller
{
    public function index(Request $request)
    {
        // Lấy IP từ request
        $ip = $request->ip();

        // Kiểm tra xem đã có chưa
        $registration = Registration::where('ip_address', $ip)->first();

        // Nếu chưa có thì tạo mới
        if (!$registration) {
            $registration = Registration::create([
                'name'      => 'Chưa cập nhật', // để trống, sẽ cập nhật sau
                'email'     => 'Chưa cập nhật',
                'phone'     => 'Chưa cập nhật',
                'subject'   => 'Chưa cập nhật',
                'client_id' => uniqid('client_', true), // gen ID tạm
                'user_agent' => $request->userAgent(),
                'ip_address' => $ip,
            ]);
        }

        $recentViews = DB::table('views')
            ->where('ip_address', $ip)
            ->orderByDesc('created_at')
            ->limit(12)
            ->get()
            ->map(function($view) {
                $modelClass = $view->model_type;
                if ($modelClass == 'Section') {
                    $section = Section::with('lesson.chapter.subject')->find($view->model_id);
                    if (!$section) return null;

                    $url = route('show.section', [
                        'subject_slug' => $section->lesson->chapter->subject->slug,
                        'chapter_slug' => $section->lesson->chapter->slug,
                        'section_slug' => $section->slug,
                    ]);

                    return [
                        'id' => $section->id,
                        'title' => $section->title,
                        'slug' => $section->slug,
                        'image' => null,
                        'url' => $url,
                        'type' => 'Section',
                    ];
                } elseif ($modelClass == 'Lesson') {
                    $lesson = Lesson::with('chapter.subject')->find($view->model_id);
                    if (!$lesson) return null;

                    $url = route('show.lesson', [
                        'subject_slug' => $lesson->chapter->subject->slug,
                        'chapter_slug' => $lesson->chapter->slug,
                        'lesson_slug' => $lesson->slug,
                    ]);

                    return [
                        'id' => $lesson->id,
                        'title' => $lesson->title,
                        'slug' => $lesson->slug,
                        'image' => null,
                        'url' => $url,
                        'type' => 'Lesson',
                    ];
                } elseif ($modelClass == 'Collection') {
                    $collection = Collection::find($view->model_id);
                    if (!$collection) return null;

                    $url = route('home.collection', [
                        'slug' => $collection->slug,
                    ]);

                    return [
                        'id' => $collection->id,
                        'title' => $collection->title,
                        'slug' => $collection->slug,
                        'image' => $collection->image,
                        'url' => $url,
                        'type' => 'Collection',
                    ];
                } else {
                    $post = Post::with('collection')->find($view->model_id);
                    if (!$post) return null;

                    $url = route('home.post.show', [
                        'slug' => $post->collection->slug,
                        'post_slug' => $post->slug,
                    ]);

                    return [
                        'id' => $post->id,
                        'title' => $post->title,
                        'slug' => $post->slug,
                        'image' => $post->image,
                        'url' => $url,
                        'type' => 'Post',
                    ];
                }

            })
            ->filter();

        $favorites = DB::table('favorites')
            ->where('ip_address', $ip)
            ->orderByDesc('created_at')
            ->limit(12)
            ->get()
            ->map(function($view) {
                $modelClass = $view->model_type;
                if ($modelClass == 'Section') {
                    $section = Section::with('lesson.chapter.subject')->find($view->model_id);
                    if (!$section) return null;

                    $url = route('show.section', [
                        'subject_slug' => $section->lesson->chapter->subject->slug,
                        'chapter_slug' => $section->lesson->chapter->slug,
                        'section_slug' => $section->slug,
                    ]);

                    return [
                        'id' => $section->id,
                        'title' => $section->title,
                        'slug' => $section->slug,
                        'image' => null,
                        'url' => $url,
                        'type' => 'Section',
                    ];
                } elseif ($modelClass == 'Lesson') {
                    $lesson = Lesson::with('chapter.subject')->find($view->model_id);
                    if (!$lesson) return null;

                    $url = route('show.lesson', [
                        'subject_slug' => $lesson->chapter->subject->slug,
                        'chapter_slug' => $lesson->chapter->slug,
                        'lesson_slug' => $lesson->slug,
                    ]);

                    return [
                        'id' => $lesson->id,
                        'title' => $lesson->title,
                        'slug' => $lesson->slug,
                        'image' => null,
                        'url' => $url,
                        'type' => 'Lesson',
                    ];
                } elseif ($modelClass == 'Collection') {
                    $collection = Collection::find($view->model_id);
                    if (!$collection) return null;

                    $url = route('home.collection', [
                        'slug' => $collection->slug,
                    ]);

                    return [
                        'id' => $collection->id,
                        'title' => $collection->title,
                        'slug' => $collection->slug,
                        'image' => $collection->image,
                        'url' => $url,
                        'type' => 'Collection',
                    ];
                } else {
                    $post = Post::with('collection')->find($view->model_id);
                    if (!$post) return null;

                    $url = route('home.post.show', [
                        'slug' => $post->collection->slug,
                        'post_slug' => $post->slug,
                    ]);

                    return [
                        'id' => $post->id,
                        'title' => $post->title,
                        'slug' => $post->slug,
                        'image' => $post->image,
                        'url' => $url,
                        'type' => 'Post',
                    ];
                }

            })
            ->filter(); // bỏ null

        

        return view('registrations.index', compact('registration', 'recentViews', 'favorites'));
    }

    public function create(Request $request)
    {
        $registration = Registration::where('ip_address', $request->ip())->first();
        return view('registrations.create', compact('registration'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'name'    => 'required|string|max:255',
            'phone'   => 'required|string|max:20',
            'email'   => 'nullable|email|max:255',
            'subject' => 'required|string|max:100',
            'note'    => 'nullable|string',
        ]);

        $registration = Registration::where('ip_address', $request->ip())->first();

        $registration->update($request->only(['name', 'phone', 'email', 'subject', 'note']));

        return redirect()->route('registration.index')->with('success', 'Cập nhật thành công!');
    }

    public function like(Request $request, $model, $model_id)
    {
        $ip = $request->ip();
        $modelClass = ucfirst($model);
        // Kiểm tra tồn tại rồi mới insert
        $exists = DB::table('favorites')
            ->where('ip_address', $ip)
            ->where('model_type', $modelClass)
            ->where('model_id', $model_id)
            ->exists();

        if (!$exists) {
            DB::table('favorites')->insert([
                'ip_address' => $ip,
                'model_type' => $modelClass,
                'model_id' => $model_id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return response()->json(['success' => true]);
    }

    public function unlike(Request $request, $model, $model_id)
    {
        $ip = $request->ip();

        $modelClass = ucfirst($model);
        DB::table('favorites')
            ->where('ip_address', $ip)
            ->where('model_type', $modelClass)
            ->where('model_id', $model_id)
            ->delete();

        return response()->json(['success' => true]);
    }
}
